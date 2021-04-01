<?php

namespace DLW\Http\Controllers\Admin\Auth;
use DLW\Http\Controllers\Controller;
use DLW\Models\ClientDetail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use DLW\Mail\ForgetPasswordMail;
use DLW\Models\Report;
use DLW\Models\AcessCount;
use DLW\Models\AcessHistory;
use DLW\Models\Admin;
use DLW\Models\PasswordReset;


class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/admin';

    public function __construct()
    {
        $this->middleware('admin.guest')->except('logout');
    }

    public function redirectTo(){
        return '/admin';
    }

    public function authenticated(Request $request, $user)
    {
        $res = ClientDetail::where(['email' => $user->email])->first();
        if($user->id != 1 && $res === null)
        {
            $this->logout($request);
            return redirect()->route('admin.google_form');
        }

        $viewids = json_decode($user->view_id, true);

        $viewidLst = [];
        $urlLst = [];
        foreach($viewids as $row)
        {
            $value = $row['value'];
            array_push($viewidLst, trim(explode(':', $value)[0]));
            array_push($urlLst, trim(explode(':', $value)[1]));
        }

        session()->put('view_ids', $viewidLst);
        session()->put('view_id_urls', $urlLst);
        session()->put('client_id', $user->client_id);
        session()->put('client_secret', $user->client_secret);
        session()->put('account_name', $user->account_name);

        $currency_setting = DB::table('currency')
            ->where("admin_id", Auth::guard('admin')->user()->id)
            ->get();

        //Get Site status info list and put session.

        $sitestatus_list = DB::table('site_status')->get();
        $sitestatus = [];
        foreach ($sitestatus_list as $key => $value) {
            $sitestatus[$value->siteid] = $value->status;
        }
        session()->put('site_status_list', $sitestatus);

        //////////////////////////////////////////////

        if(sizeof($currency_setting) > 0)
        {
            if($currency_setting[0]->type == 0)
            {
                session()->put("currency_type", $currency_setting[0]->type);
            } else
            {
                session()->put("currency_type", $currency_setting[0]->type);
                session()->put('currency_m_BRL', $currency_setting[0]->min_value);
                session()->put('currency_m_max_BRL', $currency_setting[0]->max_value);
            }
        } else
        {
            session()->put("currency_type", 0);
        }

        session()->put('currency_USD', 1);
        session()->put('currency_max_USD', 1);
        session()->put('currency_m_USD', 1);
        session()->put('currency_m_max_USD', 1);

        Report::getCurrenciesRate("BRL");

        $this->taboolaAccess(session('client_id'), session('client_secret'));

        $allCmp = Report::getTaboolaAllCampaign()['results'] ?? [];
        $end_date = date('Y-m-d');
        $allPayList = Report::getTaboolaDays('2019-01-01', $end_date)['results'] ?? [];
        $allPayList = array_reverse($allPayList);
        session()->put('all_pay_day_list', $allPayList);
        session()->put('all_cmp_ads_list', $allCmp);
        $allCmpValueLst = [];
        foreach($allCmp as $value)
        {
            $allCmpValueLst[$value['id']]['is_active'] = $value['is_active'];
            $allCmpValueLst[$value['id']]['daily_cap'] = $value['daily_cap'];
            $allCmpValueLst[$value['id']]['bid_type'] = $value['bid_type'];
            $allCmpValueLst[$value['id']]['cpc'] = $value['cpc'];
            if($value['start_date'] == null) $value['start_date'] = "-";
            $allCmpValueLst[$value['id']]['start_date'] = $value['start_date'];
        }

        $balance = Report::getTotalBalance($allPayList);

        session()->put('cur_balance', $balance);
        session()->put('all_cmp_list', $allCmpValueLst);

        $permissions = $user->permissions()->pluck('show_rule', 'page_key');
        session()->put('permissions', $permissions);
        AcessHistory::query()->updateOrInsert(['user_id' => $user->id], ['user_id' => $user->id, 'balance' => $balance, 'ip_address' => Report::getIp() ?? '127.0.0.1', 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
        ////////////// All Campaign Puase Balance < env("PAUSE_COUNT") Beta //////////////////////////////////
//        $accssCnt = 0;
//        $accessCntCollection = AcessCount::where('id', '=', 1)->get();
//        if(sizeof($accessCntCollection) > 0)
//        {
//            $accssCnt = $accessCntCollection[0]->count;
//            if($balance < 100 && $accssCnt > intval(env("PAUSE_COUNT")))
//            {
//                $allCmpLst = session()->get('all_cmp_list');
//                $sendVal =  [
//                    'is_active' => false
//                ];
//                $count = 0;
//                foreach($allCmpLst as $key => $value)
//                {
//                    if($value['is_active'])
//                    {
//                        Report::updateTaboolaCampaigns($value['id'], $sendVal);
//                        $allCmpLst[$key]['is_active'] = false;
//                    }
//                    $count++;
//                }
//                session()->put('all_cmp_list', $allCmpLst);
//            }
//        }
//
//
//        if($balance < 100)
//        {
//            $accssCnt++;
//        } else
//        {
//            $accssCnt = 0;
//        }
//
//        AcessCount::query()->updateOrInsert(['id' => 1], ['count' => $accssCnt, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);

        ////////////////////////////////////////////
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function taboolaAccess($to_client_id, $to_client_secret)
    {
        $post = array(
            "client_id"           => $to_client_id,
            "client_secret"       => $to_client_secret,
            "grant_type"          => "client_credentials",
        );

        $base_api_url = env("TO_API_BASE_URL");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_COOKIESESSION, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "App Client" );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ));

        curl_setopt($ch, CURLOPT_URL,$base_api_url."/oauth/token");
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 0);

        $result=curl_exec ($ch);

        $info = curl_getinfo($ch);
        $response = json_decode($result, true);
        $access_token = "";
        if ($info['http_code'] == 200) {
            // okay
            $access_token = $response['access_token'];

        } else {
            // error
            $access_token = $response['error'] . ': ' . $response['error_description'];
        }
        session()->put('access_token' , $access_token);
    }


    /**
     * Send reset password email link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:admins,email',
            ]
        );

        $token = str_random(60);
        $password_reset = new PasswordReset;
        $password_reset->email = $request->email;
        $password_reset->token = $token;
        $password_reset->save();

        $link = route('admin.show_reset_form', $token).'?email=' . urlencode($request->email);
        //Mail send
        Mail::to($request->email)->send(new ForgetPasswordMail($link));

        return redirect()->back()->with('success', __('globals.msg.password_reset_email_sent'));
    }

    /**
     * Reset password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:admins,email',
                'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'min:6'
            ]
        );

        PasswordReset::where( ['email' => $request->email])->delete();
        Admin::where(['email' => $request->email])->update(['password' => bcrypt($request->password)]);

        return redirect()->route('admin.login')->with('success', __('globals.msg.operation_success'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token)
    {
        $email = Input::get('email') ;
        $res = PasswordReset::where(['email' => $email, 'token' => $token])->get();
        if(sizeof($res) < 1)
            return abort( 404);
        else
            return view('admin.auth.reset_password', ['url' => 'admin', 'email' => $email]);
    }

    public function showLoginForm()
    {
        return view('admin.auth.login', ['url' => 'admin']);
    }

    public function forgetPassword()
    {
        return view('admin.auth.forgot_password', ['url' => 'admin']);
    }

    public function showGoogleForm()
    {
        return view('admin.auth.google_form', ['url' => 'admin']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('dashboard');
    }
}

