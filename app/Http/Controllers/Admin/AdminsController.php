<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Models\Admin;
use DLW\Models\ClientSetting;
use DLW\Models\ClientDetail;
use DLW\Models\Deposit;
use DLW\Models\Currency;

use DLW\Models\AcessHistory;
use DLW\Models\PasswordReset;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use DLW\Http\Requests\Admin\CreateAdminRequest;
use DLW\Http\Requests\Admin\UpdateAdminRequest;
use Auth;
use Illuminate\Support\Facades\Storage;

class AdminsController extends Controller
{
    public function __construct(){
        $this->middleware('admin.guard');
        $this->middleware('issuper');
    }
    
    public function index()
    {
        //$admins = Admin::where('id', '!=', Auth::guard('admin')->id())->get();
        $admins = Admin::orderBy('id', 'asc')->with('access_histories')->get();
        return view('admin.admins.index', ['title'=>'Account Management', 'admins'=>$admins]);
    }

    public function create()
    {
        return view('admin.admins.create', ['title'=>'Create Account']);
    }

    public function store(CreateadminRequest $request)
    {
        $admin = new Admin;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->view_id = $request->view_id;
        $admin->client_id = $request->client_id;
        $admin->client_secret = $request->client_secret;
        $admin->account_name = $request->account_name;
        $admin->password = bcrypt($request->password);

        if($request->avatar!=null){
            Storage::delete(Admin::find(Auth::guard('admin')->id())->avatar);
            $path = $request->file('avatar')->store('avatars');
            $admin->avatar = $path;
        }
        $admin->save();
        $user_id = $admin->id;

        Currency::query()->updateOrInsert(['admin_id' => $user_id], ['type' => 1, 'min_value' => $request->currency_min, 'max_value' => $request->currency_max, 'update_at' => date("Y-m-d H:i:s")]);

        $report_page = $request->report_page ?? 0;
        $campaign_page = $request->campaign_page ?? 0;
        $column_visibility = $request->column_visibility ?? 0;
        $currency_setting = $request->currency_setting ?? 0;
        $campaign_management_page = $request->campaign_management_page ?? 0;
        $ads_page = $request->ads_page ?? 0;
        $financial_setting = $request->financial_setting ?? 0;
        $payment_history = $request->payment_history ?? 0;
        $content_page = $request->content_page ?? 0;
        $utm_generator = $request->utm_generator ?? 0;



        $report_page = $report_page === 'on' ? 1 : 0;
        $campaign_page = $campaign_page === 'on' ? 1 : 0;
        $column_visibility = $column_visibility === 'on' ? 1 : 0;
        $currency_setting = $currency_setting === 'on' ? 1 : 0;
        $campaign_management_page = $campaign_management_page === 'on' ? 1 : 0;
        $ads_page = $ads_page === 'on' ? 1 : 0;
        $financial_setting = $financial_setting === 'on' ? 1 : 0;
        $payment_history = $payment_history === 'on' ? 1 : 0;
        $content_page = $content_page === 'on' ? 1 : 0;
        $utm_generator = $utm_generator === 'on' ? 1 : 0;


        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'report_page'], ['user_id' => $user_id, 'page_key' => 'report_page', 'show_rule' => $report_page, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'campaign_management_page'], ['user_id' => $user_id, 'page_key' => 'campaign_management_page', 'show_rule' => $campaign_management_page, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'ads_page'], ['user_id' => $user_id, 'page_key' => 'ads_page', 'show_rule' => $ads_page, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'campaign_page'], ['user_id' => $user_id, 'page_key' => 'campaign_page', 'show_rule' => $campaign_page, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'column_visibility'], ['user_id' => $user_id, 'page_key' => 'column_visibility', 'show_rule' => $column_visibility, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'currency_setting'], ['user_id' => $user_id, 'page_key' => 'currency_setting', 'show_rule' => $currency_setting, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'financial_setting'], ['user_id' => $user_id, 'page_key' => 'financial_setting', 'show_rule' => $financial_setting, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'payment_history'], ['user_id' => $user_id, 'page_key' => 'payment_history', 'show_rule' => $payment_history, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'content_page'], ['user_id' => $user_id, 'page_key' => 'content_page', 'show_rule' => $content_page, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'utm_generator'], ['user_id' => $user_id, 'page_key' => 'utm_generator', 'show_rule' => $utm_generator, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);

