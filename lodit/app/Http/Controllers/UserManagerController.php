<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Medicine;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagerController extends Controller
{
    // User Manager dashboard
    public function dashboard()
    {
        // Check authorization - User Manager (level 6) and above
        if (session('level') < 6) {
            abort(403, 'Unauthorized');
        }

        // Financial statistics
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $pendingRevenue = Order::where('status', 'pending')->sum('total');
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();

        // Stock statistics
        $totalMedicines = Medicine::count();
        $lowStockMedicines = Medicine::where('stock', '<', 10)->count();
        $outOfStock = Medicine::where('stock', 0)->count();

        // Recent transactions
        $recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();

        return view('manager.dashboard', [
            'totalRevenue' => $totalRevenue,
            'pendingRevenue' => $pendingRevenue,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'totalMedicines' => $totalMedicines,
            'lowStockMedicines' => $lowStockMedicines,
            'outOfStock' => $outOfStock,
            'recentOrders' => $recentOrders,
        ]);
    }

    // Financial report
    public function financialReport(Request $request)
    {
        if (session('level') < 6) {
            abort(403, 'Unauthorized');
        }

        $query = Order::query();

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->has('filter_status') && $request->filter_status) {
            $query->where('status', $request->filter_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate totals
        $totalRevenue = $query->sum('total');
        $completedRevenue = $query->where('status', 'completed')->sum('total');
        $pendingRevenue = $query->where('status', 'pending')->sum('total');

        // Daily revenue chart data
        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        return view('manager.financial-report', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'completedRevenue' => $completedRevenue,
            'pendingRevenue' => $pendingRevenue,
            'dateFrom' => $request->date_from ?? '',
            'dateTo' => $request->date_to ?? '',
            'filterStatus' => $request->filter_status ?? '',
            'dailyRevenue' => $dailyRevenue,
        ]);
    }

    // Stock report
    public function stockReport(Request $request)
    {
        if (session('level') < 6) {
            abort(403, 'Unauthorized');
        }

        $query = Medicine::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Stock filter
        if ($request->has('filter_stock') && $request->filter_stock) {
            switch ($request->filter_stock) {
                case 'low':
                    $query->where('stock', '<', 10)->where('stock', '>', 0);
                    break;
                case 'out':
                    $query->where('stock', 0);
                    break;
                case 'high':
                    $query->where('stock', '>=', 50);
                    break;
            }
        }

        $medicines = $query->orderBy('stock', 'asc')->paginate(20);

        // Statistics
        $totalMedicines = Medicine::count();
        $lowStockCount = Medicine::where('stock', '<', 10)->where('stock', '>', 0)->count();
        $outOfStockCount = Medicine::where('stock', 0)->count();
        $totalValue = Medicine::selectRaw('SUM(price * stock) as total')->first()->total ?? 0;

        return view('manager.stock-report', [
            'medicines' => $medicines,
            'totalMedicines' => $totalMedicines,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'totalValue' => $totalValue,
            'searchTerm' => $request->search ?? '',
            'filterStock' => $request->filter_stock ?? '',
        ]);
    }

    // Update medicine stock (manager can update)
    public function updateStock(Request $request, $medicineId)
    {
        if (session('level') < 6) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $medicine = Medicine::find($medicineId);
        if (!$medicine) {
            return response()->json(['success' => false, 'message' => 'Medicine not found'], 404);
        }

        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:200',
        ]);

        $oldStock = $medicine->stock;
        $medicine->stock = $validated['stock'];
        $medicine->save();

        // Log the change
        \Log::info('Stock updated', [
            'medicine_id' => $medicineId,
            'old_stock' => $oldStock,
            'new_stock' => $validated['stock'],
            'updated_by' => session('username'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully!',
        ]);
    }
}
