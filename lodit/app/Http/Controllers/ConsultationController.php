<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Medicine;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    // User asking questions
    public function askQuestion(Request $request)
    {
        if (!session('id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $validated = $request->validate([
            'question' => 'required|string|min:5|max:1000',
            'medicine_id' => 'nullable|exists:medicines,id',
        ]);

        $consultation = Consultation::create([
            'user_id' => session('id'),
            'question' => $validated['question'],
            'medicine_id' => $validated['medicine_id'] ?? null,
            'status' => 'pending',
        ]);

        // Send notification to all pharmacists (level 6) and doctors (level 4+)
        $pharmacists = User::whereIn('level', [4, 5, 6])->get();
        foreach ($pharmacists as $pharmacist) {
            Notification::create([
                'user_id' => $pharmacist->id,
                'title' => 'New Consultation Question',
                'message' => 'A customer has asked a new question about ' . ($consultation->medicine?->name ?? 'general inquiry'),
                'type' => 'consultation',
                'read' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Question submitted successfully!',
            'consultation' => $consultation
        ]);
    }

    // Get all consultations (cross-level visibility)
    public function myQuestions(Request $request)
    {
        if (!session('id')) {
            return redirect('/login');
        }

        // Show all consultations to all users
        $consultations = Consultation::with('medicine', 'consultant', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('consultation.my-questions', [
            'consultations' => $consultations,
        ]);
    }

    // Pharmacist/Doctor view pending questions
    public function pendingQuestions(Request $request)
    {
        // Check if user is pharmacist/doctor (level 6) or super admin
        if (session('level') < 4 && session('level') != 6) {
            abort(403, 'Unauthorized');
        }

        $query = Consultation::where('status', 'pending')
            ->with('user', 'medicine')
            ->orderBy('created_at', 'asc');

        $consultations = $query->paginate(15);

        return view('admin.pending-consultations', [
            'consultations' => $consultations,
        ]);
    }

    // Pharmacist/Doctor respond to question
    public function respondQuestion(Request $request, $id)
    {
        \Log::info('respondQuestion called', [
            'consultation_id' => $id,
            'user_id' => session('id'),
            'user_level' => session('level'),
            'request_body' => $request->all()
        ]);

        if (!session('id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        // Check if user is pharmacist/doctor or super admin
        if (session('level') < 4 && session('level') != 6) {
            \Log::warning('Unauthorized respond attempt', ['level' => session('level')]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $consultation = Consultation::find($id);
        if (!$consultation) {
            \Log::warning('Consultation not found', ['id' => $id]);
            return response()->json(['success' => false, 'message' => 'Consultation not found'], 404);
        }

        try {
            $validated = $request->validate([
                'response' => 'required|string|min:5|max:2000',
            ]);

            $consultation->update([
                'response' => $validated['response'],
                'consultant_id' => session('id'),
                'status' => 'answered',
                'answered_at' => now(),
            ]);

            \Log::info('Consultation response saved', ['id' => $id, 'consultant_id' => session('id')]);

            return response()->json(['success' => true, 'message' => 'Response submitted successfully!']);
        } catch (\Exception $e) {
            \Log::error('Error saving consultation response', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Response submitted successfully!',
        ]);
    }

    // Close consultation
    public function closeConsultation($id)
    {
        $consultation = Consultation::find($id);
        if (!$consultation) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        // Check authorization
        if (session('id') != $consultation->user_id && session('level') < 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $consultation->update(['status' => 'closed']);

        return response()->json(['success' => true, 'message' => 'Consultation closed']);
    }
}
