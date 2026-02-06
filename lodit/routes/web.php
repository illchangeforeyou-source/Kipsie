
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\home; 

// Login
Route::get('/login','App\Http\Controllers\home@login');
Route::post('/aksi_login','App\Http\Controllers\home@aksi_login');
Route::post('/logincheck', 'App\Http\Controllers\home@login_check');
Route::get('/logout', 'App\Http\Controllers\home@logout');
Route::get('/page', 'App\Http\Controllers\home@iea');

// Password Reset
Route::get('/forgot-password', 'App\Http\Controllers\home@forgotPassword');
Route::post('/send-reset-code', 'App\Http\Controllers\home@sendResetCode');
Route::get('/verify-reset-code', 'App\Http\Controllers\home@showVerifyCodeForm');
Route::post('/verify-reset-code', 'App\Http\Controllers\home@verifyResetCode');
Route::post('/resend-code', 'App\Http\Controllers\home@resendCode');
Route::post('/update-password', 'App\Http\Controllers\home@updatePassword');


// CRUD
Route::get('/dok','App\Http\Controllers\home@dok');
Route::get('/tambah', 'App\Http\Controllers\home@tambah');   
Route::post('/simpan', 'App\Http\Controllers\home@simpan');  
Route::get('/edit/{id}','App\Http\Controllers\home@edit');   
Route::post('/update/{id}','App\Http\Controllers\home@update');
Route::get('/delete/{id}','App\Http\Controllers\home@hapus');
Route::get('/users', 'App\Http\Controllers\home@users');
Route::get('/employeeData', 'App\Http\Controllers\home@employeeData');
Route::get('/addEmployee', 'App\Http\Controllers\home@addEmployee');
Route::post('/saveEmployee', 'App\Http\Controllers\home@saveEmployee');
Route::get('/delEmployee/{employeeid}', 'App\Http\Controllers\home@delEmployee');
Route::put('/EmployEdit/{employeeid}', 'App\Http\Controllers\home@EmployUpdate');
Route::post('/EmployUpdate/{employeeid}', 'App\Http\Controllers\home@EmployUpdate');
Route::get('/resetPassword/{userid}', 'App\Http\Controllers\home@resetPassword');
Route::post('/reservations','App\Http\Controllers\home@store')->name('reservations.store');

// Transactions
Route::get('/transactions', 'App\Http\Controllers\Home@indext')->name('transactions.index');
Route::post('/transactions', 'App\Http\Controllers\Home@storet')->name('transactions.store');
Route::get('/transactions/export/pdf', 'App\Http\Controllers\Home@exportPdf')->name('transactions.exportPdf');
Route::get('/transactions/export/excel', 'App\Http\Controllers\Home@exportExcel')->name('transactions.exportExcel');
Route::delete('/transactions/clear',  'App\Http\Controllers\Home@clearTransactions')->name('transactions.clear');
Route::delete('/transactions/{id}',  'App\Http\Controllers\Home@destroyTransaction')->name('transactions.destroy');
Route::get('/transactions/filter', 'App\Http\Controllers\Home@filterTransactions')->name('transactions.filter');

// Settings (Legacy)
Route::get('/settings', 'App\Http\Controllers\home@settings');
Route::post('/settings/update', 'App\Http\Controllers\home@updateSettings');

// Settings (New - Admin Panel)
Route::prefix('/settings')->middleware('auth.session')->group(function () {
    Route::get('/', 'App\Http\Controllers\SettingsController@index')->name('settings.index');
    Route::post('/update', 'App\Http\Controllers\SettingsController@updateSetting')->name('settings.update');
    Route::get('/notifications', 'App\Http\Controllers\SettingsController@notificationSettings')->name('settings.notifications');
    Route::post('/notifications/update', 'App\Http\Controllers\SettingsController@updateNotificationSettings')->name('settings.notifications.update');
    // Save per-user theme preference (logged-in users)
    Route::post('/user-theme', 'App\Http\Controllers\SettingsController@updateUserTheme')->name('settings.user-theme');
});

