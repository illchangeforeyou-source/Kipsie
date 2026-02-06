Route::get('/permissions/api/current-user-permissions', 'App\Http\Controllers\PermissionApiController@getCurrentUserPermissions')->middleware('auth.session');
