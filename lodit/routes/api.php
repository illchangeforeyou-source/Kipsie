<?php

use App\Http\Controllers\Home;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/medicines', [Home::class, 'index']);          // list all medicines
Route::get('/medicines-list', [Home::class, 'medicinesList']); // simple list for dropdowns
Route::post('/medicines', [Home::class, 'storeyao']);      // create a medicine
Route::put('/medicines/{id}', [Home::class, 'edityao']);   // edit a medicine
Route::delete('/medicines/{id}', [Home::class, 'delete']); // delete a medicine
Route::get('/transactions', [Home::class, 'getTransactions']); // get all transactions for API

// Profile Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/user-profile', [ProfileController::class, 'getUserProfile']);
    Route::post('/update-profile', [ProfileController::class, 'updateProfile']);
    Route::post('/update-profile-picture', [ProfileController::class, 'updateProfilePicture']);
});

// Session-based profile endpoint (fallback for session-auth apps)
Route::get('/user-profile-session', [ProfileController::class, 'getUserProfileSession']);

// Session-based update endpoints (fallback when app uses session('id') instead of Laravel Auth)
Route::post('/update-profile-session', [ProfileController::class, 'updateProfileSession']);
Route::post('/update-profile-picture-session', [ProfileController::class, 'updateProfilePictureSession']);

// Debug route: allow posting profile updates with explicit session_id (no CSRF)
Route::post('/debug/update-profile', [ProfileController::class, 'updateProfileSession']);

// App Settings Routes (Public for logo/name, Authenticated for updates)
Route::get('/app-settings', [AppSettingsController::class, 'getSettings']);

Route::middleware(['auth'])->group(function () {
    Route::post('/update-app-settings', [AppSettingsController::class, 'updateSettings']);
    Route::post('/update-account-settings', [AppSettingsController::class, 'updateAccountSettings']);
    Route::post('/update-employee-info', [AppSettingsController::class, 'updateEmployeeInfo']);
});
