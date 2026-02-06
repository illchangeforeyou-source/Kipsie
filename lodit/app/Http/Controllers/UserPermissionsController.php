<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserPermission;

class UserPermissionsController extends Controller
{
    public function managePermissions(Request $request)
    {
        if (!$request->session()->has('id') || !in_array($request->session()->get('level'), [4, 5])) {
            return redirect('/login')->with('error', 'Unauthorized');
        }

        return view('permissions.manage');
    }
    
    // API: Get all users and their permissions
    public function getAllUsersPermissions(Request $request)
    {
        if (!$request->session()->has('id') || !in_array($request->session()->get('level'), [4, 5])) {
            return response()->json(['success' => false], 403);
        }

        $users = DB::table('login')
            ->select('id', 'name', 'email', 'level')
            ->where('hidden', '!=', 1)
            ->orderBy('level', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $permissionCategories = [
            'Dashboard' => ['view_dashboard', 'view_analytics'],
            'Medicines' => ['view_medicines', 'create_medicine', 'edit_medicine', 'delete_medicine'],
            'Orders' => ['view_orders', 'edit_order_status', 'process_payment'],
            'Users' => ['view_users', 'manage_users', 'assign_roles', 'manage_permissions'],
            'Reports' => ['view_sales_report', 'view_stock_report', 'export_reports'],
            'Consultations' => ['view_consultations', 'answer_consultations'],
            'Deliveries' => ['view_deliveries', 'manage_delivery_status'],
            'Prescriptions' => ['view_prescriptions', 'validate_prescriptions'],
            'Database' => ['backup_database', 'reset_database', 'manage_database']
        ];

        // Flatten permissions list
        $allPermissions = [];
        foreach ($permissionCategories as $permissions) {
            $allPermissions = array_merge($allPermissions, $permissions);
        }

        // Get user permissions
        $permissions = DB::table('user_permissions')
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->groupBy('user_id');

        // Build permission map
        $permissionMap = [];
        foreach ($users as $user) {
            $permissionMap[$user->id] = [];
            
            foreach ($allPermissions as $perm) {
                $userPerm = $permissions[$user->id] ?? collect();
                $permissionMap[$user->id][$perm] = $userPerm->firstWhere('permission_key', $perm)?->can_access ?? false;
            }
        }

        return response()->json([
            'users' => $users,
            'permissions' => $permissionMap,
            'categories' => $permissionCategories
        ]);
    }

    public function updatePermission(Request $request)
    {
        if (!$request->session()->has('id') || $request->session()->get('level') != 4) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'user_id' => 'required|integer',
            'permission_key' => 'required|string',
            'can_access' => 'required|boolean'
        ]);

        UserPermission::updateOrCreate(
            [
                'user_id' => $request->input('user_id'),
                'permission_key' => $request->input('permission_key')
            ],
            [
                'can_access' => $request->input('can_access')
            ]
        );

