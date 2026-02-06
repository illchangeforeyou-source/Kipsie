<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AppSetting;
use App\Models\UserPermission;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->session()->has('id') || $request->session()->get('level') != 4) {
            return redirect('/login')->with('error', 'Unauthorized');
        }

        $settings = AppSetting::all()->keyBy('setting_key');
        $settingsArray = [];

        foreach ($settings as $key => $setting) {
            $settingsArray[$key] = $setting->setting_value;
        }

        return view('settings.app-settings', ['settings' => $settingsArray, 'allSettings' => $settings]);
    }

    public function updateSetting(Request $request)
    {
        if (!$request->session()->has('id') || $request->session()->get('level') != 4) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'setting_key' => 'required|string',
            'setting_value' => 'nullable',
            'setting_type' => 'required|string'
        ]);

        $key = $request->input('setting_key');
        $value = $request->input('setting_value');
        $type = $request->input('setting_type');

        // Handle file uploads (like logo)
        if ($request->hasFile('logo') && $key === 'company_logo') {
            $file = $request->file('logo');
            $path = $file->store('logo', 'public');
            $value = $path;
        }

        AppSetting::set($key, $value, $type);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully'
        ]);
    }

    public function notificationSettings(Request $request)
    {
        if (!$request->session()->has('id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userId = $request->session()->get('id');

        return view('settings.notification-settings', ['userId' => $userId]);
    }

    public function updateNotificationSettings(Request $request)
    {
        if (!$request->session()->has('id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'notifications_enabled' => 'boolean',
            'notification_sound' => 'string'
        ]);

        $userId = $request->session()->get('id');

        \App\Models\UserPreference::updateOrCreate(
            ['user_id' => $userId],
            [
                'notifications_enabled' => $request->input('notifications_enabled', true),
                'notification_sound' => $request->input('notification_sound', 'default')
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated'
        ]);
    }

    /**
     * Update theme preference for the current user (AJAX)
     */
    public function updateUserTheme(Request $request)
    {
        if (!$request->session()->has('id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'theme' => 'required|string|in:light,dark'
        ]);

        $userId = $request->session()->get('id');

        \App\Models\UserPreference::updateOrCreate(
            ['user_id' => $userId],
            ['theme' => $request->input('theme')]
        );

        return response()->json(['success' => true]);
    }
}
