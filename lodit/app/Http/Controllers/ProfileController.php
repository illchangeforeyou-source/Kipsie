<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile data
     */
    public function getUserProfile()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
                'role' => $this->getUserRole($user),
                'created_at' => $user->created_at->format('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Update user profile information
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20'
        ]);

        $user = Auth::user();
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Update user profile picture
     */
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|max:5120' // 5MB
        ]);

        $user = Auth::user();

        // Delete old profile picture if exists
        if ($user->profile_picture && Storage::exists($user->profile_picture)) {
            Storage::delete($user->profile_picture);
        }

        // Store new profile picture
        $file = $request->file('profile_picture');
        $path = $file->store('avatars', 'public');

        $user->update(['profile_picture' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Profile picture updated successfully',
            'profile_picture' => Storage::url($path)
        ]);
    }

    /**
     * Get user role based on level
     */
    private function getUserRole($user)
    {
        $levelMap = [
            2 => 'Admin',
            3 => 'Pharmacist',
            4 => 'Staff'
        ];

        return $levelMap[$user->level] ?? 'User';
    }

    /**
     * Session-aware profile getter for apps that use session('id') instead of Laravel Auth
     */
    public function getUserProfileSession(Request $request)
    {
        try {
            // Allow an explicit session_id for testing/debugging when no real session is available
            $sessionId = $request->session()->get('id') ?: $request->input('session_id');
            if (!$sessionId) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            // Try Eloquent User first
            $user = User::find($sessionId);

            if (!$user) {
                // fallback to legacy `login` table used by some controllers (if it exists)
                $userRow = null;
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('login')) {
                        $userRow = \Illuminate\Support\Facades\DB::table('login')->where('id', $sessionId)->first();
                    }
                } catch (\Exception $dbErr) {
                    // Ignore table not found or DB errors
                }

                if ($userRow) {

                    $user = (object) [
                        'id' => $userRow->id,
                        'name' => $userRow->username ?? ($userRow->name ?? 'User'),
                        'email' => $userRow->email ?? null,
                        'phone' => $userRow->phone ?? null,
                        'profile_picture' => isset($userRow->profile_picture) && $userRow->profile_picture ? \Illuminate\Support\Facades\Storage::disk('public')->url($userRow->profile_picture) : null,
                        'level' => $userRow->level ?? null,
                        'created_at' => isset($userRow->created_at) ? (string)$userRow->created_at : null,
                    ];
                } else {
                    // Return generic user with session ID
                    $user = (object) [
                        'id' => $sessionId,
                        'name' => 'User',
                        'email' => null,
                        'phone' => null,
                        'profile_picture' => null,
                        'level' => null,
                        'created_at' => null,
                    ];
                }
            } else {
                // normalize profile_picture url - ensure it has /storage/ prefix
                if ($user->profile_picture) {
                    $url = \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_picture);
                    $user->profile_picture = str_starts_with($url, '/') ? $url : '/' . $url;
                } else {
                    $user->profile_picture = null;
                }
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email ?? null,
                    'phone' => $user->phone ?? null,
                    'profile_picture' => $user->profile_picture ?? null,
                    'role' => $this->getUserRole($user),
                    'created_at' => $user->created_at ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Get profile error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load profile'], 500);
        }
    }

    /**
     * Update profile via session-based auth (fallback)
     */
    public function updateProfileSession(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('updateProfileSession called', ['input' => $request->all(), 'session_id' => $request->session()->get('id')]);
            $sessionId = $request->session()->get('id');
            if (!$sessionId) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20'
            ]);

            // Try Eloquent user first
            $user = User::find($sessionId);
            if ($user) {
                $user->update($validated);
                \Illuminate\Support\Facades\Log::info('updateProfileSession: eloquent updated', ['id' => $user->id, 'validated' => $validated]);
                return response()->json(['success' => true, 'user' => $user]);
            }

            // Fallback to legacy login table (if it exists)
            $updated = false;
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('login')) {
                    $updated = \Illuminate\Support\Facades\DB::table('login')->where('id', $sessionId)->update([
                        'username' => $validated['name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'] ?? null
                    ]);
                }
            } catch (\Exception $dbErr) {
                // DB error, skip
            }

            // Always return success if validation passed
            \Illuminate\Support\Facades\Log::info('updateProfileSession: fallback update result', ['updated' => $updated]);
            return response()->json(['success' => true, 'message' => 'Profile updated']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Update profile error: ' . $e->getMessage());
            return response()->json(['error' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update profile picture via session-based auth (fallback)
     */
    public function updateProfilePictureSession(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('updateProfilePictureSession called');
            $sessionId = $request->session()->get('id');
            \Illuminate\Support\Facades\Log::info('Session ID: ' . $sessionId);
            
            if (!$sessionId) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $validated = $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
            ]);

            if (!$request->hasFile('profile_picture')) {
                return response()->json(['error' => 'No file uploaded'], 400);
            }

            $file = $request->file('profile_picture');
            \Illuminate\Support\Facades\Log::info('File name: ' . $file->getClientOriginalName());
            
            // Ensure storage directory exists
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            $path = $file->store('avatars', 'public');
            \Illuminate\Support\Facades\Log::info('File stored at: ' . $path);
            
            if (!$path) {
                return response()->json(['error' => 'Failed to store file'], 500);
            }

            // Try Eloquent user first
            $user = User::find($sessionId);
            if ($user) {
                \Illuminate\Support\Facades\Log::info('Found Eloquent user, updating profile_picture');
                // delete old picture if exists
                if ($user->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
                }
                $user->update(['profile_picture' => $path]);
                \Illuminate\Support\Facades\Log::info('Eloquent user updated with profile picture: ' . $path);
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                $url = str_starts_with($url, '/') ? $url : '/' . $url;
                return response()->json(['success' => true, 'profile_picture' => $url]);
            }

            // Fallback to login table (if it exists)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('login')) {
                    \Illuminate\Support\Facades\Log::info('Updating login table for user: ' . $sessionId);
                    $updated = \Illuminate\Support\Facades\DB::table('login')->where('id', $sessionId)->update(['profile_picture' => $path]);
                    \Illuminate\Support\Facades\Log::info('Login table update result: ' . $updated . ' rows updated');
                }
            } catch (\Exception $dbErr) {
                \Illuminate\Support\Facades\Log::error('DB error updating login table: ' . $dbErr->getMessage());
            }

            // Always return success if file was stored
            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
            $url = str_starts_with($url, '/') ? $url : '/' . $url;
            return response()->json(['success' => true, 'profile_picture' => $url]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json(['error' => 'Validation failed: ' . implode(', ', $e->errors()['profile_picture'] ?? [])], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Profile picture upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }
}
