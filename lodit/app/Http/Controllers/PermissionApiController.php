<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserPermission;

class PermissionApiController extends Controller
{
    /**
     * Get current user's permissions
     * Used by frontend permission enforcer
     */
    public function getCurrentUserPermissions(Request $request)
    {
        if (!$request->session()->has('id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $userId = $request->session()->get('id');
        $userLevel = $request->session()->get('level');

        // Get all permission categories
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

        // Get user's permissions
        $userPerms = DB::table('user_permissions')
            ->where('user_id', $userId)
            ->get()
            ->keyBy('permission_key');

        // Build permission map for current user
        $permissions = [];
        foreach ($allPermissions as $perm) {
            $permissions[$perm] = (bool)($userPerms[$perm]?->can_access ?? false);
        }

        return response()->json([
            'success' => true,
            'userId' => $userId,
            'userLevel' => $userLevel,
            'permissions' => $permissions
        ]);
    }
}
