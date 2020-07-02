<?php

namespace DLW\Http\Controllers\Admin\Auth;
use DLW\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DLW\Models\Report;

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

      if(Auth::guard('admin')->user()->is_super == true) {
        $view_id = env('ANALYTICS_VIEW_ID');
        $client_id = env('TO_CLIENT_ID');
        $client_secret = env("TO_CLIENT_SECRET");
        $account_name = env("TO_ACCOUNT_NAME");
        $view_id_merge = env("SUPER_ADMIN_MERGE_VIEW_IDS");

        session()->put('view_id_merge', $view_id_merge);

        //var_dump(session('view_id_merge'));exit;
        if(!isset($client_id) || $client_id == "")
        {
          session()->put('client_id', Auth::guard('admin')->user()->client_id);
        } else
        {
          session()->put('client_id', $client_id);
        }

        if(!isset($client_secret) || $client_secret == "")
        {
          session()->put('client_secret', Auth::guard('admin')->user()->client_secret);
        } else
        {
          session()->put('client_secret', $client_secret);
        }

        if(!isset($account_name) || $account_name == "")
        {
          session()->put('account_name', Auth::guard('admin')->user()->account_name);
        } else
        {
          session()->put('account_name', $account_name);
        }
        if(!isset($view_id) || $view_id == "") {
          //session('view_id', Auth::guard('admin')->user()->view_id);
          session()->put('view_id', Auth::guard('admin')->user()->view_id);
        } else
        {
          session()->put('view_id', $view_id);
        }
      } else
      {
        session()->put('view_id', Auth::guard('admin')->user()->view_id);
        session()->put('client_id', Auth::guard('admin')->user()->client_id);
        session()->put('client_secret', Auth::guard('admin')->user()->client_secret);
        session()->put('account_name', Auth::guard('admin')->user()->account_name);
      }
      

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

      $allCmp = Report::getTaboolaAllCampaign()['results'];
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
      session()->put('all_cmp_list', $allCmpValueLst);
    }

    protected function guard(){
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

    public function showLoginForm(){
      return view('admin.auth.login', ['url' => 'admin']);
    }

    public function logout(){
      Auth::guard('admin')->logout();
      return redirect()->route('dashboard');
    }
}

