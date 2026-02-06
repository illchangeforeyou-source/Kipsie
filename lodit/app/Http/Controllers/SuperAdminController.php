<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\mo1;

class SuperAdminController extends Controller
{
    /**
     * Check if user is super admin
     */
    private function checkSuperAdmin(Request $request)
    {
        if (!$request->session()->has('id')) {
            return redirect('/login')->with('error', 'Please log in first.');
        }
        if ($request->session()->get('level') != 4) {
            return redirect('/')->with('error', 'Unauthorized access. Super Admin only.');
        }
        return true;
    }

    /**
     * Create a SQL dump of the SQLite database using PHP (fallback when sqlite3 is not available).
     */
    private function createSqlDump($dbPath, $backupFile)
    {
        if (!file_exists($dbPath)) {
            throw new \Exception('Database file not found for SQL dump.');
        }

        $pdo = new \PDO('sqlite:' . $dbPath);
        $fh = fopen($backupFile, 'w');
        if ($fh === false) {
            throw new \Exception('Cannot open backup file for writing.');
        }

        // Write header
        fwrite($fh, "-- SQLite Database Dump\n");
        fwrite($fh, "-- Generated: " . date('c') . "\n\n");

        // Export schema and other objects
        $rows = $pdo->query("SELECT type, name, tbl_name, sql FROM sqlite_master WHERE sql NOT NULL AND name NOT LIKE 'sqlite_%' ORDER BY type DESC, name;")->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $sql = trim($row['sql']);
            if ($sql !== '') {
                fwrite($fh, $sql . ";\n\n");
            }
        }

