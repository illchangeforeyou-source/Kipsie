<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppSettingsController extends Controller
{
    /**
     * Get current app settings
     */
    public function getSettings()
    {
        try {
            // Return settings stored in session or use defaults
            $settings = [
                'logo' => session('app_logo', '/foto/baobei.jpg'),
                'app_name' => session('app_name', 'LODIT'),
                'theme' => session('theme', 'dark'),
            ];

            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not load settings',
                'settings' => [
                    'logo' => '/foto/baobei.jpg',
                    'app_name' => 'LODIT',
                    'theme' => 'dark'
                ]
            ], 200); // Return 200 with defaults even if error
        }
    }

    /**
     * Update app settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'app_name' => 'required|string|max:100',
                'theme' => 'required|in:light,dark',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120'
            ]);

            // Handle logo upload
            $logoPath = session('app_logo', '/foto/baobei.jpg');
            
            if ($request->hasFile('logo')) {
                try {
                    // Save the logo to public/foto directory
                    $file = $request->file('logo');
                    $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('foto'), $filename);
                    $logoPath = '/foto/' . $filename;
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload logo: ' . $e->getMessage()
                    ], 400);
                }
            }

            // Store settings in session (in production, use database)
            session([
                'app_name' => $validated['app_name'],
                'app_logo' => $logoPath,
                'theme' => $validated['theme']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'settings' => [
                    'app_name' => $validated['app_name'],
                    'logo' => $logoPath,
                    'theme' => $validated['theme']
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update account settings
     */
    public function updateAccountSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:100',
                'password' => 'nullable|string|min:6'
            ]);

            // Get current user from session or auth
            $userId = auth()->id() ?? session('user_id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // In a production app, update the user in database
            // For now, just return success
            session([
                'username' => $validated['username']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account settings updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating account settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update employee information
     */
    public function updateEmployeeInfo(Request $request)
    {
        try {
            $validated = $request->validate([
                'employeename' => 'nullable|string|max:100',
                'employeeage' => 'nullable|integer|min:0|max:150',
                'employeegender' => 'nullable|in:Male,Female,Other',
                'employeebirthdate' => 'nullable|date',
                'employeerace' => 'nullable|string|max:100',
                'employeereligion' => 'nullable|string|max:100',
                'employeebloodtype' => 'nullable|string|max:10',
                'employeeposition' => 'nullable|string|max:100'
            ]);

            // Get current user from session
            $userId = auth()->id() ?? session('user_id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // In a production app, update the user in database
            // For now, just store in session
            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    session([$key => $value]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee information updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating employee information: ' . $e->getMessage()
            ], 500);
        }
    }
}
