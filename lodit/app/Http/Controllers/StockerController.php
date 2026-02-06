<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockerController extends Controller
{
    /**
     * Check if user is a stocker (level 8)
     */
    private function checkStocker(Request $request)
    {
        if (!$request->session()->has('id')) {
            return redirect('/login')->with('error', 'Please log in first.');
        }
        if ($request->session()->get('level') != 8) {
            return redirect('/kli')->with('error', 'Only stockers can access this page.');
        }
        return true;
    }

    /**
     * Display stock management page for stocker
     */
    public function stockManagement(Request $request)
    {
        $check = $this->checkStocker($request);
        if ($check !== true) return $check;

        $medicines = Medicine::orderBy('name')->get();

        return view('stocker.stock-management', [
            'medicines' => $medicines,
        ]);
    }

    /**
     * Add stock (increment)
     */
    public function addStock(Request $request)
    {
        $check = $this->checkStocker($request);
        if ($check !== true) return $check;

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);
        $oldStock = $medicine->stock;

        // Increment stock
        $medicine->increment('stock', $validated['quantity']);

        // Record transaction
        $costPerUnit = $medicine->price;
        $totalCost = $costPerUnit * $validated['quantity'];
        $currentBalance = DB::table('transactions')->sum('amount') ?? 0;
        $expense = -abs($totalCost); // Ensure it's negative (expense)

        DB::table('transactions')->insert([
            'type' => 'expense',
            'description' => "Stock added: {$medicine->name} - {$validated['quantity']} units by Stocker " . (session('id') ?? 'Unknown'),
            'medicine_id' => $medicine->id,
            'quantity' => $validated['quantity'],
            'category' => 'Stock Purchase',
            'date' => now()->format('Y-m-d'),
            'amount' => $expense,
            'balance' => $currentBalance + $expense,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log admin action
        $this->logStockAction('STOCK_ADD', $medicine->id, $oldStock, $medicine->stock, $validated['quantity']);

        // Send Discord and in-app notifications
        $notificationMessage = "📦 Stock Added: {$medicine->name} ({$validated['quantity']} units) added by Stocker";
        try {
            \App\Services\DiscordNotifier::notify(
                "📦 STOCK ADDED: {$medicine->name}\n" .
                "Quantity: +{$validated['quantity']}\n" .
                "Old Stock: {$oldStock} → New Stock: {$medicine->stock}\n" .
                "Stocker ID: " . (session('id') ?? 'Unknown')
            );
        } catch (\Exception $e) {}

        // Add in-app notification
        try {
            DB::table('notifications')->insert([
                'user_id' => session('id'),
                'title' => 'Stock Added',
                'message' => $notificationMessage,
                'type' => 'info',
                'read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Stock added successfully',
            'new_stock' => $medicine->stock,
        ]);
    }

    /**
     * Set stock (replace value)
     */
    public function setStock(Request $request)
    {
        $check = $this->checkStocker($request);
        if ($check !== true) return $check;

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);
        $oldStock = $medicine->stock;
        $quantityDifference = $validated['quantity'] - $oldStock;

        // Set stock
        $medicine->stock = $validated['quantity'];
        $medicine->save();

        // Record transaction if there's a change
        if ($quantityDifference != 0) {
            $costPerUnit = $medicine->price;
            $totalCost = $costPerUnit * abs($quantityDifference);
            $currentBalance = DB::table('transactions')->sum('amount') ?? 0;

            DB::table('transactions')->insert([
                'type' => 'expense',
                'description' => "Stock adjusted: {$medicine->name} - from {$oldStock} to {$validated['quantity']} by Stocker " . (session('id') ?? 'Unknown'),
                'medicine_id' => $medicine->id,
                'quantity' => abs($quantityDifference),
                'category' => 'Stock Adjustment',
                'date' => now()->format('Y-m-d'),
                'amount' => ($quantityDifference > 0 ? -$totalCost : $totalCost),
                'balance' => $currentBalance + ($quantityDifference > 0 ? -$totalCost : $totalCost),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Log admin action
        $this->logStockAction('STOCK_SET', $medicine->id, $oldStock, $medicine->stock, $quantityDifference);

        // Send Discord and in-app notifications
        try {
            $action = $quantityDifference > 0 ? '📦 STOCK INCREASED' : '📉 STOCK DECREASED';
            \App\Services\DiscordNotifier::notify(
                "{$action}: {$medicine->name}\n" .
                "Change: {$quantityDifference}\n" .
                "Old Stock: {$oldStock} → New Stock: {$medicine->stock}\n" .
                "Stocker ID: " . (session('id') ?? 'Unknown')
            );
        } catch (\Exception $e) {}

        // Add in-app notification
        try {
            $action = $quantityDifference > 0 ? 'increased' : 'decreased';
            DB::table('notifications')->insert([
                'user_id' => session('id'),
                'title' => 'Stock Adjusted',
                'message' => "Stock for {$medicine->name} {$action} from {$oldStock} to {$validated['quantity']} units",
                'type' => 'info',
                'read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'new_stock' => $medicine->stock,
        ]);
    }

    /**
     * Log stock action for audit trail
     */
    private function logStockAction($actionType, $medicineId, $oldStock, $newStock, $quantity)
    {
        try {
            DB::table('pending_admin_changes')->insert([
                'action_type' => $actionType,
                'target_user_id' => $medicineId,
                'admin_id' => session('id') ?? null,
                'old_data' => json_encode(['stock' => $oldStock]),
                'new_data' => json_encode(['stock' => $newStock]),
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Ignore if table doesn't exist or other errors
        }
    }
}
