<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Models\Report;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use DLW\Libraries\GoogleAnalytics;
use Illuminate\Support\Facades\Auth;


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

        $currencies = ['USD','BRL'];
        $cur_currency = 'BRL';
        $prev_currency = session('cur_currency');
        
        $start_date = session('rep_start_date');
        $end_date = session('rep_end_date');
        $cur_view_id = session('cur_view_id');

        if(!isset($start_date))
        {
            $end_date = date('Y-m-d');
            $start_date = date('Y-m-d');
            //$start_date = date('Y-m-d', strtotime("-1 days"));
        }

        if(!isset($cur_view_id))
        {
            $cur_view_id = "";
        }

        $view_ids = session('view_ids');
        $view_id_urls = session('view_id_urls');

        if(isset($prev_currency) && $prev_currency != "")
        {
            $cur_currency = $prev_currency;
        }
        //[ToboolaAccessToken: '.substr(session('access_token'),30).'...]
        return view('admin.reports.index', ['title' => 'Report', 'currencies' => $currencies, 'curcurrency' => $cur_currency, 'rep_start_date' => $start_date, 'rep_end_date' => $end_date, 'cur_view_id' => $cur_view_id, 'view_ids' => $view_ids, 'view_id_urls' => $view_id_urls]);
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

        $dementionLst = ['ga:adContent','ga:source'];
        $matrixLst = ['ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM'];
        
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
        foreach($resdata['items'] as $value)
        {
            $childItems = [];
            foreach($value as $key=>$row)
            {
                if($key == 0 || $key == 1)
                {
                    array_push($childItems, $row);
                }
                 else if($key == 2 || $key == 6)
                {
                    array_push($childItems, number_format(round(floatval($row)*$currencyRate, 2), 2, '.', ','));
                } else if($key == 5)
                {
                    array_push($childItems, number_format(floatval($row), 2, '.', ',').'%');
                }
                else
                {
                    array_push($childItems, number_format(floatval($row), 0, '.', ','));
                }
            }
            array_push($resItems, $childItems);
        }

        $totalItems = [];

        $key = 0;
        foreach($resdata['totalForResults'] as $row)
        {
            if($key == 0 || $key == 4)
            {
                array_push($totalItems, number_format(round(floatval($row)*$currencyRate, 2), 2, '.', ','));
            } else if($key == 1 || $key == 2)
            {
                array_push($totalItems, number_format(floatval($row), 0, '.', ','));
            } else 
            {
                array_push($totalItems, number_format(floatval($row), 2, '.', ',').'%');
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