// Reports
Route::prefix('/reports')->middleware('auth.session')->group(function () {
    Route::get('/', 'App\Http\Controllers\ReportsController@dashboard')->name('reports.dashboard');
    Route::get('/monthly/{year}/{month}', 'App\Http\Controllers\ReportsController@monthlyReport')->name('reports.monthly');
    Route::get('/annual/{year}', 'App\Http\Controllers\ReportsController@annualReport')->name('reports.annual');
    Route::get('/export/monthly/{year}/{month}', 'App\Http\Controllers\ReportsController@exportMonthlyReport')->name('reports.export.monthly');
    Route::get('/export/annual/{year}', 'App\Http\Controllers\ReportsController@exportAnnualReport')->name('reports.export.annual');
    
    // API endpoints for reports
    Route::get('/api/monthly-sales/{year}/{month}', 'App\Http\Controllers\ReportsController@getMonthlySalesData');
    Route::get('/api/annual-sales/{year}', 'App\Http\Controllers\ReportsController@getAnnualData');
    Route::get('/api/five-year-trend', 'App\Http\Controllers\ReportsController@getFiveYearTrendData');
    Route::get('/api/top-medicines', 'App\Http\Controllers\ReportsController@getTopMedicines');
    Route::get('/api/recent-orders', 'App\Http\Controllers\ReportsController@getRecentOrders');
});

// User Permissions & Accessibility
Route::prefix('/permissions')->middleware('auth.session')->group(function () {
    Route::get('/manage', 'App\Http\Controllers\UserPermissionsController@managePermissions')->name('permissions.manage');
    Route::post('/update', 'App\Http\Controllers\UserPermissionsController@updatePermission')->name('permissions.update');
    
    // API endpoints for permissions
    Route::get('/api/all-users', 'App\Http\Controllers\UserPermissionsController@getAllUsersPermissions');
    Route::post('/api/bulk-update', 'App\Http\Controllers\UserPermissionsController@bulkUpdatePermissions');
    Route::post('/api/reset/{userId}', 'App\Http\Controllers\UserPermissionsController@resetUserPermissions');
    Route::get('/api/user/{userId}', 'App\Http\Controllers\UserPermissionsController@getUserPermissions');
    
    // Level-based permission endpoints
    Route::get('/api/level-permissions', 'App\Http\Controllers\UserPermissionsController@getLevelPermissions');
    Route::post('/api/save-level-permissions', 'App\Http\Controllers\UserPermissionsController@saveLevelPermissions');
    Route::post('/api/reset-level/{level}', 'App\Http\Controllers\UserPermissionsController@resetLevelPermissions');
});

// Notifications
Route::prefix('/notifications')->middleware('auth.session')->group(function () {
    Route::get('/list', 'App\Http\Controllers\NotificationController@list')->name('notifications.list');
    Route::post('/{id}/read', 'App\Http\Controllers\NotificationController@markAsRead')->name('notifications.read');
    Route::post('/read-all', 'App\Http\Controllers\NotificationController@markAllAsRead')->name('notifications.read-all');
    Route::delete('/{id}/delete', 'App\Http\Controllers\NotificationController@delete')->name('notifications.delete');
    // Dev helper to seed a test notification for the current user
    Route::post('/seed-test', 'App\Http\Controllers\NotificationController@seedTest')->name('notifications.seed-test');
});


// Register
Route::get('/register', 'App\Http\Controllers\home@register');
Route::post('/registersave','App\Http\Controllers\home@register_save');

