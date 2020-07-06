<?php

namespace DLW\Http\Controllers\Admin;

use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use DLW\Libraries\GoogleAnalytics;
use DLW\Models\Score;
use DLW\Models\Report;
use Illuminate\Support\Facades\Auth;

use Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.guard');
    }

    public function index()
    {
        $dementionLst = ['ga:sourceMedium'];
        $matrixLst = ['ga:adsenseRevenue', 'ga:adsenseAdUnitsViewed', 'ga:adsenseAdsViewed', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM', 'ga:adsenseExits', 'ga:adsenseViewableImpressionPercent', 'ga:adsenseCoverage'];
        ////
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $period = Period::create( $startDate, $endDate );
        //Period::months(2), Period::days(2)
        //////
        
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-1 months"));
        //$aaa = GoogleAnalytics::report("ga:" . env('ANALYTICS_VIEW_ID'), $dementionLst, $matrixLst, $start_date, $end_date);
        //$allUserCount = User::all()->count();
        //$newUserCount = User::newUserCountRecently(1);
        //$time_tool = GoogleAnalytics::getTimeTool();

        $cur_view_id = session('cur_view_id');
        $view_ids = session('view_ids');
        $view_id_urls = session('view_id_urls');


        if(!isset($cur_view_id))
        {
            $cur_view_id = $view_ids[0];
        }

        $viewid =$cur_view_id;
        
        GoogleAnalytics::setViewId($viewid);

        $allCampaigns = GoogleAnalytics::getCampaigns();
        //$time_reference = GoogleAnalytics::getTimeReference();
        $activeUsers = GoogleAnalytics::activeUsersNow($viewid);
        $activePages = GoogleAnalytics::activePagesNow($viewid);
        $topDevices = GoogleAnalytics::topDevices();
        $rsCountry = Score::rankByCountry();
        $rsActivity = Score::rankByActivity();
        $rsStore = Score::rankByStore();
        $anaUsers = GoogleAnalytics::analyticUsers();
        $returnUsers = GoogleAnalytics::returnUsers();
        $users_country = GoogleAnalytics::usersCountry();

        $start_date = session('rep_start_date');
        $end_date = session('rep_end_date');

        if(!isset($start_date))
        {
            //$end_date = date('Y-m-d');
            //$start_date = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime("-1 days"));
            $end_date = date('Y-m-d', strtotime("-1 days"));

        }
       
        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'allusercount' => 0,
            'newusercount' => 0,
            'time_tool' => 0,
            'time_reference' => 0,
            'activeusers' => $activeUsers,
            'all_campaigns' => $allCampaigns,
            'activepages' => $activePages,
            'topdevices' => $topDevices,
            'rs_country' => $rsCountry,
            'rs_activity' => $rsActivity,
            'rs_store' => $rsStore,
            'ana_users' => $anaUsers,
            'return_users' => $returnUsers,
            'users_country' => $users_country,
            'rep_start_date' => $start_date,
            'rep_end_date' => $end_date,
            'cur_view_id' => $cur_view_id, 
            'view_ids' => $view_ids, 
            'view_id_urls' => $view_id_urls
        ]);
    }

    public function changeViewid(Request $request)
    {
        $view_id = $request->get('cur_view_id');
        session()->put("cur_view_id", $view_id);
        return response()->json(['status'=>true]);
    }

    public function getTotalValue(Request $request)
    {
        $start_date = $request->get('startDate');
        $end_date = $request->get('endDate');
        session()->put("rep_start_date", $start_date);
        session()->put("rep_end_date", $end_date);

        $currencyType = intval(session('currency_type'));
        $currency = "BRL";
        if($currencyType == 0)  //Auto Method...
        {
            $currencyRate = Report::getCurrenciesRate($currency);
            $currecyMaxRate = floatval(session('currency_max_'.$currency));
            $braRate = session('currency_BRL');
        } else                  //Manual Method...
        {
            $currencyRate = floatval(session('currency_m_'.$currency));
            $currecyMaxRate = floatval(session('currency_m_max_'.$currency));
            $braRate = session('currency_m_BRL');
        }
        

        $res = Report::getTaboolaCampaigns($start_date, $end_date);
        
        if(sizeof($res) == 0)
            return response()->json(['status'=>false]);
        
        $dementionLst = ['ga:adContent','ga:source'];
        $matrixLst = ['ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM'];
        
        $view_ids = session('view_ids');
        
        $result = [];
        foreach ($view_ids as $key => $value) {
            $result = array_merge($result, GoogleAnalytics::getAllCampaign($value, $dementionLst, $matrixLst, $start_date, $end_date));
        }

        $s_spent = 0;
        $s_gSpent = 0;
        $s_rMax = 0;
        $s_lMax = 0;
        $s_roiMax = 0;
        
        $count = 0;
        foreach($res['results'] as $value)
        {
            $cmp_id = $value['campaign'];
            $spent = $value['spent'];
            $clicks = $value['clicks'];
            
            if(floatval($spent) == 0 && floatval($clicks) == 0)
                continue;

            $findVal = $this->findCampaign($result, $cmp_id);
            if(empty($findVal)) continue;

            
            $spent = floatval($spent)/floatval($braRate)*$currencyRate;
            $gSpent = $findVal[2]*$currencyRate;

            $rMax = $gSpent/$currencyRate*$currecyMaxRate;
            
            $lMax = $gSpent/$currencyRate*$currecyMaxRate - $spent;
            $roiMax = $lMax / $spent * 100;
            
            $s_spent += $spent;
            $s_gSpent += $gSpent;
            $s_rMax += $rMax;
            $s_roiMax += $roiMax;
            $s_lMax += $lMax;
            
            $count++;
        }

        if($count == 0) 
        {
            return response()->json(['status'=>false]);
        } else
        {
            if($s_spent == 0)
            {
                $s_roiMax = number_format(round($s_lMax / 1 * 100, 2), 2, '.', ',');
            }
            else                
            {
                $s_roiMax = number_format(round($s_lMax / $s_spent * 100, 2), 2, '.', ',');
            }
            $s_spent = number_format(round($s_spent, 2), 2, '.', ',');
            $s_rMax = number_format(round($s_rMax, 2), 2, '.', ',');
            $s_lMax = number_format(round($s_lMax, 2), 2, '.', ',');
            return response()->json(['status'=>true, 's_spent' => $s_spent, 's_rmax' => $s_rMax, 's_lmax' => $s_lMax, 's_roimax' => $s_roiMax]);
        }
    }
    
    public function precho($val)
    {
        echo  "<html><body><pre>";
        var_dump($val);
        echo  "</pre></body></html>";
        exit(0);
    }

    public function findCampaign($data, $id)
    {
        foreach ($data as $key => $value) 
        {
            if(preg_match("/\b$id\b/i", $value[0]) || preg_match("/\b$id\b/i", $value[1]))
                return $value;
        }
        return [];
    }
    
}
