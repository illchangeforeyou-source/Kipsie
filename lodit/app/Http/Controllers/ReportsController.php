<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!$request->session()->has('id')) {
            return redirect('/login')->with('error', 'Please log in first.');
        }

        $year = $request->input('year', date('Y'));
        
        // Return the view which will load data via API
        return view('reports.dashboard');
    }

    
    // Deprecated: Use public getMonthlySalesData() instead

    
    // API Methods for Frontend
    
    public function getMonthlySalesData($year, $month = null)
    {
        try {
            $days = [];
            $dailySales = [];
            $totalSales = 0;
            $totalOrders = 0;

            if ($month) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($year, $month, $day)->format('Y-m-d');
                    $dayLabel = Carbon::create($year, $month, $day)->format('M d');
                    
                    $sales = DB::table('orders')
                        ->whereDate('created_at', $date)
                        ->sum('total');
                    
                    $days[] = $dayLabel;
                    $dailySales[] = (float)($sales ?? 0);
                    $totalSales += $sales ?? 0;
                }

                $totalOrders = DB::table('orders')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();
            } else {
                for ($m = 1; $m <= 12; $m++) {
                    $monthName = Carbon::create($year, $m)->format('M');
                    $sales = DB::table('orders')
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $m)
                        ->sum('total');
                    
                    $days[] = $monthName;
                    $dailySales[] = (float)($sales ?? 0);
                    $totalSales += $sales ?? 0;
                }

                $totalOrders = DB::table('orders')
                    ->whereYear('created_at', $year)
                    ->count();
            }

            return response()->json([
                'days' => $days,
                'daily_sales' => $dailySales,
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'avg_order' => $totalOrders > 0 ? $totalSales / $totalOrders : 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'days' => [],
                'daily_sales' => [],
                'total_sales' => 0,
                'total_orders' => 0,
                'avg_order' => 0
            ]);
        }
    }

    public function getAnnualData($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $months = [];
        $revenues = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($year, $month)->format('M');
            $revenue = DB::table('orders')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total');
            
            $months[] = $monthName;
            $revenues[] = (float)($revenue ?? 0);
        }

        return response()->json([
            'months' => $months,
            'revenue' => $revenues
        ]);
    }

    public function getFiveYearTrendData()
    {
        $years = [];
        $revenues = [];
        $orders = [];

        $currentYear = (int)date('Y');
        
        for ($i = 4; $i >= 0; $i--) {
            $year = $currentYear - $i;
            $revenue = DB::table('orders')
                ->whereYear('created_at', $year)
                ->sum('total');
            
            $orderCount = DB::table('orders')
                ->whereYear('created_at', $year)
                ->count();
            
            $years[] = (string)$year;
            $revenues[] = (float)($revenue ?? 0);
            $orders[] = (int)($orderCount ?? 0);
        }

        return response()->json([
            'years' => $years,
            'revenues' => $revenues,
            'orders' => $orders
        ]);
    }

    public function getTopMedicines()
    {
        try {
            // order_items table doesn't exist in current schema, return empty
            return response()->json([
                'medicines' => []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'medicines' => []
            ]);
        }
    }

    public function getRecentOrders()
    {
        try {
            // order_items table doesn't exist, return simple orders without item count
            $orders = DB::table('orders')
                ->select(
                    'orders.id',
                    'orders.customer_name',
                    'orders.total as total_price',
                    DB::raw('0 as item_count'),
                    'orders.created_at'
                )
                ->orderByDesc('orders.created_at')
                ->limit(15)
                ->get();

            return response()->json([
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'orders' => []
            ]);
        }
    }

    public function monthlyReport($year, $month)
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        $orders = Order::with('items.medicine')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return view('reports.monthly-report', [
            'orders' => $orders,
            'month' => $month,
            'year' => $year
        ]);
    }

    public function annualReport($year)
    {
        $orders = Order::with('items.medicine')
            ->whereYear('created_at', $year)
            ->get();

        return view('reports.annual-report', [
            'orders' => $orders,
            'year' => $year
        ]);
    }

    public function exportMonthlyReport(Request $request, $year, $month)
    {
        $format = $request->query('format', 'pdf');
        
        $orders = Order::with('items.medicine')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $totalSales = $orders->sum('total_price');
        
        if ($format === 'excel') {
            return $this->exportToExcel($orders, "Monthly_Report_{$year}_{$month}");
        }
        
        // Default to PDF
        return $this->exportToPdf($orders, "Monthly_Report_{$year}_{$month}");
    }

    public function exportAnnualReport(Request $request, $year)
    {
        $format = $request->query('format', 'pdf');
        
        $orders = Order::with('items.medicine')
            ->whereYear('created_at', $year)
            ->get();

        $totalSales = $orders->sum('total_price');
        
        if ($format === 'excel') {
            return $this->exportToExcel($orders, "Annual_Report_{$year}");
        }
        
        // Default to PDF
        return $this->exportToPdf($orders, "Annual_Report_{$year}");
    }

    private function exportToExcel($orders, $filename)
    {
        // For now, return a simple CSV which Excel can open
        $csv = "Order ID,Medicine,Quantity,Unit Price,Total,Date\n";
        
        foreach ($orders as $order) {
            foreach ($order->items ?? [] as $item) {
                $medicineName = $item->medicine->name ?? 'Unknown';
                $itemTotal = $item->quantity * $item->price;
                $csv .= "{$order->id},";
                $csv .= "{$medicineName},";
                $csv .= "{$item->quantity},";
                $csv .= "{$item->price},";
                $csv .= "{$itemTotal},";
                $csv .= "{$order->created_at->format('Y-m-d')}\n";
            }
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename={$filename}.csv");
    }

    private function exportToPdf($orders, $filename)
    {
        // Simple PDF export - generate HTML and return as downloadable
        $html = "<h1>Report: {$filename}</h1>";
        $html .= "<table border='1' style='width:100%; border-collapse:collapse;'>";
        $html .= "<tr><th>Order ID</th><th>Medicine</th><th>Qty</th><th>Price</th><th>Total</th><th>Date</th></tr>";
        
        foreach ($orders as $order) {
            foreach ($order->items ?? [] as $item) {
                $medicineName = $item->medicine->name ?? 'Unknown';
                $itemTotal = $item->quantity * $item->price;
                $html .= "<tr>";
                $html .= "<td>{$order->id}</td>";
                $html .= "<td>{$medicineName}</td>";
                $html .= "<td>{$item->quantity}</td>";
                $html .= "<td>$" . number_format($item->price, 2) . "</td>";
                $html .= "<td>$" . number_format($itemTotal, 2) . "</td>";
                $html .= "<td>{$order->created_at->format('Y-m-d')}</td>";
                $html .= "</tr>";
            }
        }
        
        $html .= "</table>";
        
        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename={$filename}.pdf");
    }
}