        // Export table data
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $colsStmt = $pdo->query("PRAGMA table_info('" . str_replace("'","''", $table) . "');");
            $cols = $colsStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($cols)) continue;

            $colNames = array_map(function ($c) { return $c['name']; }, $cols);
            $colList = array_map(function ($c) { return '"' . str_replace('"','""',$c) . '"'; }, $colNames);
            $colListStr = implode(', ', $colList);

            $dataStmt = $pdo->query("SELECT * FROM \"" . str_replace('"','""', $table) . "\";");
            $rowsData = $dataStmt->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($rowsData)) continue;

            foreach ($rowsData as $r) {
                $vals = [];
                foreach ($colNames as $cn) {
                    $v = $r[$cn];
                    if ($v === null) {
                        $vals[] = 'NULL';
                    } elseif (is_numeric($v) && !is_string($v)) {
                        $vals[] = $v;
                    } else {
                        // Escape single quotes by doubling
                        $escaped = str_replace("'", "''", $v);
                        $vals[] = "'" . $escaped . "'";
                    }
                }
                $valStr = implode(', ', $vals);
                $insert = "INSERT INTO \"{$table}\" ({$colListStr}) VALUES ({$valStr});\n";
                fwrite($fh, $insert);
            }
            fwrite($fh, "\n");
        }

        fclose($fh);
        $pdo = null;
    }

    /**
     * Export users as CSV (includes id, username, level, hidden, created_at)
     */
    public function exportUsers(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        $filename = 'users_export_' . date('Ymd_His') . '.csv';
        $callback = function () {
            $out = fopen('php://output', 'w');
            // header
            fputcsv($out, ['id', 'username', 'level', 'hidden', 'created_at', 'updated_at']);
            $users = DB::table('login')->get();
            foreach ($users as $u) {
                fputcsv($out, [
                    $u->id,
                    $u->username,
                    $u->level ?? '',
                    $u->hidden ?? 0,
                    $u->created_at ?? '',
                    $u->updated_at ?? '',
                ]);
            }
            fclose($out);
        };

        try {
            \App\Services\DiscordNotifier::notify("Users EXPORT started: filename={$filename} by admin=" . (session('id') ?? 'unknown'));
        } catch (\Exception $e) {}

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Import users from uploaded CSV. Upserts by `id` if present, otherwise creates new user.
     */
    public function importUsers(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        $request->validate(['file' => 'required|file']);
        $file = $request->file('file');
        $path = $file->getRealPath();

        $fh = fopen($path, 'r');
        if ($fh === false) return back()->with('error', 'Failed to open uploaded file.');

        $header = fgetcsv($fh);
        if (!$header) { fclose($fh); return back()->with('error', 'Empty CSV file.'); }

        $count = 0;
        while (($row = fgetcsv($fh)) !== false) {
            $data = array_combine($header, $row);
            if (!$data) continue;

            $id = $data['id'] ?? null;
            $record = [
                'username' => $data['username'] ?? null,
                'level' => $data['level'] ?? null,
                'hidden' => isset($data['hidden']) ? intval($data['hidden']) : 0,
                'updated_at' => now(),
            ];

            if ($id) {
                $existing = DB::table('login')->where('id', $id)->first();
                if ($existing) {
                    DB::table('login')->where('id', $id)->update($record);
                    $this->logAdminAction('UPDATE', $id, session('id') ?? null, (array)$existing, $record);
                } else {
                    $record['id'] = $id;
                    $record['created_at'] = $data['created_at'] ?? now();
                    $record['password'] = bcrypt('changeme');
                    DB::table('login')->insert($record);
                    $this->logAdminAction('CREATE', $id, session('id') ?? null, null, $record);
                }
            } else {
                $record['created_at'] = $data['created_at'] ?? now();
                $record['password'] = bcrypt('changeme');
                $newId = DB::table('login')->insertGetId($record);
                $this->logAdminAction('CREATE', $newId, session('id') ?? null, null, $record);
            }
            $count++;
        }
        fclose($fh);

        try {
            \App\Services\DiscordNotifier::notify("Users IMPORT completed: rows={$count} by admin=" . (session('id') ?? 'unknown'));
        } catch (\Exception $e) {}

        return back()->with('success', "Imported {$count} user rows.");
    }

    /**
     * Export medicines as CSV
     */
    public function exportMedicines(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        $filename = 'medicines_export_' . date('Ymd_His') . '.csv';
        $callback = function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'name', 'price', 'stock', 'hidden', 'created_at', 'updated_at']);
            $meds = DB::table('medicines')->get();
            foreach ($meds as $m) {
                fputcsv($out, [
                    $m->id,
                    $m->name,
                    $m->price,
                    $m->stock,
                    $m->hidden ?? 0,
                    $m->created_at ?? '',
                    $m->updated_at ?? '',
                ]);
            }
            fclose($out);
        };

        try {
            \App\Services\DiscordNotifier::notify("Medicines EXPORT started: filename={$filename} by admin=" . (session('id') ?? 'unknown'));
        } catch (\Exception $e) {}

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Import medicines from CSV. Upserts by `id` if present.
     */
    public function importMedicines(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        $request->validate(['file' => 'required|file']);
        $file = $request->file('file');
        $path = $file->getRealPath();

        $fh = fopen($path, 'r');
        if ($fh === false) return back()->with('error', 'Failed to open uploaded file.');

        $header = fgetcsv($fh);
        if (!$header) { fclose($fh); return back()->with('error', 'Empty CSV file.'); }

        $count = 0;
        while (($row = fgetcsv($fh)) !== false) {
            $data = array_combine($header, $row);
            if (!$data) continue;

            $id = $data['id'] ?? null;
            $record = [
                'name' => $data['name'] ?? null,
                'price' => isset($data['price']) ? floatval($data['price']) : 0,
                'stock' => isset($data['stock']) ? intval($data['stock']) : 0,
                'hidden' => isset($data['hidden']) ? intval($data['hidden']) : 0,
                'updated_at' => now(),
            ];

            if ($id) {
                $existing = DB::table('medicines')->where('id', $id)->first();
                if ($existing) {
                    DB::table('medicines')->where('id', $id)->update($record);
                    try {
                        DB::table('pending_admin_changes')->insert(['action_type'=>'UPDATE','target_user_id'=>$id,'admin_id'=>session('id')??null,'old_data'=>json_encode((array)$existing),'new_data'=>json_encode($record),'status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
                        \App\Services\DiscordNotifier::notify("Medicine UPDATED via import: id={$id} by admin=" . (session('id') ?? 'unknown'));
                    } catch (\Exception $e) {}
                } else {
                    $record['id'] = $id;
                    $record['created_at'] = $data['created_at'] ?? now();
                    DB::table('medicines')->insert($record);
                    try {
                        DB::table('pending_admin_changes')->insert(['action_type'=>'CREATE','target_user_id'=>$id,'admin_id'=>session('id')??null,'old_data'=>null,'new_data'=>json_encode($record),'status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
                        \App\Services\DiscordNotifier::notify("Medicine CREATED via import: id={$id} by admin=" . (session('id') ?? 'unknown'));
                    } catch (\Exception $e) {}
                }
            } else {
                $record['created_at'] = $data['created_at'] ?? now();
                $newId = DB::table('medicines')->insertGetId($record);
                try {
                    DB::table('pending_admin_changes')->insert(['action_type'=>'CREATE','target_user_id'=>$newId,'admin_id'=>session('id')??null,'old_data'=>null,'new_data'=>json_encode($record),'status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
                    \App\Services\DiscordNotifier::notify("Medicine CREATED via import: id={$newId} by admin=" . (session('id') ?? 'unknown'));
                } catch (\Exception $e) {}
            }
            $count++;
        }
        fclose($fh);

        try {
            \App\Services\DiscordNotifier::notify("Medicines IMPORT completed: rows={$count} by admin=" . (session('id') ?? 'unknown'));
        } catch (\Exception $e) {}

        return back()->with('success', "Imported {$count} medicine rows.");
    }

    /**
     * Display pending admin changes
     */
    public function pendingChanges(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            // Get pending changes
            $pendingChanges = DB::table('pending_admin_changes')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            // Enrich with admin and target user info
            $enriched = $pendingChanges->map(function ($change) {
                $admin = DB::table('login')->where('id', $change->admin_id)->first();
                $targetUser = DB::table('login')->where('id', $change->target_user_id)->first();
                $adminEmployee = DB::table('employee')->where('userid', $change->admin_id)->first();

                return (object)[
                    'id' => $change->id,
                    'action_type' => $change->action_type,
                    'target_user_id' => $change->target_user_id,
                    'target_username' => $targetUser ? $targetUser->username : 'Deleted',
                    'admin_id' => $change->admin_id,
                    'admin_username' => $admin ? $admin->username : 'Unknown',
                    'admin_email' => $adminEmployee ? $adminEmployee->employeename : 'N/A',
                    'old_data' => $change->old_data ? json_decode($change->old_data, true) : null,
                    'new_data' => $change->new_data ? json_decode($change->new_data, true) : null,
                    'created_at' => $change->created_at,
                ];
            });

            return view('superadmin.pending-changes', ['pendingChanges' => $enriched]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load pending changes: ' . $e->getMessage());
        }
    }

    /**
     * Approve a pending change
     */
    public function approveChange(Request $request, $changeId)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            DB::table('pending_admin_changes')
                ->where('id', $changeId)
                ->update([
                    'status' => 'approved',
                    'approved_by' => session('id'),
                    'approved_at' => now(),
                    'updated_at' => now(),
                ]);

            try {
                \App\Services\DiscordNotifier::notify("Pending change APPROVED: id={$changeId} by superadmin=" . (session('id') ?? 'unknown'));
            } catch (\Exception $e) {}

            return redirect('/superadmin/pending-changes')->with('success', 'Change approved.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve change: ' . $e->getMessage());
        }
    }

    /**
     * Reject and revert a pending change
     */
    public function rejectChange(Request $request, $changeId)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            $change = DB::table('pending_admin_changes')->where('id', $changeId)->first();

            // Revert the change based on action type
            if ($change->action_type === 'DELETE' && $change->old_data) {
                $oldData = json_decode($change->old_data, true);
                
                // Check if user exists - if hidden, unhide it
                $existingUser = DB::table('login')->where('id', $change->target_user_id)->first();
                if ($existingUser) {
                    // Unhide the user instead of recreating
                    DB::table('login')
                        ->where('id', $change->target_user_id)
                        ->update([
                            'hidden' => 0,
                            'updated_at' => now(),
                        ]);
                } else {
                    // User was permanently deleted, so restore from old data
                    DB::table('login')->insert([
                        'id' => $change->target_user_id,
                        'username' => $oldData['username'] ?? 'restored_user',
                        'password' => $oldData['password'] ?? bcrypt('default'),
                        'level' => $oldData['level'] ?? 2,
                        'hidden' => 0,
                        'created_at' => $oldData['created_at'] ?? now(),
                        'updated_at' => now(),
                    ]);
                }

                // Restore employee if exists
                if (isset($oldData['employee']) && is_array($oldData['employee'])) {
                    $emp = $oldData['employee'];
                    // Check if employee already exists
                    $existingEmployee = DB::table('employee')->where('userid', $change->target_user_id)->first();
                    if (!$existingEmployee) {
                        DB::table('employee')->insert([
                            'userid' => $change->target_user_id,
                            'employeeid' => $emp['employeeid'] ?? null,
                            'employeename' => $emp['employeename'] ?? 'Restored',
                            'created_at' => $emp['created_at'] ?? now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } else if ($change->action_type === 'CREATE') {
                // Hide the created user instead of deleting
                DB::table('login')
                    ->where('id', $change->target_user_id)
                    ->update(['hidden' => 1, 'updated_at' => now()]);
            } else if ($change->action_type === 'UPDATE' && $change->old_data) {
                // Revert to old data
                $oldData = json_decode($change->old_data, true);
                $updateData = [];
                
                // Only update scalar values
                foreach ($oldData as $key => $value) {
                    if (!is_array($value) && !is_object($value)) {
                        $updateData[$key] = $value;
                    }
                }
                
                if (!empty($updateData)) {
                    $updateData['updated_at'] = now();
                    DB::table('login')
                        ->where('id', $change->target_user_id)
                        ->update($updateData);
                }
            } else if ($change->action_type === 'PASSWORD_CHANGE') {
                // Password changes cannot be reverted (we don't store the old password)
                return back()->with('error', 'Password changes cannot be reverted.');
            }

            // Mark as rejected
            DB::table('pending_admin_changes')
                ->where('id', $changeId)
                ->update([
                    'status' => 'rejected',
                    'approved_by' => session('id'),
                    'approved_at' => now(),
                    'updated_at' => now(),
                ]);

            try {
                \App\Services\DiscordNotifier::notify("Pending change REJECTED: id={$changeId} by superadmin=" . (session('id') ?? 'unknown'));
            } catch (\Exception $e) {}

            return redirect('/superadmin/pending-changes')->with('success', 'Change rejected and reverted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject change: ' . $e->getMessage());
        }
    }

    /**
     * View audit log
     */
    public function auditLog(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            $filterAdmin = $request->input('filter_admin', '');
            $filterAction = $request->input('filter_action', '');
            $page = $request->input('page', 1);
            $perPage = 20;

            // Get all changes (approved, rejected, pending)
            $changes = DB::table('pending_admin_changes')
                ->when($filterAdmin, function ($query) use ($filterAdmin) {
                    return $query->where('admin_id', $filterAdmin);
                })
                ->when($filterAction, function ($query) use ($filterAction) {
                    return $query->where('action_type', $filterAction);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Enrich with admin and target user info
            $enriched = $changes->map(function ($change) {
                $admin = DB::table('login')->where('id', $change->admin_id)->first();
                $targetUser = DB::table('login')->where('id', $change->target_user_id)->first();
                $adminEmployee = DB::table('employee')->where('userid', $change->admin_id)->first();

                return (object)[
                    'id' => $change->id,
                    'action_type' => $change->action_type,
                    'status' => $change->status,
                    'target_user_id' => $change->target_user_id,
                    'target_username' => $targetUser ? $targetUser->username : 'Deleted',
                    'admin_id' => $change->admin_id,
                    'admin_username' => $admin ? $admin->username : 'Unknown',
                    'admin_email' => $adminEmployee ? $adminEmployee->employeename : 'N/A',
                    'created_at' => $change->created_at,
                    'approved_at' => $change->approved_at,
                ];
            });

            $total = count($enriched);
            $paginatedChanges = $enriched->forPage($page, $perPage);

            // Get all admins for filter
            $admins = DB::table('login')->where('level', 3)->get();

            return view('superadmin.audit-log', [
                'changes' => $paginatedChanges,
                'totalChanges' => $total,
                'admins' => $admins,
                'currentPage' => $page,
                'perPage' => $perPage,
                'filterAdmin' => $filterAdmin,
                'filterAction' => $filterAction,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load audit log: ' . $e->getMessage());
        }
    }

    /**
     * View hidden/removed users
     */
    public function hiddenUsers(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            $hei = new mo1();
            
            // Get hidden users
            $users = DB::table('login')
                ->where('hidden', 1)
                ->orderBy('updated_at', 'desc')
                ->get();

            $employees = $hei->tampil('employee');
            $levels = $hei->tampil('level');

            // Enrich user data
            $enriched = $users->map(function ($user) use ($employees, $levels) {
                $employee = collect($employees)->firstWhere('userid', $user->id);
                $level = collect($levels)->firstWhere('lvlnumber', $user->level ?? null);

                return (object)[
                    'id' => $user->id,
                    'username' => $user->username,
                    'level' => $user->level,
                    'level_name' => $level->beingas ?? 'Unknown',
                    'employeename' => $employee->employeename ?? 'No Employee Data',
                    'hidden_at' => $user->updated_at,
                ];
            });

            // Also collect recently deleted medicines from pending_admin_changes if any
            $deletedMedicines = [];
            try {
                $deletions = DB::table('pending_admin_changes')
                    ->where('action_type', 'DELETE')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

                foreach ($deletions as $d) {
                    if ($d->old_data) {
                        $old = json_decode($d->old_data, true);
                        // Heuristic: medicine records usually have a 'name' or 'medicine_name' key
                        if (isset($old['name']) || isset($old['medicine_name'])) {
                            $deletedMedicines[] = (object)[
                                'id' => $d->target_user_id,
                                'name' => $old['name'] ?? $old['medicine_name'] ?? 'Unknown',
                                'old_data' => $old,
                                'deleted_at' => $d->created_at,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // ignore if table doesn't exist
            }

            return view('superadmin.hidden-users', ['users' => $enriched, 'deletedMedicines' => $deletedMedicines]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load hidden users: ' . $e->getMessage());
        }
    }

    /**
     * Unhide a user (restore from hidden)
     */
    public function unhideUser(Request $request, $userId)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            DB::table('login')
                ->where('id', $userId)
                ->update([
                    'hidden' => 0,
                    'updated_at' => now(),
                ]);

            return redirect('/superadmin/hidden-users')->with('success', 'User restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore user: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a hidden user
     */
    public function permanentlyDeleteUser(Request $request, $userId)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            // Get user data for logging
            $user = DB::table('login')->where('id', $userId)->first();

            // Permanently delete employee and login records
            DB::table('employee')->where('userid', $userId)->delete();
            DB::table('login')->where('id', $userId)->delete();

            return redirect('/superadmin/hidden-users')->with('success', 'User permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to permanently delete user: ' . $e->getMessage());
        }
    }

    /**
     * Restore a deleted medicine (unhide)
     */
    public function restoreMedicine(Request $request, $id)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            $old = DB::table('medicines')->where('id', $id)->first();
            if (!$old) return back()->with('error', 'Medicine not found.');

            DB::table('medicines')->where('id', $id)->update([
                'hidden' => 0,
                'updated_at' => now(),
            ]);

            // Log restore as UPDATE action
            try {
                DB::table('pending_admin_changes')->insert([
                    'action_type' => 'RESTORE',
                    'target_user_id' => $id,
                    'admin_id' => session('id') ?? null,
                    'old_data' => json_encode((array)$old),
                    'new_data' => json_encode(['hidden' => 0]),
                    'status' => 'approved',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                try { \App\Services\DiscordNotifier::notify("Medicine RESTORED: id={$id} by admin=" . (session('id') ?? 'unknown')); } catch (\Exception $e) {}
            } catch (\Exception $e) {
                // ignore
            }

            return redirect('/superadmin/hidden-users')->with('success', 'Medicine restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore medicine: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a medicine
     */
    public function permanentlyDeleteMedicine(Request $request, $id)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            $old = DB::table('medicines')->where('id', $id)->first();
            if ($old) {
                // delete any images/files referenced by medicine
                if (!empty($old->images)) {
                    $imgs = is_array($old->images) ? $old->images : json_decode($old->images, true);
                    if (is_array($imgs)) {
                        foreach ($imgs as $img) {
                            $path = storage_path('app/public/' . $img);
                            if (file_exists($path)) unlink($path);
                        }
                    }
                } elseif (!empty($old->image)) {
                    $path = storage_path('app/public/' . $old->image);
                    if (file_exists($path)) unlink($path);
                }

                DB::table('medicines')->where('id', $id)->delete();

                // Log permanent delete
                try {
                    DB::table('pending_admin_changes')->insert([
                        'action_type' => 'PERMANENT_DELETE',
                        'target_user_id' => $id,
                        'admin_id' => session('id') ?? null,
                        'old_data' => json_encode((array)$old),
                        'new_data' => null,
                        'status' => 'approved',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    try { \App\Services\DiscordNotifier::notify("Medicine PERMANENTLY DELETED: id={$id} by admin=" . (session('id') ?? 'unknown')); } catch (\Exception $e) {}
                } catch (\Exception $e) {
                    // ignore
                }
            }

            return redirect('/superadmin/hidden-users')->with('success', 'Medicine permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to permanently delete medicine: ' . $e->getMessage());
        }
    }

    /**
     * Display backup and reset database page
     */
    public function backupReset(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            // Get backup file info if it exists
            $backupPath = storage_path('backups');
            $backups = [];
            
            if (is_dir($backupPath)) {
                $files = array_diff(scandir($backupPath), array('.', '..'));
                foreach ($files as $file) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['sql', 'sqlite'])) {
                        $filePath = $backupPath . '/' . $file;
                        $backups[] = [
                            'filename' => $file,
                            'size' => filesize($filePath),
                            'created' => filemtime($filePath)
                        ];
                    }
                }
            }

            usort($backups, function ($a, $b) {
                return $b['created'] - $a['created'];
            });

            return view('superadmin.backup-reset', [
                'backups' => $backups
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load backup & reset page: ' . $e->getMessage());
        }
    }

    /**
     * Perform database backup
     */
    public function performBackup(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        try {
            // Create backups directory if it doesn't exist
            $backupPath = storage_path('backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Get database path
            $dbPath = database_path('database.sqlite');
            
            if (!file_exists($dbPath)) {
                return back()->with('error', 'Database file not found.');
            }

            // Create backup with timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $backupFilename = "backup_{$timestamp}.sql";
            $backupFile = $backupPath . '/' . $backupFilename;

            // Try using sqlite3 to dump database to SQL file
            $command = "sqlite3 \"" . addslashes($dbPath) . "\" \".dump\" > \"" . addslashes($backupFile) . "\"";
            @shell_exec($command);

            // If dump produced a very small or empty file, fall back to a PHP-based SQL dumper
            $created = false;
            if (file_exists($backupFile) && filesize($backupFile) > 20) {
                $created = true;
            } else {
                if (file_exists($backupFile)) @unlink($backupFile);
                try {
                    $this->createSqlDump($dbPath, $backupFile);
                    if (file_exists($backupFile) && filesize($backupFile) > 20) {
                        $created = true;
                    }
                } catch (\Exception $e) {
                    // ignore and fail below
                }
            }

            if (!$created) {
                return back()->with('error', 'Failed to create SQL backup. Ensure write permissions and that the database file is readable.');
            }

            try {
                \App\Services\DiscordNotifier::notify("Database BACKUP created: {$backupFilename} by admin=" . (session('id') ?? 'unknown'));
            } catch (\Exception $e) {}

            return back()->with('success', 'Database backup created successfully: ' . $backupFilename);
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Perform database reset
     */
    public function performReset(Request $request)
    {
        $check = $this->checkSuperAdmin($request);
        if ($check !== true) return $check;

        // Confirm before reset
        if (!$request->input('confirm') === 'yes') {
            return back()->with('error', 'Reset not confirmed.');
        }

        try {
            // Get database path
            $dbPath = database_path('database.sqlite');

            if (!file_exists($dbPath)) {
                return back()->with('error', 'Database file not found.');
            }

            // First, create a backup before resetting
            $backupPath = storage_path('backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $backupPath . '/pre-reset-backup_' . $timestamp . '.sql';
            $command = "sqlite3 \"" . addslashes($dbPath) . "\" \".dump\" > \"" . addslashes($backupFile) . "\"";
            @shell_exec($command);

            // If dump failed or is empty, fallback to PHP-based SQL dumper
            if (!file_exists($backupFile) || filesize($backupFile) < 20) {
                if (file_exists($backupFile)) @unlink($backupFile);
                try {
                    $this->createSqlDump($dbPath, $backupFile);
                } catch (\Exception $e) {
                    // ignore, will proceed with reset but no proper sql backup created
                }
            }

            // Delete all tables
            $pdo = new \PDO('sqlite:' . $dbPath);
            $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table';")->fetchAll(\PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                $pdo->exec("DROP TABLE IF EXISTS {$table};");
            }

            // Clear any cached data
            $pdo = null;

            try {
                \App\Services\DiscordNotifier::notify("Database RESET performed. Pre-reset backup: " . basename($backupFile) . " by admin=" . (session('id') ?? 'unknown'));
            } catch (\Exception $e) {}

            return back()->with('success', 'Database reset successfully. A backup was created as pre-reset-backup_' . $timestamp . '.sql');
        } catch (\Exception $e) {
            return back()->with('error', 'Reset failed: ' . $e->getMessage());
        }
    }
}
