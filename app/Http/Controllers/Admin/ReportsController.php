<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Models\Report;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use DLW\Libraries\GoogleAnalytics;

use Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.guard');
    }

    public function index()
    {
        //$reports = Report::orderby('updated_at', 'desc')->get();
        //$dementionLst = ['ga:socialActivityContentUrl'];
        if(sizeof(session('permissions')) > 0 && session('permissions')['report_page'] == 0)
        {
            return abort( 404);
        }

        $currencies = ['USD','BRL'];
        $cur_currency = 'BRL';
        $prev_currency = session('cur_currency');
        
        $start_date = session('rep_start_date');
        $end_date = session('rep_end_date');
        $cur_view_id = session('cur_view_id');
        $view_ids = session('view_ids');

        if(!isset($cur_view_id))
        {
            $cur_view_id = $view_ids[0];
        }

        if(!isset($start_date))
        {
            $end_date = date('Y-m-d');
            $start_date = date('Y-m-d');
            //$start_date = date('Y-m-d', strtotime("-1 days"));
        }

        if(!isset($cur_view_id))
        {
            $cur_view_id = $view_ids[0];
        }

        $view_ids = session('view_ids');
        $view_id_urls = session('view_id_urls');

        if(isset($prev_currency) && $prev_currency != "")
        {
            $cur_currency = $prev_currency;
        }

        
        $viewid =$cur_view_id;
        
        GoogleAnalytics::setViewId($viewid);
        //$time_reference = GoogleAnalytics::getTimeReference();
        $activeUsers = GoogleAnalytics::activeUsersNow($viewid);
        $activePages = GoogleAnalytics::activePagesNow($viewid);
        $topdevices = GoogleAnalytics::topDevices();
        if(!isset($topdevices[0][1]) || empty($topdevices[0][1])) $topdevices[0][1] = 0;
        if(!isset($topdevices[1][1]) || empty($topdevices[1][1])) $topdevices[1][1] = 0;
        if(!isset($topdevices[2][1]) || empty($topdevices[2][1])) $topdevices[2][1] = 0;
        $totalDevices = $topdevices[0][1] + $topdevices[1][1] + $topdevices[2][1];
        if($totalDevices == 0 ) $totalDevices = 1;

        $anaUsers = GoogleAnalytics::analyticUsers() ?? [];
        $returnUsers = GoogleAnalytics::returnUsers() ?? [];
        $users_country = GoogleAnalytics::usersCountry() ?? [];

        $start_date = session('rep_start_date');
        $end_date = session('rep_end_date');

        if(!isset($start_date))
        {
            $start_date = date('Y-m-d', strtotime("-1 days"));
            $end_date = date('Y-m-d', strtotime("-1 days"));
        }
       
        return view('admin.reports.index', [
            'title' => 'Reports',
            'allusercount' => 0,
            'newusercount' => 0,
            'time_tool' => 0,
            'time_reference' => 0,
            'activeusers' => $activeUsers,
            'activepages' => $activePages,
            'topdevices' => $topdevices,
            'total_devices' => $totalDevices,
            'ana_users' => $anaUsers,
            'return_users' => $returnUsers,
            'users_country' => $users_country,
            'rep_start_date' => $start_date,
            'rep_end_date' => $end_date,
            'cur_view_id' => $cur_view_id, 
            'view_ids' => $view_ids, 
            'view_id_urls' => $view_id_urls,
            'currencies' => $currencies, 
            'curcurrency' => $cur_currency, 
        ]);
    }

    public function getAnalysisJson(Request $request)
    {

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $currency = $request->get('currency');
        $curviewid = $request->get('curviewid');
        session()->put("cur_view_id", $curviewid);
        $viewid ="ga:".$curviewid;


        //date range keep session...///
        session()->put("rep_start_date", $start_date);
        session()->put("rep_end_date", $end_date);

        $columnIndex = $request->get('order')[0]['column']; // Column index
        $columnName = $request->get('columns')[$columnIndex]['name']; // Column name
        $columnSortOrder = $request->get('order')[0]['dir']; // asc or desc
        $searchValue = $request->get('search')['value']; // Search value
        

        $currencyType = intval(session('currency_type'));

        if($currencyType == 0)  //Auto Method...
        {
            $currencyRate = Report::getCurrenciesRate($currency);
        } else                  //Manual Method...
        {
            session()->put('cur_currency', $currency);
            $currencyRate = floatval(session('currency_m_'.$currency));
        }


        $search = (isset($searchValue)) ? $searchValue : '';
        $order = "";
        if(isset($columnSortOrder) && $columnSortOrder == "asc")
        {   
            $order = "-";
        }

        //ga:sessions/ga:pageviews
        $dementionLst = ['ga:adContent','ga:source'];
        $matrixLst = ['ga:users', 'ga:bounceRate', 'ga:sessions', 'ga:pageviews', 'ga:avgSessionDuration', 'ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM'];
        
        $filterLst = [];
        if($search != "")
        {
            $filterLst = ["ga:adContent%3D@$search", "ga:source%3D@$search"];    
        }

        $sort = $order.$columnName;
        ////
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $period = Period::create($startDate, $endDate);
        //Period::months(2), Period::days(2)
        //////

        //$end_date = date('Y-m-d');
        //$start_date = date('Y-m-d', strtotime("-1 months"));
        $resdata = GoogleAnalytics::report($viewid, $dementionLst, $matrixLst, $start_date, $end_date, $start + 1, $length, $filterLst, $sort);

        $resItems = [];
        foreach($resdata['items'] as $value) {
            $childItems = [];
            for($key = 0; $key < sizeof($value); $key++)
            {
                if($key == 0 || $key == 1)
                {
                    array_push($childItems, $value[$key]);
                } else if($key == 4)
                {
                    continue;
                } else if($key == 5)
                {
                    if($value[4] == 0)
                        array_push($childItems, number_format(round($value[5], 2), 2, '.', ','));
                    else
                        array_push($childItems, number_format(round($value[5]/$value[4], 2), 2, '.', ','));
                } else if($key == 7 || $key == 11)
                {
                    array_push($childItems, number_format(round(floatval($value[$key])*$currencyRate, 2), 2, '.', ','));
                } else if($key == 3 || $key == 10)
                {
                    array_push($childItems, number_format(floatval($value[$key]), 2, '.', ',').'%');
                } else if($key == 2 || $key == 8 || $key == 9)
                {
                    array_push($childItems, number_format(floatval($value[$key]), 0, '.', ','));
                } else if($key == 6)
                {
                    array_push($childItems, gmdate("H:i:s", $value[$key]));
                }
            }
            array_push($resItems, $childItems);
        }

        $totalItems = [];
        $totalList = $resdata['totalForResults'];
        $key = 0;
        $divVal = 0;
        foreach ($totalList as $row)
        {
            if($key == 0 || $key == 6 || $key == 7)
            {
                array_push($totalItems, number_format(floatval($row), 0, '.', ','));
            } else if($key == 1 || $key == 8)
            {
                array_push($totalItems, number_format(floatval($row), 2, '.', ',').'%');
            } else if($key == 2)
            {
                $divVal = $row;
                $key++;
                continue;
            } else if($key == 3)
            {
                if($divVal == 0)
                    array_push($totalItems, number_format(round($row, 2), 2, '.', ','));
                else
                    array_push($totalItems, number_format(round($row/$divVal, 2), 2, '.', ','));
            } else if($key == 5 || $key == 9)
            {
                array_push($totalItems, number_format(round(floatval($row)*$currencyRate, 2), 2, '.', ','));
            } else if($key == 4)
            {
                array_push($totalItems, gmdate("H:i:s", $row));
            }
            $key++;
        }


        $data = array(
            'draw' => $draw,
            'recordsTotal' => $resdata['totalResults'],
            'recordsFiltered' => $resdata['totalResults'],
            'data' => $resItems,
            'currency' => $currencyRate,
            'total' => $totalItems
        );

        return response()->json($data);
    }


    public function getSiteAnalysisJson(Request $request)
    {

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $cmp_id = $request->get('cmpid');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $currency = $request->get('currency');
        $curviewid = $request->get('curviewid');
        session()->put("cur_view_id", $curviewid);
        $viewid ="ga:".$curviewid;


        //date range keep session...///
        session()->put("rep_start_date", $start_date);
        session()->put("rep_end_date", $end_date);

        $columnIndex = $request->get('order')[0]['column']; // Column index
        $columnName = $request->get('columns')[$columnIndex]['name']; // Column name
        $columnSortOrder = $request->get('order')[0]['dir']; // asc or desc
        $searchValue = $request->get('search')['value']; // Search value


        $currencyType = intval(session('currency_type'));

        if($currencyType == 0)  //Auto Method...
        {
            $currencyRate = Report::getCurrenciesRate($currency);
        } else                  //Manual Method...
        {
            session()->put('cur_currency', $currency);
            $currencyRate = floatval(session('currency_m_'.$currency));
        }


        $search = (isset($searchValue)) ? $searchValue : '';
        $order = "";
        if(isset($columnSortOrder) && $columnSortOrder == "asc")
        {
            $order = "-";
        }

        //ga:sessions/ga:pageviews
        $dementionLst = ['ga:adContent','ga:medium'];
        $matrixLst = ['ga:users', 'ga:bounceRate', 'ga:sessions', 'ga:pageviews', 'ga:avgSessionDuration', 'ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM'];
        $filterLst = ["ga:adContent%3D%3D$cmp_id"];

        if($search != "")
        {
            array_push($filterLst, "ga:medium%3D@$search");
        }

        $sort = $order.$columnName;
        ////
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $period = Period::create($startDate, $endDate);
        //Period::months(2), Period::days(2)
        //////

        //$end_date = date('Y-m-d');
        //$start_date = date('Y-m-d', strtotime("-1 months"));
        $resdata = GoogleAnalytics::report($viewid, $dementionLst, $matrixLst, $start_date, $end_date, $start + 1, $length, $filterLst, $sort);

        $resItems = [];
        foreach($resdata['items'] as $value) {
            $childItems = [];
            for($key = 0; $key < sizeof($value); $key++)
            {
                if($key == 0 || $key == 1)
                {
                    array_push($childItems, $value[$key]);
                } else if($key == 4)
                {
                    continue;
                } else if($key == 5)
                {
                    if($value[4] == 0)
                        array_push($childItems, number_format(round($value[5], 2), 2, '.', ','));
                    else
                        array_push($childItems, number_format(round($value[5]/$value[4], 2), 2, '.', ','));
                } else if($key == 7 || $key == 11)
                {
                    array_push($childItems, number_format(round(floatval($value[$key])*$currencyRate, 2), 2, '.', ','));
                } else if($key == 3 || $key == 10)
                {
                    array_push($childItems, number_format(floatval($value[$key]), 2, '.', ',').'%');
                } else if($key == 2 || $key == 8 || $key == 9)
                {
                    array_push($childItems, number_format(floatval($value[$key]), 0, '.', ','));
                } else if($key == 6)
                {
                    array_push($childItems, gmdate("H:i:s", $value[$key]));
                }
            }
            array_push($resItems, $childItems);
        }

        $totalItems = [];
        $totalList = $resdata['totalForResults'];
        $key = 0;
        $divVal = 0;
        foreach ($totalList as $row)
        {
            if($key == 0 || $key == 6 || $key == 7)
            {
                array_push($totalItems, number_format(floatval($row), 0, '.', ','));
            } else if($key == 1 || $key == 8)
            {
                array_push($totalItems, number_format(floatval($row), 2, '.', ',').'%');
            } else if($key == 2)
            {
                $divVal = $row;
                $key++;
                continue;
            } else if($key == 3)
            {
                if($divVal == 0)
                    array_push($totalItems, number_format(round($row, 2), 2, '.', ','));
                else
                    array_push($totalItems, number_format(round($row/$divVal, 2), 2, '.', ','));
            } else if($key == 5 || $key == 9)
            {
                array_push($totalItems, number_format(round(floatval($row)*$currencyRate, 2), 2, '.', ','));
            } else if($key == 4)
            {
                array_push($totalItems, gmdate("H:i:s", $row));
            }
            $key++;
        }


        $data = array(
            'draw' => $draw,
            'recordsTotal' => $resdata['totalResults'],
            'recordsFiltered' => $resdata['totalResults'],
            'data' => $resItems,
            'currency' => $currencyRate,
            'total' => $totalItems
        );

        return response()->json($data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Report $report)
    {
        //
    }

    public function edit(Report $report)
    {
        //
    }

    public function update(Request $request, Report $report)
    {
        //
    }

    public function destroy(Report $report)
    {
        //
    }
}
