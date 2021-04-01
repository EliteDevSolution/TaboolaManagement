<?php

namespace DLW\Http\Controllers\Admin;


use DLW\Models\PasswordReset;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

use DLW\Models\Admin;
use DLW\Models\ClientSetting;
use DLW\Models\ClientDetail;
use DLW\Models\Report;

use DLW\Mail\NewUserMail;

class ClientDetailsController extends Controller
{
    public function __construct(){

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::guard('admin')->user()->is_super)
            return abort(404);
        $list = ClientDetail::orderBy('id', 'asc')->get();
        return view('admin.client_details.index', ['title'=>__('globals.common.request_list'), 'list'=>$list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Update info for user side.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ClientDetail  $clientDetail
     * @return \Illuminate\Http\Response
     */
    public function updateInfo(Request $request, ClientDetail $clientDetail)
    {
        if(!Auth::guard('admin')->user())
            return abort(404);
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:client_details,email,'.$clientDetail->id,
                'business_name' => 'required',
                'cnpj' => 'required',
                'address' => 'required',
                'bank_name' => 'required',
                'bank_proxy' => 'required',
                'bank_confirm' => 'required',
                'cpf_cnpj' => 'required'
            ]
        );

        $clientDetail->email = $request->email;
        $clientDetail->name = $request->name;
        $clientDetail->business_name = $request->business_name;
        $clientDetail->cnpj = $request->cnpj;
        $clientDetail->address = $request->address;
        $clientDetail->phone_number = $request->phone;
        $clientDetail->bank_name = $request->bank_name;
        $clientDetail->bank_proxy_name = $request->bank_proxy;
        $clientDetail->bank_cpf_cnpj = $request->cpf_cnpj;
        $clientDetail->ip_address = Report::getIp() ?? '127.0.0.1';
        $clientDetail->bank_account_confirm = $request->bank_confirm;
        $clientDetail->note = $request->other_info;
        $clientDetail->save();
        //Admin::where(['email' => $request->email])->update(['email' => $request->email]);
        return redirect()->route('profile.client_details.show', urlencode($request->email))->with('success', __('globals.msg.save_success'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveData(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:client_details,email',
                'business_name' => 'required',
                'cnpj' => 'required',
                'address' => 'required',
                'bank_name' => 'required',
                'bank_proxy' => 'required',
                'bank_confirm' => 'required',
                'cpf_cnpj' => 'required'
            ]
        );

        $settings = new ClientDetail;
        $settings->email = $request->email;
        $settings->name = $request->name;
        $settings->business_name = $request->business_name;
        $settings->cnpj = $request->cnpj;
        $settings->address = $request->address;
        $settings->phone_number = $request->phone;
        $settings->bank_name = $request->bank_name;
        $settings->bank_proxy_name = $request->bank_proxy;
        $settings->bank_cpf_cnpj = $request->cpf_cnpj;
        $settings->accept_status = 1;
        $settings->bank_account_confirm = $request->bank_confirm;
        $settings->ip_address = Report::getIp() ?? '127.0.0.1';
        $settings->accept_date_time = date("Y-m-d H:i:s").' '.config('app.timezone');
        $settings->doc_version = env('DOC_VERSION');
        $settings->note = $request->other_info;
        $settings->save();

        $curUser = Admin::where(['email' => $request->email])->first();

        if(!$curUser)
        {
            $admin = new Admin;
            $admin->name = $request->name;
            $admin->view_id = '[]';
            $admin->client_id = 'taboola client id';
            $admin->client_id = 'taboola client id';
            $admin->client_secret = 'taboola client secret key';
            $admin->account_name = 'taboola account name';
            $admin->email = $request->email;
            $new_password = Report::generateRandomString();
            $admin->password = bcrypt($new_password);
            $admin->save();

            $user_id = $admin->id;

            //User Permission Add

            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'report_page'], ['user_id' => $user_id, 'page_key' => 'report_page', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'campaign_management_page'], ['user_id' => $user_id, 'page_key' => 'campaign_management_page', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'ads_page'], ['user_id' => $user_id, 'page_key' => 'ads_page', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'campaign_page'], ['user_id' => $user_id, 'page_key' => 'campaign_page', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'column_visibility'], ['user_id' => $user_id, 'page_key' => 'column_visibility', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'currency_setting'], ['user_id' => $user_id, 'page_key' => 'currency_setting', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'financial_setting'], ['user_id' => $user_id, 'page_key' => 'financial_setting', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'payment_history'], ['user_id' => $user_id, 'page_key' => 'payment_history', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'content_page'], ['user_id' => $user_id, 'page_key' => 'content_page', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
            ClientSetting::query()->updateOrInsert(['user_id' => $user_id, 'page_key' => 'utm_generator'], ['user_id' => $user_id, 'page_key' => 'utm_generator', 'show_rule' => 0, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);

            ///Mail send
            Mail::to($request->email)->send(new NewUserMail(['name' => $request->name, 'email' => $request->email, 'password' => $new_password]));
            return redirect()->route('admin.login')->with('success', __('globals.msg.reg_new_success'));
        }

        return redirect()->route('admin.login')->with('success', __('globals.msg.request_sent_success'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAjaxInfo(Request $request)
    {
        if(request()->ajax()) {
            $res = ClientDetail::where(['email' => $request->email])->first();
            return response()->json(['results' => $res]);
        }
    }


    /**
     * show & edit page for user side.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showInfo(Request $request, $email)
    {
        if(!Auth::guard('admin')->user())
        {
            abort(404);
        }
        $curRow = ClientDetail::where(['email' => $email])->first();
        if(!$curRow)
        {
            abort(404);
        }
        return view('admin.client_details.show', ['title'=>__('globals.common.request_list'), 'detail'=>$curRow]);
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAjaxDocVersion(Request $request)
    {
        if(request()->ajax()) {
            $date_time = date("Y-m-d H:i:s").' '.config('app.timezone');
            $ip = Report::getIp() ?? '127.0.0.1';
            $res = ClientDetail::where(['email' => $request->email])->update(['doc_version' => $request->doc_version, 'accept_status' => 1,
                'accept_date_time' => $date_time, 'ip_address' => $ip]);
            return response()->json(['results' => $res]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ClientDetail  $clientDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientDetail $clientDetail)
    {
        return view('admin.client_details.edit', ['title'=>__('globals.common.request_list'), 'detail'=>$clientDetail]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ClientDetail  $clientDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClientDetail $clientDetail)
    {
        if(!Auth::guard('admin')->user()->is_super)
            return abort(404);
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:client_details,email,'.$clientDetail->id,
                'business_name' => 'required',
                'cnpj' => 'required',
                'address' => 'required',
                'bank_name' => 'required',
                'bank_proxy' => 'required',
                'bank_confirm' => 'required',
                'cpf_cnpj' => 'required'
            ]
        );

        $clientDetail->email = $request->email;
        $clientDetail->name = $request->name;
        $clientDetail->business_name = $request->business_name;
        $clientDetail->cnpj = $request->cnpj;
        $clientDetail->address = $request->address;
        $clientDetail->phone_number = $request->phone;
        $clientDetail->bank_name = $request->bank_name;
        $clientDetail->bank_proxy_name = $request->bank_proxy;
        $clientDetail->bank_cpf_cnpj = $request->cpf_cnpj;
        $clientDetail->bank_account_confirm = $request->bank_confirm;
        $clientDetail->note = $request->other_info;
        $clientDetail->save();
        //Admin::where(['email' => $request->email])->update(['email' => $request->email]);
        return redirect()->route('client_details.index')->with('success', __('globals.msg.save_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