// Extras
Route::get('/kli','App\Http\Controllers\home@kli');
Route::get('/report','App\Http\Controllers\home@report');

 
// med
Route::get('/yao','App\Http\Controllers\home@yao');
Route::post('/save-order', 'App\Http\Controllers\home@storem')->name('save.order');
Route::post('/send-receipt-email', 'App\Http\Controllers\home@sendReceiptEmailFromModal')->name('receipt.email');
Route::post('/send-receipt-whatsapp', 'App\Http\Controllers\home@sendReceiptWhatsAppFromModal')->name('receipt.whatsapp');
Route::get('/medtransactions', 'App\Http\Controllers\home@medtransactions');
Route::delete('/clearMedTransactions', 'App\Http\Controllers\home@clearMedTransactions');
Route::get('/exportMedPdf', 'App\Http\Controllers\home@exportMedPdf');
Route::get('/exportMedExcel', 'App\Http\Controllers\home@exportMedExcel');
Route::patch('/orders/{id}/status', [Home::class, 'updateStatus'])
    ->name('orders.updateStatus');

// Patient medicine purchase history (patient-level only)
Route::get('/medicine-history', 'App\\Http\\Controllers\\home@medicineHistory')->middleware('auth.session')->name('medicine.history');
Route::get('/api/order/{id}', 'App\\Http\\Controllers\\home@getOrder')->middleware('auth.session')->name('order.api');
Route::post('/send-receipt-email-history', 'App\\Http\\Controllers\\home@sendReceiptEmailHistory')->middleware('auth.session')->name('receipt.email.history');

// /
Route::get('/','App\Http\Controllers\home@wow');

// Auth
Route::get('/activate/{token}', [Home::class, 'activate']);

// Web-accessible API fallbacks for profile actions (session-based)
Route::get('/api/user-profile-session', [App\Http\Controllers\ProfileController::class, 'getUserProfileSession']);
Route::post('/api/update-profile-session', [App\Http\Controllers\ProfileController::class, 'updateProfileSession']);
Route::post('/api/update-profile-picture-session', [App\Http\Controllers\ProfileController::class, 'updateProfilePictureSession']);

// App Settings API
Route::get('/api/app-settings', [App\Http\Controllers\AppSettingsController::class, 'getSettings']);
Route::post('/api/update-app-settings', [App\Http\Controllers\AppSettingsController::class, 'updateSettings']);

// Med but like the buying one

Route::post('/storem', [Home::class, 'storem']);

Route::get('/medicines', [Home::class, 'getMedicines']);        // Fetch all medicines (JSON)
Route::get('/categories', [Home::class, 'getCategories']);      // Fetch all categories (JSON)
Route::post('/medicines/add', [Home::class, 'storeyao'])->name('medicines.add');
Route::put('/medicines/{id}', [Home::class, 'updateMedicine']); // Update existing
Route::delete('/medicines/{id}', [Home::class, 'deleteMedicine']); // Delete
Route::get('/stock-management', [Home::class, 'stockManagementPage'])->name('stock.management');
Route::post('/stock/update', [Home::class, 'updateStock'])->name('stock.update');
Route::get('/medicine-report', [Home::class, 'medicineReport'])->name('medicine.report');
Route::post('/transaction/{id}/delete', [Home::class, 'deleteTransaction'])->name('transaction.delete');
Route::get('/deleted-transactions', [Home::class, 'deletedTransactions'])->name('deleted.transactions');
Route::post('/transaction/{id}/restore', [Home::class, 'restoreTransaction'])->name('transaction.restore');
Route::post('/transaction/{id}/permanent-delete', [Home::class, 'permanentlyDeleteTransaction'])->name('transaction.permanent.delete');
// Data
Route::get('/user/{id}/access', 'App\Http\Controllers\home@IEAedit')->name('user.access.edit');
Route::put('/user/{id}/access', 'App\Http\Controllers\home@IEAupdate')->name('user.access.update');