        return response()->json(['success' => true]);
    }

    public function bulkUpdatePermissions(Request $request)
    {
        if (!$request->session()->has('id') || $request->session()->get('level') != 4) {
            return response()->json(['success' => false], 403);
        }

        $permissions = $request->input('permissions', []);

        foreach ($permissions as $userId => $userPerms) {
            // Clear existing permissions
            UserPermission::where('user_id', $userId)->delete();

            // Insert new permissions
            $records = [];
            foreach ($userPerms as $permKey => $canAccess) {
                $records[] = [
                    'user_id' => $userId,
                    'permission_key' => $permKey,
                    'can_access' => (bool)$canAccess,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            if (!empty($records)) {
                UserPermission::insert($records);
            }
        }

        return response()->json(['success' => true]);
    }

    public function resetUserPermissions(Request $request, $userId)
    {
        if (!$request->session()->has('id') || $request->session()->get('level') != 4) {
            return response()->json(['success' => false], 403);
        }

        UserPermission::where('user_id', $userId)->delete();

        return response()->json(['success' => true]);
    }

    public function getUserPermissions(Request $request, $userId)
    {
        if (!$request->session()->has('id') || $request->session()->get('level') != 4) {
            return response()->json(['success' => false], 403);
        }

        $permissions = UserPermission::where('user_id', $userId)
            ->get()
            ->keyBy('permission_key')
            ->mapWithKeys(fn($p) => [$p->permission_key => $p->can_access])
            ->toArray();

        return response()->json(['permissions' => $permissions]);
    }

    public function checkPermission($userId, $permissionKey)
    {
        $permission = UserPermission::where('user_id', $userId)
            ->where('permission_key', $permissionKey)
            ->first();

        return $permission ? $permission->can_access : false;
    }

    // New level-based permission methods
    public function getLevelPermissions(Request $request)
    {
        // Allow any authenticated user to fetch permissions for their level
        // (sidebar needs this to filter menu items)
        if (!$request->session()->has('id')) {
            return response()->json(['success' => false], 401);
        }

        $permissionCategories = [
            'Dashboard' => ['view_dashboard', 'view_analytics'],
            'Medicines' => ['view_medicines', 'create_medicine', 'edit_medicine', 'delete_medicine'],
            'Orders' => ['view_orders', 'edit_order_status', 'process_payment'],
            'Users' => ['view_users', 'manage_users', 'assign_roles', 'manage_permissions'],
            'Reports' => ['view_sales_report', 'view_stock_report', 'export_reports'],
            'Consultations' => ['view_consultations', 'answer_consultations'],
            'Deliveries' => ['view_deliveries', 'manage_delivery_status'],
            'Prescriptions' => ['view_prescriptions', 'validate_prescriptions'],
            'Database' => ['backup_database', 'reset_database', 'manage_database']
        ];

        // Flatten permissions list
        $allPermissions = [];
        foreach ($permissionCategories as $permissions) {
            $allPermissions = array_merge($allPermissions, $permissions);
        }

        // Get permissions for each level
        $levelPermissions = [];
        foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $level) {
            // Initialize every permission key to false for the level
            $levelPermissions[$level] = [];
            foreach ($allPermissions as $perm) {
                $levelPermissions[$level][$perm] = false;
            }

            // If there is at least one user with this level, prefer that user's explicit permissions
            $user = DB::table('login')->where('level', $level)->first();
            if ($user) {
                $userPerms = DB::table('user_permissions')
                    ->where('user_id', $user->id)
                    ->get()
                    ->keyBy('permission_key');

                foreach ($allPermissions as $perm) {
                    $levelPermissions[$level][$perm] = (bool)($userPerms[$perm]?->can_access ?? false);
                }
            }
        }

        return response()->json([
            'permissions' => $levelPermissions,
            'categories' => $permissionCategories
        ]);
    }

    public function saveLevelPermissions(Request $request)
    {
        if (!$request->session()->has('id') || !in_array($request->session()->get('level'), [4, 5])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $levelPermissions = $request->input('permissions', []);

            foreach ($levelPermissions as $level => $permissions) {
                // Get all users with this level
                $users = DB::table('login')->where('level', (int)$level)->pluck('id');

                foreach ($users as $userId) {
                    // Delete existing permissions
                    UserPermission::where('user_id', $userId)->delete();

                    // Insert new permissions
                    $records = [];
                    foreach ($permissions as $permKey => $canAccess) {
                        // Ensure boolean value
                        $value = $canAccess === true || $canAccess === 'true' || $canAccess === 1 || $canAccess === '1';
                        
                        $records[] = [
                            'user_id' => $userId,
                            'permission_key' => $permKey,
                            'can_access' => $value,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }

                    if (!empty($records)) {
                        UserPermission::insert($records);
                    }
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function resetLevelPermissions(Request $request, $level)
    {
        if (!$request->session()->has('id') || !in_array($request->session()->get('level'), [4, 5])) {
            return response()->json(['success' => false], 403);
        }

        // Get all users with this level and delete their permissions
        $users = DB::table('login')->where('level', $level)->pluck('id');
        UserPermission::whereIn('user_id', $users)->delete();

        return response()->json(['success' => true]);
    }
}
