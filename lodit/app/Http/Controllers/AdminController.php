<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\mo1;

class AdminController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin(Request $request)
    {
        if (!$request->session()->has('id')) {
            return redirect('/login')->with('error', 'Please log in first.');
        }
        if ($request->session()->get('level') != 3) {
            return redirect('/')->with('error', 'Unauthorized access. Admin only.');
        }
        return true;
    }

    /**
     * Log an admin action to pending_changes table
     */
    private function logAdminAction($actionType, $targetUserId, $adminId, $oldData = null, $newData = null)
    {
        try {
            DB::table('pending_admin_changes')->insert([
                'action_type' => $actionType,
                'target_user_id' => $targetUserId,
                'admin_id' => $adminId,
                'old_data' => $oldData ? json_encode($oldData) : null,
                'new_data' => $newData ? json_encode($newData) : null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Notify Discord (best-effort)
            try {
                \App\Services\DiscordNotifier::notify("Admin action: {$actionType} by admin {$adminId} on target {$targetUserId}");
            } catch (\Exception $e) {
                // ignore notifier errors
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet, silently fail
        }
    }

    /**
     * Display admin dashboard with all users
     */
    public function dashboard(Request $request)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        $hei = new mo1();
        
        // Get sorting and filtering parameters
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchTerm = $request->input('search', '');
        $filterLevel = $request->input('filter_level', '');
        $page = $request->input('page', 1);
        $perPage = 10;

        // Fetch login table - EXCLUDE super admins (level should not be the highest level) and EXCLUDE hidden users
        $users = DB::table('login')
            ->where('level', '!=', 4) // Exclude super admin level (adjust if different)
            ->where('hidden', '!=', 1) // Exclude hidden users
            ->when($searchTerm, function ($query) use ($searchTerm) {
                return $query->where('username', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('id', 'LIKE', '%' . $searchTerm . '%');
            })
            ->when($filterLevel, function ($query) use ($filterLevel) {
                return $query->where('level', $filterLevel);
            })
            ->orderBy($sortBy, $sortOrder)
            ->get();

        $employees = $hei->tampil('employee');
        $levels = $hei->tampil('level');

        // Join data
        $joined = collect($users)->map(function ($user) use ($employees, $levels) {
            $employee = collect($employees)->firstWhere('userid', $user->id);
            $level = collect($levels)->firstWhere('lvlnumber', $user->level ?? null);

            return (object)[
                'id' => $user->id,
                'username' => $user->username,
                'password_hash' => $user->password,
                'level' => $user->level ?? 'Unknown',
                'level_name' => $level->beingas ?? 'User',
                'employeename' => $employee->employeename ?? 'No Employee Data',
                'employeeid' => $employee->employeeid ?? null,
                'created_at' => $user->created_at,
            ];
        });

        // Paginate manually
        $total = count($joined);
        $paginatedUsers = $joined->forPage($page, $perPage);

        echo view('header');
        echo view('admin.dashboard', [
            'users' => $paginatedUsers,
            'totalUsers' => $total,
            'levels' => $levels,
            'currentPage' => $page,
            'perPage' => $perPage,
            'searchTerm' => $searchTerm,
            'filterLevel' => $filterLevel,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
        echo view('footer');
        echo view('menu');
    }

    /**
     * Show edit user form
     */
    public function editUser(Request $request, $id)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        $hei = new mo1();
        $user = DB::table('login')->where('id', $id)->first();

        if (!$user) {
            return redirect('/admin/dashboard')->with('error', 'User not found.');
        }

        $levels = $hei->tampil('level');

        echo view('header');
        echo view('admin.edit-user', [
            'user' => $user,
            'levels' => $levels,
        ]);
        echo view('footer');
        echo view('menu');
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, $id)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        $request->validate([
            'username' => 'required|string|max:255|unique:login,username,' . $id,
            'level' => 'required|integer',
        ]);

        // Get old data
        $oldUser = DB::table('login')->where('id', $id)->first();

        $updated = DB::table('login')
            ->where('id', $id)
            ->update([
                'username' => $request->username,
                'level' => $request->level,
                'updated_at' => now(),
            ]);

        if ($updated) {
            // Log the action
            $this->logAdminAction('UPDATE', $id, session('id'), 
                ['username' => $oldUser->username, 'level' => $oldUser->level],
                ['username' => $request->username, 'level' => $request->level]
            );
            return redirect('/admin/dashboard')->with('success', 'User updated successfully.');
        }

        return back()->with('error', 'Failed to update user.');
    }

    /**
     * Show change password form
     */
    public function changePasswordForm(Request $request, $id)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        $user = DB::table('login')->where('id', $id)->first();

        if (!$user) {
            return redirect('/admin/dashboard')->with('error', 'User not found.');
        }

        echo view('header');
        echo view('admin.change-password', ['user' => $user]);
        echo view('footer');
        echo view('menu');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, $id)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $updated = DB::table('login')
            ->where('id', $id)
            ->update([
                'password' => Hash::make($request->password),
                'updated_at' => now(),
            ]);

        if ($updated) {
            // Log the action
            $this->logAdminAction('PASSWORD_CHANGE', $id, session('id'), null, ['password' => 'changed']);
            return redirect('/admin/dashboard')->with('success', 'Password updated successfully.');
        }

        return back()->with('error', 'Failed to update password.');
    }

    /**
     * Create a new user
     */
    public function storeUser(Request $request)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        $request->validate([
            'username' => 'required|string|max:255|unique:login,username',
            'password' => 'required|string|min:6|confirmed',
            'level' => 'required|integer',
        ]);

        try {
            $newUserId = DB::table('login')->insertGetId([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'level' => $request->level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log the action
            $this->logAdminAction('CREATE', $newUserId, session('id'), null, 
                ['username' => $request->username, 'level' => $request->level]
            );

            return redirect('/admin/dashboard')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Show delete confirmation form
     */
    public function deleteUserForm(Request $request, $id)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        $hei = new mo1();
        $user = DB::table('login')->where('id', $id)->first();

        if (!$user) {
            return redirect('/admin/dashboard')->with('error', 'User not found.');
        }

        $employee = DB::table('employee')->where('userid', $id)->first();

        echo view('header');
        echo view('admin.delete-user', [
            'user' => $user,
            'employee' => $employee,
        ]);
        echo view('footer');
        echo view('menu');
    }

    /**
     * Delete user (soft delete - hide from view)
     */
    public function deleteUser(Request $request, $id)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        // Prevent deleting yourself
        if (session('id') == $id) {
            return redirect('/admin/dashboard')->with('error', 'You cannot remove your own account.');
        }

        try {
            // Get user data before deletion
            $user = DB::table('login')->where('id', $id)->first();
            $employee = DB::table('employee')->where('userid', $id)->first();

            // Hide the user instead of actually deleting (soft delete)
            DB::table('login')->where('id', $id)->update([
                'hidden' => 1,
                'updated_at' => now(),
            ]);

            // Log the action
            $this->logAdminAction('DELETE', $id, session('id'), 
                ['username' => $user->username, 'level' => $user->level, 'employee' => $employee],
                null
            );

            return redirect('/admin/dashboard')->with('success', 'User removed successfully.');
        } catch (\Exception $e) {
            return redirect('/admin/dashboard')->with('error', 'Failed to remove user: ' . $e->getMessage());
        }
    }

    /**
     * Search users
     */
    public function search(Request $request)
    {
        $check = $this->checkAdmin($request);
        if ($check !== true) return $check;

        $searchTerm = $request->input('search', '');
        $hei = new mo1();

        $users = DB::table('login')
            ->where('username', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('id', 'LIKE', '%' . $searchTerm . '%')
            ->get();

        $employees = $hei->tampil('employee');
        $levels = $hei->tampil('level');

        $joined = collect($users)->map(function ($user) use ($employees, $levels) {
            $employee = collect($employees)->firstWhere('userid', $user->id);
            $level = collect($levels)->firstWhere('lvlnumber', $user->level ?? null);

            return (object)[
                'id' => $user->id,
                'username' => $user->username,
                'level' => $user->level ?? 'Unknown',
                'level_name' => $level->beingas ?? 'User',
                'employeename' => $employee->employeename ?? 'No Employee Data',
                'employeeid' => $employee->employeeid ?? null,
                'created_at' => $user->created_at,
            ];
        });

        echo view('header');
        echo view('admin.dashboard', [
            'users' => $joined,
            'totalUsers' => count($joined),
            'searchTerm' => $searchTerm,
        ]);
        echo view('footer');
        echo view('menu');
    }
}