// Admin Panel
Route::prefix('/admin')->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\AdminController@dashboard')->name('admin.dashboard');
    Route::post('/user/store', 'App\Http\Controllers\AdminController@storeUser')->name('admin.store-user');
    Route::post('/user/{id}/update', 'App\Http\Controllers\AdminController@updateUser')->name('admin.update-user');
    Route::post('/user/{id}/update-password', 'App\Http\Controllers\AdminController@updatePassword')->name('admin.update-password');
    Route::post('/user/{id}/delete', 'App\Http\Controllers\AdminController@deleteUser')->name('admin.delete-user');
    
    // Purchase History Report
    Route::get('/purchase-history', 'App\Http\Controllers\PurchaseHistoryController@index')->name('admin.purchase-history');
    
    // Payment Confirmations
    Route::get('/payment-confirmations', 'App\Http\Controllers\PaymentConfirmationController@pending')->name('admin.payment-confirmations');
    Route::post('/payment-confirmation/{id}/confirm', 'App\Http\Controllers\PaymentConfirmationController@confirm')->name('admin.confirm-payment');
    Route::post('/payment-confirmation/{id}/reject', 'App\Http\Controllers\PaymentConfirmationController@reject')->name('admin.reject-payment');
    
    // Pending Consultations
    Route::get('/pending-consultations', 'App\Http\Controllers\ConsultationController@pendingQuestions')->name('admin.pending-consultations');
    Route::post('/consultation/{id}/respond', 'App\Http\Controllers\ConsultationController@respondQuestion')->name('admin.respond-consultation');
});

// Cashier Panel
Route::prefix('/cashier')->middleware('auth.session')->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\CashierController@dashboard')->name('cashier.dashboard');
    Route::post('/payment/{id}/confirm', 'App\Http\Controllers\CashierController@confirmPayment')->name('cashier.confirm-payment');
    Route::post('/payment/{id}/reject', 'App\Http\Controllers\CashierController@rejectPayment')->name('cashier.reject-payment');
});

// Super Admin Panel
Route::prefix('/superadmin')->group(function () {
    Route::get('/pending-changes', 'App\Http\Controllers\SuperAdminController@pendingChanges')->name('superadmin.pending-changes');
    Route::post('/pending-changes/{id}/approve', 'App\Http\Controllers\SuperAdminController@approveChange')->name('superadmin.approve-change');
    Route::post('/pending-changes/{id}/reject', 'App\Http\Controllers\SuperAdminController@rejectChange')->name('superadmin.reject-change');
    Route::get('/audit-log', 'App\Http\Controllers\SuperAdminController@auditLog')->name('superadmin.audit-log');
    Route::get('/hidden-users', 'App\Http\Controllers\SuperAdminController@hiddenUsers')->name('superadmin.hidden-users');
    Route::post('/user/{id}/unhide', 'App\Http\Controllers\SuperAdminController@unhideUser')->name('superadmin.unhide-user');
    Route::post('/user/{id}/permanent-delete', 'App\Http\Controllers\SuperAdminController@permanentlyDeleteUser')->name('superadmin.permanent-delete-user');
    // Recently deleted medicines management
    Route::post('/medicine/{id}/restore', 'App\Http\Controllers\SuperAdminController@restoreMedicine')->name('superadmin.restore-medicine');
    Route::post('/medicine/{id}/permanent-delete', 'App\Http\Controllers\SuperAdminController@permanentlyDeleteMedicine')->name('superadmin.permanent-delete-medicine');
    // Import / Export CSV for users and medicines
    Route::get('/export/users', 'App\Http\Controllers\SuperAdminController@exportUsers')->name('superadmin.export-users');
    Route::post('/import/users', 'App\Http\Controllers\SuperAdminController@importUsers')->name('superadmin.import-users');
    Route::get('/export/medicines', 'App\Http\Controllers\SuperAdminController@exportMedicines')->name('superadmin.export-medicines');
    Route::post('/import/medicines', 'App\Http\Controllers\SuperAdminController@importMedicines')->name('superadmin.import-medicines');
    Route::get('/backup-reset', 'App\Http\Controllers\SuperAdminController@backupReset')->name('superadmin.backup-reset');
    Route::post('/backup-reset/backup', 'App\Http\Controllers\SuperAdminController@performBackup')->name('superadmin.perform-backup');
    Route::post('/backup-reset/reset', 'App\Http\Controllers\SuperAdminController@performReset')->name('superadmin.perform-reset');
});

