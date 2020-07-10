<?php

// Site Routes
Route::get('/', 'Admin\DashboardController@index')->name('dashboard');

// Admin Panel Routes
Route::prefix('admin')->group(function () {
	Route::get('/', 'Admin\DashboardController@index')->name('dashboard');
	Route::post('/dashboard/gettotal', 'Admin\DashboardController@getTotalValue')->name('dashboard.gettotal');

	Route::post('/dashboard/changeviewid', 'Admin\DashboardController@changeViewid')->name('dashboard.changeviewid');
	Route::post('/dashboard/changedate', 'Admin\DashboardController@changeDate')->name('dashboard.changedate');
	Route::post('/dashboard/changeallviewid', 'Admin\DashboardController@changeAllViewid')->name('dashboard.changeallviewid');
	
	Route::get('/login', 'Admin\Auth\LoginController@showLoginForm')->name('admin.login');
	Route::get('/register', 'Auth\RegisterController@showAdminRegisterForm');

	Route::post('/login', 'Admin\Auth\LoginController@login');
	Route::post('/register', 'Auth\RegisterController@createAdmin');
	Route::post('/logout', 'Admin\Auth\LoginController@logout')->name('admin.logout');

	Route::delete('/delete-multiple-users', 'Admin\UsersController@deleteMultiple')->name('users.multiple-delete');

	Route::resource('/admins', 'Admin\AdminsController');
	Route::resource('/sheet', 'Admin\SheetController');
	Route::post('/sheet/gettable', 'Admin\SheetController@getTableData')->name('sheet.gettable');
	Route::post('/sheet/getsite', 'Admin\SheetController@getSiteData')->name('sheet.getsite');
	Route::post('/sheet/updatecampagin', 'Admin\SheetController@updateCampaign')->name('sheet.updatecampagin');
	Route::post('/sheet/setcmpmargin', 'Admin\SheetController@setMarginValue')->name('sheet.setcmpmargin');
	Route::post('/sheet/setcurrency', 'Admin\SheetController@setCurrencyValue')->name('sheet.setcurrency');
	Route::post('/sheet/getcurrency', 'Admin\SheetController@getCurrencyInfo')->name('sheet.getcurrency');
	Route::post('/sheet/sitechangestatus', 'Admin\SheetController@changeSiteStatus')->name('sheet.sitechangestatus');
	Route::post('/sheet/cmpdailychange', 'Admin\SheetController@changeCmpDailyValue')->name('sheet.setcmpdaily');
	Route::post('/sheet/cmpstrategychange', 'Admin\SheetController@changeCmpStrategyValue')->name('sheet.setcmpstrategy');
	
	Route::resource('/reports', 'Admin\ReportsController');
	Route::get('/getanalysisjson', 'Admin\ReportsController@getAnalysisJson');
	
});