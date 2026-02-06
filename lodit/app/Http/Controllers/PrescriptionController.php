<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Order;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrescriptionController extends Controller
{
    // User upload prescription for restricted medicine
    public function upload(Request $request)
    {
        if (!session('id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'medicine_id' => 'required|exists:medicines,id',
            'prescription_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Check authorization
        $order = Order::find($validated['order_id']);
        if ($order->user_id != session('id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if medicine requires prescription
        $medicine = Medicine::find($validated['medicine_id']);
        if (!$medicine) {
            return response()->json(['success' => false, 'message' => 'Medicine not found'], 404);
        }

        // Store file
        $filePath = $request->file('prescription_file')->store('prescriptions', 'public');

        $prescription = Prescription::create([
            'order_id' => $validated['order_id'],
            'user_id' => session('id'),
            'medicine_id' => $validated['medicine_id'],
            'file_path' => $filePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Prescription uploaded successfully! Awaiting pharmacist approval.',
            'prescription' => $prescription
        ]);
    }

    // Pharmacist view pending prescriptions
    public function pending(Request $request)
    {
        // Check if user is pharmacist (level 7) or super admin
        if (session('level') != 7 && session('level') < 4) {
            abort(403, 'Unauthorized');
        }

        $query = Prescription::where('status', 'pending')
            ->with('user', 'medicine', 'order')
            ->orderBy('created_at', 'asc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%$search%");
            })->orWhereHas('medicine', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        $prescriptions = $query->paginate(15);

        return view('admin.pending-prescriptions', [
            'prescriptions' => $prescriptions,
            'searchTerm' => $request->search ?? '',
        ]);
    }

    // Pharmacist approve prescription
    public function approve(Request $request, $id)
    {
        if (session('level') != 7 && session('level') < 4) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $prescription = Prescription::find($id);
        if (!$prescription) {
            return response()->json(['success' => false, 'message' => 'Prescription not found'], 404);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $prescription->update([
            'status' => 'approved',
            'pharmacist_id' => session('id'),
            'validated_at' => now(),
            'pharmacist_notes' => $validated['notes'] ?? null,
        ]);

        try {
            \App\Services\DiscordNotifier::notify("Prescription APPROVED: Medicine=" . ($prescription->medicine->name ?? 'Unknown') . " | Customer=" . ($prescription->user->username ?? 'Unknown') . " | Pharmacist=" . (session('id') ?? 'unknown'));
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Prescription approved!',
        ]);
    }

    // Pharmacist reject prescription
    public function reject(Request $request, $id)
    {
        if (session('level') != 7 && session('level') < 4) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $prescription = Prescription::find($id);
        if (!$prescription) {
            return response()->json(['success' => false, 'message' => 'Prescription not found'], 404);
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $prescription->update([
            'status' => 'rejected',
            'pharmacist_id' => session('id'),
            'validated_at' => now(),
            'pharmacist_notes' => $validated['notes'],
        ]);

        try {
            \App\Services\DiscordNotifier::notify("Prescription REJECTED: Medicine=" . ($prescription->medicine->name ?? 'Unknown') . " | Customer=" . ($prescription->user->username ?? 'Unknown') . " | Reason=" . substr($validated['notes'], 0, 100) . " | Pharmacist=" . (session('id') ?? 'unknown'));
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Prescription rejected.',
        ]);
    }

    // User view their prescriptions
    public function myPrescriptions()
    {
        if (!session('id')) {
            return redirect('/login');
        }

        $prescriptions = Prescription::where('user_id', session('id'))
            ->with('medicine', 'pharmacist', 'order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('prescription.my-prescriptions', [
            'prescriptions' => $prescriptions,
        ]);
    }

    // Download prescription file
    public function download($id)
    {
        $prescription = Prescription::find($id);
        if (!$prescription) {
            abort(404);
        }

        // Check authorization
        if (session('id') != $prescription->user_id && session('level') < 7) {
            abort(403);
        }

        return Storage::disk('public')->download($prescription->file_path);
    }
}