// User Consultation Routes
Route::prefix('/consultation')->middleware('auth.session')->group(function () {
    Route::post('/ask', 'App\Http\Controllers\ConsultationController@askQuestion')->name('consultation.ask');
    Route::get('/my-questions', 'App\Http\Controllers\ConsultationController@myQuestions')->name('consultation.my-questions');
    Route::post('/{id}/close', 'App\Http\Controllers\ConsultationController@closeConsultation')->name('consultation.close');
});

// Payment Routes
Route::prefix('/payment')->middleware('auth.session')->group(function () {
    Route::post('/confirm-from-order/{orderId}', 'App\Http\Controllers\PaymentConfirmationController@createFromOrder')->name('payment.create-from-order');
    Route::get('/my-confirmations', 'App\Http\Controllers\PaymentConfirmationController@userConfirmations')->name('payment.my-confirmations');
});

// Delivery Routes
Route::prefix('/delivery')->group(function () {
    Route::get('/track/{orderId}', 'App\Http\Controllers\DeliveryController@trackOrder')->name('delivery.track');
    Route::get('/my-orders', 'App\Http\Controllers\DeliveryController@myDeliveries')->middleware('auth.session')->name('delivery.my-orders');
    Route::get('/all', 'App\Http\Controllers\DeliveryController@allDeliveries')->name('delivery.all');
    Route::post('/{orderId}/update-status', 'App\Http\Controllers\DeliveryController@updateDeliveryStatus')->name('delivery.update-status');
});

// Prescription Routes
Route::prefix('/prescription')->group(function () {
    Route::post('/upload', 'App\Http\Controllers\PrescriptionController@upload')->name('prescription.upload');
    Route::get('/pending', 'App\Http\Controllers\PrescriptionController@pending')->name('prescription.pending');
    Route::post('/{id}/approve', 'App\Http\Controllers\PrescriptionController@approve')->name('prescription.approve');
    Route::post('/{id}/reject', 'App\Http\Controllers\PrescriptionController@reject')->name('prescription.reject');
    Route::get('/my-prescriptions', 'App\Http\Controllers\PrescriptionController@myPrescriptions')->middleware('auth.session')->name('prescription.my-prescriptions');
    Route::get('/{id}/download', 'App\Http\Controllers\PrescriptionController@download')->name('prescription.download');
});

// User Manager Routes
Route::prefix('/manager')->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\UserManagerController@dashboard')->name('manager.dashboard');
    Route::get('/financial-report', 'App\Http\Controllers\UserManagerController@financialReport')->name('manager.financial-report');
    Route::get('/stock-report', 'App\Http\Controllers\UserManagerController@stockReport')->name('manager.stock-report');
    Route::post('/medicine/{id}/update-stock', 'App\Http\Controllers\UserManagerController@updateStock')->name('manager.update-stock');
});

// Stocker Routes
Route::prefix('/stocker')->group(function () {
    Route::get('/stock-management', 'App\Http\Controllers\StockerController@stockManagement')->name('stocker.stock-management');
    Route::post('/stock/add', 'App\Http\Controllers\StockerController@addStock')->name('stocker.add-stock');
    Route::post('/stock/set', 'App\Http\Controllers\StockerController@setStock')->name('stocker.set-stock');
});

// Receipt Routes (POS System)
Route::prefix('/receipt')->group(function () {
    Route::get('/generate/{orderId}', 'App\Http\Controllers\Home@generateReceipt')->name('receipt.generate');
    Route::get('/download/{orderId}', 'App\Http\Controllers\Home@downloadReceipt')->name('receipt.download');
    Route::post('/send-email', 'App\Http\Controllers\Home@sendReceiptEmail')->name('receipt.send-email');
    Route::post('/send-sms', 'App\Http\Controllers\Home@sendReceiptSMS')->name('receipt.send-sms');
});

// Age Verification Route
Route::post('/verify-age', 'App\Http\Controllers\Home@verifyAge')->name('verify-age');

Route::get('/permissions/api/current-user-permissions', 'App\Http\Controllers\PermissionApiController@getCurrentUserPermissions')->middleware('auth.session');