        return redirect()->route('admins.index');
    }

    public function show(Admin $admin)
    {
        //
    }

    public function edit(Admin $admin)
    {
        $title = 'Edit Account';
        $permissions = $admin->permissions()->pluck('show_rule', 'page_key');

        if(sizeof($permissions) === 0)
        {
            $report_page = false;
            $campaign_page = false;
            $column_visibility = false;
            $currency_setting = false;
            $campaign_management_page = false;
            $ads_page = false;
            $financial_setting = false;
            $payment_history = false;
            $content_page = false;
            $utm_generator = false;

        } else {
            $report_page = $permissions['report_page'] ?? 0=== 1 ? true : false;
            $campaign_page = $permissions['campaign_page'] ?? 0=== 1 ? true : false;
            $column_visibility = $permissions['column_visibility'] ?? 0=== 1 ? true : false;
            $currency_setting = $permissions['currency_setting'] ?? 0=== 1 ? true : false;
            $campaign_management_page = $permissions['campaign_management_page'] ?? 0=== 1 ? true : false;
            $ads_page = $permissions['ads_page'] ?? 0 === 1 ? true : false;
            $financial_setting = $permissions['financial_setting'] ?? 0=== 1 ? true : false;
            $payment_history = $permissions['payment_history'] ?? 0=== 1 ? true : false;
            $content_page = $permissions['content_page'] ?? 0=== 1 ? true : false;
            $utm_generator = $permissions['utm_generator'] ?? 0 === 1 ? true : false;
        }

        $currency_min = $admin->currencies[0]->min_value ?? 4.2;
        $currency_max = $admin->currencies[0]->max_value ?? 4.2;

        return view('admin.admins.edit', compact('title', 'admin', 'currency_min', 'currency_max', 'report_page', 'campaign_management_page', 'campaign_page', 'column_visibility', 'currency_setting', 'ads_page', 'financial_setting', 'payment_history', 'content_page', 'utm_generator'));
    }

    public function profileSetting($userid)
    {
        $title = __('globals.common.my_account');
        $admin = Admin::find($userid);
        $permissions = $admin->permissions()->pluck('show_rule', 'page_key');

        if(sizeof($permissions) === 0)
        {
            $report_page = false;
            $campaign_page = false;
            $column_visibility = false;
            $currency_setting = false;
            $campaign_management_page = false;
            $ads_page = false;
            $financial_setting = false;
            $payment_history = false;
            $content_page = false;
        } else {
            $report_page = $permissions['report_page'] === 1 ? true : false;
            $campaign_page = $permissions['campaign_page'] === 1 ? true : false;
            $column_visibility = $permissions['column_visibility'] === 1 ? true : false;
            $currency_setting = $permissions['currency_setting'] === 1 ? true : false;
            $campaign_management_page = $permissions['campaign_management_page'] === 1 ? true : false;
            $ads_page = $permissions['ads_page'] === 1 ? true : false;
            $financial_setting = $permissions['financial_setting'] === 1 ? true : false;
            $payment_history = $permissions['payment_history'] === 1 ? true : false;
            $content_page = $permissions['content_page'] === 1 ? true : false;
        }
        return view('admin.admins.profile', compact('title', 'admin', 'report_page', 'campaign_management_page', 'campaign_page', 'column_visibility', 'currency_setting', 'ads_page', 'financial_setting', 'payment_history', 'content_page'));
    }

    public function update(UpdateadminRequest $request, Admin $admin)
    {
        $admin->name = $request->name;
        $admin->email = $request->email;
        if(!isset($request->profile))
        {
            $admin->view_id = $request->view_id;
            $admin->client_id = $request->client_id;
            $admin->client_secret = $request->client_secret;
            $admin->account_name = $request->account_name;
            Currency::query()->updateOrInsert(['admin_id' => $admin->id], ['min_value' => $request->currency_min, 'max_value' => $request->currency_max, 'update_at' => date("Y-m-d H:i:s")]);
        }

        if(!empty($request->password))
            $admin->password = bcrypt($request->password);
        if($request->avatar!=null)
        {
            Storage::delete(Admin::find($admin->id)->avatar);
            $path = $request->file('avatar')->store('avatars');
            $admin->avatar = $path;
        }
        $admin->save();

        $user_id = $admin->id;
        $report_page = $request->report_page ?? 0;
        $campaign_page = $request->campaign_page ?? 0;
        $column_visibility = $request->column_visibility ?? 0;
        $currency_setting = $request->currency_setting ?? 0;
        $campaign_management_page = $request->campaign_management_page ?? 0;
        $ads_page = $request->ads_page ?? 0;
        $financial_setting = $request->financial_setting ?? 0;
        $payment_history = $request->payment_history ?? 0;
        $content_page = $request->content_page ?? 0;
        $utm_generator = $request->utm_generator ?? 0;

        $report_page = $report_page === 'on' ? 1 : 0;
        $campaign_page = $campaign_page === 'on' ? 1 : 0;
        $column_visibility = $column_visibility === 'on' ? 1 : 0;
        $currency_setting = $currency_setting === 'on' ? 1 : 0;
        $campaign_management_page = $campaign_management_page === 'on' ? 1 : 0;
        $ads_page = $ads_page === 'on' ? 1 : 0;
        $financial_setting = $financial_setting === 'on' ? 1 : 0;
        $payment_history = $payment_history === 'on' ? 1 : 0;
        $content_page = $content_page === 'on' ? 1 : 0;
        $utm_generator = $utm_generator === 'on' ? 1 : 0;

        if(!isset($request->profile))
        {
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'report_page'], ['user_id' => $user_id, 'page_key' => 'report_page', 'show_rule' => $report_page, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'campaign_management_page'], ['user_id' => $user_id, 'page_key' => 'campaign_management_page', 'show_rule' => $campaign_management_page, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'ads_page'], ['user_id' => $user_id, 'page_key' => 'ads_page', 'show_rule' => $ads_page, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'campaign_page'], ['user_id' => $user_id, 'page_key' => 'campaign_page', 'show_rule' => $campaign_page, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'column_visibility'], ['user_id' => $user_id, 'page_key' => 'column_visibility', 'show_rule' => $column_visibility, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'currency_setting'], ['user_id' => $user_id, 'page_key' => 'currency_setting', 'show_rule' => $currency_setting, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'financial_setting'], ['user_id' => $user_id, 'page_key' => 'financial_setting', 'show_rule' => $financial_setting, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'payment_history'], ['user_id' => $user_id, 'page_key' => 'payment_history', 'show_rule' => $payment_history, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'content_page'], ['user_id' => $user_id, 'page_key' => 'content_page', 'show_rule' => $content_page, 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'utm_generator'], ['user_id' => $user_id, 'page_key' => 'utm_generator', 'show_rule' => $utm_generator, 'updated_at' => date("Y-m-d H:i:s")]);
        }

        if(Auth::guard('admin')->user()->email == $request->email && !isset($request->profile))
        {
            $viewids = json_decode($request->view_id, true);
            $viewidLst = [];
            $urlLst = [];
            foreach($viewids as $row)
            {
                $value = $row['value'];
                array_push($viewidLst, trim(explode(':', $value)[0]));
                array_push($urlLst, trim(explode(':', $value)[1]));
            }

            session()->put('cur_view_id', $viewidLst[0]);
            session()->put('view_ids', $viewidLst);
            session()->put('view_id_urls', $urlLst);
        }
        if(isset($request->profile))
        {
            return redirect()->route('admin.profile', $user_id)->with('success', __('globals.msg.save_success'));
        }
        return redirect()->route('admins.index');
    }

    public function destroy(Admin $admin)
    {
        Storage::delete($admin->avatar);
        $admin->delete();
        ClientSetting::query()->where('user_id', '=', $admin->id)->delete();
        Currency::query()->where('admin_id', '=', $admin->id)->delete();
        Deposit::where(['user_id' => $admin->id])->delete();
        ClientDetail::where(['email' => $admin->email])->delete();

        return response()->json(['status'=>true, 'message'=>'Admin deleted successfully.']);
    }
}
