<?php

// Site Routes
Route::get('/', 'Admin\DashboardController@index')->name('dashboard');

// Admin Panel Routes
Route::prefix('admin')->group(function () {

    Route::get('/login', 'Admin\Auth\LoginController@showLoginForm')->name('admin.login');
    Route::get('/form', 'Admin\Auth\LoginController@showGoogleForm')->name('admin.google_form');
    Route::get('/forgot_password', 'Admin\Auth\LoginController@forgetPassword')->name('admin.forgot_password');
    Route::post('/password/email', 'Admin\Auth\LoginController@sendResetLinkEmail')->name('admin.send_reset_mail');
    $this->get('password/reset/{token}', 'Admin\Auth\LoginController@showResetForm')->name('admin.show_reset_form');
    $this->post('password/reset', 'Admin\Auth\LoginController@resetPassword')->name('admin.reset_password');
    Route::get('/register', 'Auth\RegisterController@showAdminRegisterForm');
    Route::post('/preinfo', 'Admin\ClientDetailsController@getAjaxInfo')->name('admin.ajax_preinfo');
    Route::post('/update_docversion', 'Admin\ClientDetailsController@updateAjaxDocVersion')->name('admin.ajax_update_docversion');


    Route::post('/login', 'Admin\Auth\LoginController@login');
    Route::post('/register', 'Auth\RegisterController@createAdmin');
    Route::post('/logout', 'Admin\Auth\LoginController@logout')->name('admin.logout');
    Route::post('/client_details/savedata', 'Admin\ClientDetailsController@saveData')->name('client_details.savedata');

    Route::middleware(['admin.guard'])->group(function () {

        Route::get('/', 'Admin\DashboardController@index')->name('dashboard');
        Route::post('/dashboard/gettotal', 'Admin\DashboardController@getTotalValue')->name('dashboard.gettotal');

        Route::post('/dashboard/changeviewid', 'Admin\DashboardController@changeViewid')->name('dashboard.changeviewid');
        Route::post('/dashboard/changedate', 'Admin\DashboardController@changeDate')->name('dashboard.changedate');
        Route::post('/dashboard/changeallviewid', 'Admin\DashboardController@changeAllViewid')->name('dashboard.changeallviewid');

        Route::resource('/client_details', 'Admin\ClientDetailsController');


        Route::get('/client_details/show/{email}', 'Admin\ClientDetailsController@showInfo')->name('profile.client_details.show');
        Route::post('/client_details/user/{clientDetail}', 'Admin\ClientDetailsController@updateInfo')->name('profile.client_details.update');

        Route::resource('/admins', 'Admin\AdminsController');
        Route::get('/admins/profile/{id}', 'Admin\AdminsController@profileSetting')->name('admin.profile');

        Route::resource('/campaigns', 'Admin\CampaignController');
        Route::get('/campaigns/duplicate_page/{id}', 'Admin\CampaignController@showDuplicate')->name('campaigns.page_duplicate');
        Route::post('/campaigns/duplicate/{id}', 'Admin\CampaignController@duplicate')->name('campaigns.duplicate');
        Route::post('/campaigns/update', 'Admin\CampaignController@ajaxCampaignUpdate')->name('campaigns.ajax_update');

        Route::resource('/ads', 'Admin\AdsController');
        Route::post('/ads/mass_add/{id}', 'Admin\AdsController@massAdd')->name('ads.mass_add');
        Route::post('/ads/remove/{id}', 'Admin\AdsController@ajaxAdsRemove')->name('ads.remove');
        Route::post('/ads/update/{id}', 'Admin\AdsController@ajaxAdsUpdate')->name('ads.ajax_update');
        Route::post('/ads/sessiondate', 'Admin\AdsController@ajaxSetSessionDate')->name('ads.ajax_setsessiondate');
        Route::post('/ads/sessioncmpid', 'Admin\AdsController@ajaxSetSessionCampaignId')->name('ads.ajax_setsessioncmpid');
        Route::post('/ads/multi_img_upload', 'Admin\AdsController@ajaxMultiImageUpload')->name('ads.ajax_multi_img_upload');
        Route::post('/ads/save', 'Admin\AdsController@ajaxSaveAds')->name('ads.ajax_save_ads');

        Route::resource('/sheet', 'Admin\SheetController');
        Route::post('/sheet/gettable', 'Admin\SheetController@getTableData')->name('sheet.gettable');
        Route::post('/sheet/getsite', 'Admin\SheetController@getSiteData')->name('sheet.getsite');
        Route::post('/sheet/summery_report', 'Admin\SheetController@getSummeryReport')->name('sheet.summery_report');

        Route::post('/sheet/updatecampagin', 'Admin\SheetController@updateCampaign')->name('sheet.updatecampagin');
        Route::post('/sheet/setcmpmargin', 'Admin\SheetController@setMarginValue')->name('sheet.setcmpmargin');
        Route::post('/sheet/setcurrency', 'Admin\SheetController@setCurrencyValue')->name('sheet.setcurrency');
        Route::post('/sheet/getcurrency', 'Admin\SheetController@getCurrencyInfo')->name('sheet.getcurrency');
        Route::post('/sheet/sitechangestatus', 'Admin\SheetController@changeSiteStatus')->name('sheet.sitechangestatus');
        Route::post('/sheet/cmpdailychange', 'Admin\SheetController@changeCmpDailyValue')->name('sheet.setcmpdaily');
        Route::post('/sheet/cmpstrategychange', 'Admin\SheetController@changeCmpStrategyValue')->name('sheet.setcmpstrategy');
        Route::post('/sheet/siteaccountblock', 'Admin\SheetController@changeSiteAccountLevel')->name('sheet.sitechangeaccountlevel');



        Route::resource('/deposits', 'Admin\DepositController');
        Route::post('/deposits/get_all', 'Admin\DepositController@ajaxGetAllDeposits')->name('deposits.get_all');
        Route::post('/deposits/save_data', 'Admin\DepositController@ajaxSaveData')->name('deposits.save_data');
        Route::post('/deposits/edit_data', 'Admin\DepositController@ajaxEditData')->name('deposits.edit_data');
        Route::post('/deposits/remove/{deposit}', 'Admin\DepositController@ajaxDepositsRemove')->name('deposits.remove');
        Route::post('/deposits/sessionuserid', 'Admin\DepositController@ajaxSetSessionUserId')->name('deposits.ajax_setsessionuserid');


        Route::resource('/payments', 'Admin\PaymentHistoryController');
        Route::post('/payments/sessiondate', 'Admin\PaymentHistoryController@ajaxSetSessionDate')->name('payments.ajax_setsessiondate');
        Route::post('/payments/sessiondatetype', 'Admin\PaymentHistoryController@ajaxSetSessionDateType')->name('payments.ajax_setsessiondatetype');


        Route::resource('/reports', 'Admin\ReportsController');
        Route::get('/getanalysisjson', 'Admin\ReportsController@getAnalysisJson');
        Route::get('/site_getanalysisjson', 'Admin\ReportsController@getSiteAnalysisJson')->name('analysis.sitejson');

        Route::resource('/contents', 'Admin\ContentController');
        Route::post('/contents/sessionsitedata', 'Admin\ContentController@ajaxSetSessionSiteData')->name('content.ajax_setsessionsitedata');

        Route::resource('/title_analysis', 'Admin\TitleAnalysisController');
        Route::post('/get_title_score', 'Admin\TitleAnalysisController@analysis')->name('analysis.site_score');

    });



});