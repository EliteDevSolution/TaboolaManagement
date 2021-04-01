<?php

namespace DLW\Http\Controllers\Admin;

use Cassandra\Date;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use DLW\Libraries\GoogleAnalytics;
use DLW\Models\Score;
use DLW\Models\Report;
use Illuminate\Support\Facades\Auth;
use File;

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
        ////
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();

        //////
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-1 months"));
        //$aaa = GoogleAnalytics::report("ga:" . env('ANALYTICS_VIEW_ID'), $dementionLst, $matrixLst, $start_date, $end_date);
        //$allUserCount = User::all()->count();
        //$newUserCount = User::newUserCountRecently(1);
        //$time_tool = GoogleAnalytics::getTimeTool();
        //$period = Period::create( $startDate, $endDate );
        //Period::months(2), Period::days(2)

        $cur_view_id = session('cur_all_view_id');
        $view_ids = session('view_ids');
        $view_id_urls = session('view_id_urls');


        if(!isset($cur_view_id))
        {
            $cur_view_id = "0";
        }

        $viewid =$cur_view_id;

        $start_date = session('rep_start_date');
        $end_date = session('rep_end_date');

        if(!isset($start_date))
        {
            $start_date = date('Y-m-d', strtotime("-1 days"));
            $end_date = date('Y-m-d', strtotime("-1 days"));
            session()->put("rep_start_date", $start_date);
            session()->put("rep_end_date", $end_date);
        }

        $begin = new \DateTime( $start_date);
        $end = new \DateTime( $end_date );
        $end = $end->modify( '+1 day' );
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval ,$end);

        $currencyType = intval(session('currency_type'));
        $currency = 'BRL';

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
            session()->put('cur_currency', $currency);
        }


        $taboolaRes = Report::getTaboolaCampaignsDay($start_date, $end_date)['results'] ?? [];

        $dementionLst = ['ga:date','ga:adContent','ga:source'];
        $matrixLst = ['ga:adsenseRevenue'];

        $view_ids = session('view_ids') ?? [];

        $result = [];
        $keyList = [];
        $lastGoogleRes = [];
        if($cur_view_id != "0")
        {
            $result = GoogleAnalytics::getAllCampaign($cur_view_id, $dementionLst, $matrixLst, $start_date, $end_date);
            $lastGoogleRes = $result;
        } else
        {
            foreach ($view_ids as $key => $value) {
                $lastGoogleRes = GoogleAnalytics::getAllCampaign($value, $dementionLst, $matrixLst, $start_date, $end_date);
                $result = array_merge($result, $lastGoogleRes);
            }
        }

        $gBenefitArray = array();
        $taboolaSpentArray = array();
        $roiArray = array();
        $profitMaxArray = array();

        $sum_gBenefit = 0;
        $sum_tSpent = 0;
        $sum_roi = 0;
        $sum_profit = 0;

        //Calculation processor..../////
        $sendVal = [];
        $sum_recevied = 0;
        $sum_spent = 0;
        foreach($daterange as $date)
        {

            $curDate = $date->format("Y-m-d");
            $curGoogleDate = $date->format("Ymd");
            $total_Recevied = 0;
            $cmpid_list = [];
            $cmptitle_list = '';
            $receive_list = [];

            foreach ($result as $item)
            {
                if($item[1] == "(not set)") continue;
                if($item[0] == $curGoogleDate)
                {
                    $cmpid_list[] = $item[1];
                    $cmptitle_list .= $item[2];
                    if (!isset($receive_list[$item[1]]))
                        $receive_list[$item[1]] = $item[3] * $currecyMaxRate;
                    else
                        $receive_list[$item[1]] += $item[3] * $currecyMaxRate;
                }
            }
            $total_Spent = 0;
            foreach ($taboolaRes as $key => $v)
            {
                $curId = $v['campaign'];
                if(preg_match("/\b$curDate\b/i", $v['date']) && (in_array($v['campaign'], $cmpid_list) || preg_match("/\b$curId\b/i", $cmptitle_list)))
                {
                    $total_Recevied += $receive_list[$curId];
                    //array_push($totalR, $receive_list[$curId]);
                    if($v['spent'] == 0) continue;
                    $total_Spent += round($v['spent'],2);
                }
            }

            $curVal['total_recevie'] = round($total_Recevied,2);
            $curVal['total_spent'] = $total_Spent;
            $curVal['total_profit'] = round($total_Recevied - $total_Spent, 2);
            if($total_Spent == 0)
                $curVal['total_roi'] = round($curVal['total_profit'] * 100, 2);
            else
                $curVal['total_roi'] = round($curVal['total_profit'] / $total_Spent * 100, 2);

            $sendVal[$curDate] = $curVal;
            $sum_spent += $total_Spent;
            $sum_recevied += $total_Recevied;
        }

        $sum_profit = round($sum_recevied - $sum_spent, 2);
        if($sum_spent == 0)
        {
            $sum_roi = round($sum_profit * 100, 2);
        } else
        {
            $sum_roi = round($sum_profit / $sum_spent * 100, 2);
        }
        $sum_gBenefit = number_format($sum_recevied, 2, '.', ',');
        $sum_tSpent = number_format($sum_spent, 2, '.', ',');
        $sum_profit = number_format($sum_profit, 2, '.', ',');
        $sum_roi = number_format($sum_roi, 2, '.', ',');

        //$anaUsers = GoogleAnalytics::analyticUsers();

        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'send_val' => $sendVal,
            'g_benefit' => $gBenefitArray,
            't_spent' => $taboolaSpentArray,
            'roi' => $roiArray,
            'profit' => $profitMaxArray,
            'sum_benefit' => $sum_gBenefit,
            'sum_spent' => $sum_tSpent,
            'sum_profit' => $sum_profit,
            'sum_roi' => $sum_roi,
            'rep_start_date' => $start_date,
            'rep_end_date' => $end_date,
            'cur_view_id' => $viewid,
            'view_ids' => $view_ids,
            'view_id_urls' => $view_id_urls,
        ]);
    }

    public function changeViewid(Request $request)
    {
        $view_id = $request->get('cur_view_id');
        session()->put("cur_view_id", $view_id);
        return response()->json(['status'=>true]);
    }

    public function changeAllViewid(Request $request)
    {
        $view_id = $request->get('cur_all_view_id');
        session()->put("cur_all_view_id", $view_id);
        return response()->json(['status'=>true]);
    }

    public function changeDate(Request $request)
    {
        $start_date = $request->get('startDate');
        $end_date = $request->get('endDate');
        session()->put("rep_start_date", $start_date);
        session()->put("rep_end_date", $end_date);
        return response()->json(['status'=>true]);
    }

    public function getTotalValueTemp(Request $request)
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

        $res = Report::getTaboolaDays($start_date, $end_date);

        if(sizeof($res) == 0)
            return response()->json(['status'=>false]);

        $dementionLst = ['ga:date'];
        $matrixLst = ['ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM'];

        $view_ids = session('view_ids');

        $result = [];

        foreach ($view_ids as $key => $value) {
            $result = array_merge($result, GoogleAnalytics::getAllCampaign($value, $dementionLst, $matrixLst, $start_date, $end_date));
        }

        $s_gSpent = 0;
        $s_spent = 0;
        $s_rMax = 0;
        $s_lMax = 0;
        $s_roiMax = 0;

        $count = 0;

        foreach($result as $row)
        {
            $s_gSpent += $row[1]*$currencyRate;
        }

        foreach($res['results'] as $value)
        {
            $s_spent += $value['spent'];
        }

        $s_lMax = $s_gSpent/$currencyRate*$currecyMaxRate - $s_spent;

        if($s_spent == 0)
        {
            $s_roiMax = number_format(round($s_lMax / 1 * 100, 2), 2, '.', ',');
        }
        else
        {
            $s_roiMax = number_format(round($s_lMax / $s_spent * 100, 2), 2, '.', ',');
        }

        $s_rMax = $s_gSpent/$currencyRate*$currecyMaxRate;
        $s_spent = number_format(round($s_spent, 2), 2, '.', ',');
        $s_rMax = number_format(round($s_rMax, 2), 2, '.', ',');
        $s_lMax = number_format(round($s_lMax, 2), 2, '.', ',');
        return response()->json(['status'=>true, 's_spent' => $s_spent, 's_rmax' => $s_rMax, 's_lmax' => $s_lMax, 's_roimax' => $s_roiMax]);
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

    public function findCampaign($data, $id)
    {
        foreach ($data as $key => $value)
        {
            if(preg_match("/\b$id\b/i", $value[0]) || preg_match("/\b$id\b/i", $value[1]))
                return $value;
        }
        return [];
    }

    public function findTaboolaHourValue($data, $val)
    {
        if(strlen($val) == 1) $val = '0'.$val;
        $val .= ":00";
        foreach ($data as $key => $value)
        {
            if(preg_match("/\b$val\b/i", $value['hour_of_day']))
            {
                return round(floatval($value['spent']),2);
            }
        }
        return 0;
    }

    public function findHourTotal($data, $val)
    {
        $sum = 0;
        if(strlen($val) == 1) $val = '0'.$val;
        foreach ($data as $key => $value)
        {
            if(preg_match("/\b$val\b/i", $value[0]))
            {
                $sum += round(floatval($value[1]),2);
            }
        }
        return $sum;
    }

    public function findDayTotal($data, $val)
    {
        $sum = 0;
        foreach ($data as $key => $value)
        {
            if(preg_match("/\b$val\b/i", $value[0]))
            {
                $sum += round(floatval($value[1]),2);
            }
        }
        return $sum;
    }

    public function findTaboolaDayValue($data, $val)
    {
        $val = date("Y-m-d", strtotime($val));
        foreach ($data as $key => $value)
        {
            if(preg_match("/\b$val\b/i", $value['date']))
            {
                return round(floatval($value['spent']),2);
            }
        }
        return 0;
    }

}
