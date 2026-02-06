<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Check authorization - admin and super admin only
        if (session('level') < 3) {
            abort(403, 'Unauthorized');
        }

        $query = Order::query();

        // Search by customer name or order ID
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('customer_name', 'like', "%$search%")
                  ->orWhere('id', 'like', "%$search%");
        }

        // Filter by status
        if ($request->has('filter_status') && $request->filter_status) {
            $query->where('status', $request->filter_status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by price range
        if ($request->has('price_from') && $request->price_from) {
            $query->where('total', '>=', $request->price_from);
        }

        if ($request->has('price_to') && $request->price_to) {
            $query->where('total', '<=', $request->price_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortColumns = ['id', 'customer_name', 'total', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $orders = $query->paginate(20);

        // Calculate statistics
        $totalSales = Order::sum('total');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        return view('admin.purchase-history', [
            'orders' => $orders,
            'searchTerm' => $request->search ?? '',
            'filterStatus' => $request->filter_status ?? '',
            'dateFrom' => $request->date_from ?? '',
            'dateTo' => $request->date_to ?? '',
            'priceFrom' => $request->price_from ?? '',
            'priceTo' => $request->price_to ?? '',
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
        ]);
    }
}
