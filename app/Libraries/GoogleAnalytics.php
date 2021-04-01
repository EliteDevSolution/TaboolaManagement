<?php 
namespace DLW\Libraries;

use Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

class GoogleAnalytics{

    public function __construct()
    {

    }

    static function setViewId($viewid)
    {
        Analytics::setViewId($viewid);
    }

    static function usersCountry() {
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $period = Period::create( $startDate, $endDate );
        $result = Analytics::performQuery($period,'ga:sessions',  ['dimensions'=>'ga:country, ga:countryIsoCode','sort'=>'-ga:sessions']);
        return $result->rows;
    }

    static function topDevices()
    {
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $period = Period::create( $startDate, $endDate );
        $result = Analytics::performQuery($period,'ga:sessions', [
            'dimensions'=>'ga:deviceCategory',

        ]);
        return $result->rows;
    }

    static function getAllUsers(){
        $live_users = Analytics::getAnalyticsService()->data_realtime->get('ga:'.session('view_id'), 'rt:activeVisitors')->totalsForAllResults['rt:activeVisitors'];
        return $live_users;
    }

    static function getTimeTool(){
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $period = Period::create( $startDate, $endDate );
        $session_duration = Analytics::performQuery($period,'ga:sessionDuration');
        return date('h:m:s', $session_duration->rows[0][0]);
    }

    static function getTimeReference(){
        $val = 0;
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $period = Period::create( $startDate, $endDate );
        $session_duration = Analytics::performQuery($period,'ga:sessionDuration', [
            'dimensions'=>'ga:pagePath', 
            'include-empty-rows'=>false,

        ]);
        
        
        foreach($session_duration->rows as $row){
            $val += $row[1];
            /* if( (substr($row[0], 0, 9)=="/chapter/") && (substr_count($row[0], '/')==2) ){
                $val += $row[1];
            } */
        }
        return date('h:m:s', $val);
    }

    static function activeUsersNow($viewid){
        $activeUsers = Analytics::getAnalyticsService()->data_realtime->get('ga:'.$viewid, 'rt:activeUsers')->rows[0][0];
        if(($activeUsers==null)||($activeUsers=='')) $activeUsers = 0;
        return $activeUsers; 
    }

    static function activePagesNow($viewid){
        $activePages = Analytics::getAnalyticsService()->data_realtime->get('ga:'.$viewid, 'rt:pageviews', ['dimensions'=>'rt:pagePath', 'max-results'=>10]);
        return $activePages->rows;
    }

    static function analyticUsers(){
        $result = Analytics::performQuery(Period::days(7),'ga:users, ga:sessions, ga:bounceRate, ga:avgSessionDuration',  ['dimensions'=>'ga:date, ga:dayOfWeek']);
        return $result->rows;
    }

    static function returnUsers(){
        $result = Analytics::performQuery(Period::months(2),'ga:users, ga:newUsers',  ['dimensions'=>'ga:month']);
        return $result->rows;
    }

    static function getCampaigns()
    {
        $result = Analytics::performQuery(Period::months(6),'ga:users, ga:newUsers, ga:sessions,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:goalConversionRateAll, ga:goalCompletionsAll,ga:goalValueAll',  ['dimensions'=>'ga:campaign','include-empty-rows'=>false, 'sort'=>'-ga:users']);
        return $result->rows;
    }

    static function getAdsense()
    {
        $result = Analytics::performQuery(Period::months(2),'ga:adsenseRevenue, ga:adsenseAdUnitsViewed, ga:adsenseAdsViewed, ga:adsenseAdsClicks, ga:adsensePageImpressions, ga:adsenseCTR, ga:adsenseECPM, ga:adsenseExits, ga:adsenseViewableImpressionPercent, ga:adsenseCoverage',  ['dimensions'=>'ga:sourceMedium','include-empty-rows'=>false]);
        return $result->rows;
    }

    static function report($view, $dimensions, $metrics, $start_date, $end_date, $start_index=1, $max_results=10, $filter_lst=[], $sort=''){
        // to make the request quicker
        
        // query the last month analytics

        // if( !is_array( $dimensions ) )
        // 	$dimensions = array( $dimensions );
        //$view ="ga:".session('view_id');
    
        $dimensions = implode( ",", $dimensions );
        $metrics = implode( ",", $metrics );
        $filters = implode( ",", $filter_lst);
    
        try{
            
            $analytics = Analytics::getAnalyticsService();
            $options = [];
    
            $options['dimensions'] = $dimensions;
            $options['max-results'] = $max_results;
            $options['start-index'] = $start_index;
            //$exceptFilter = "(not set)";
            //$defaultFilter = "ga:adContent!@$exceptFilter";
            // if($filters != "")
            //     $options['filters'] = $defaultFilter.';'.$filters;
            // else
            //     $options['filters'] = $defaultFilter;
            if($filters != "")
                $options['filters'] = $filters;

            if($sort != "")
                $options['sort'] = $sort;
            
            $data = $analytics->data_ga->get( $view, $start_date, $end_date, $metrics,
                $options
            );
    
            $res = [
                'items' => isset($data['rows']) ? $data['rows'] : [],
                'columnHeaders'	=> $data['columnHeaders'],
                'totalResults'	=> $data['totalResults'],
                'totalForResults' => $data['totalsForAllResults']
            ];
    
        }catch( Exception $ex ){
            return Response::json([
                'status'	=> 0,
                'code'		=> 2,
                'message'	=> 'Google analytics internal server error: (Technical details) ' . $ex->getErrors()[0]['message']
            ]);
        }//catch
    
        return $res;
    }//report

    static function getAllCampaign($view_id, $dimensions, $metrics, $start_date, $end_date){
        // to make the request quicker
        
        // query the last month analytics

        // if( !is_array( $dimensions ) )
        // 	$dimensions = array( $dimensions );
        $view ="ga:".$view_id;

    
        $dimensions = implode( ",", $dimensions );
        $metrics = implode( ",", $metrics );

        try{
            
            $analytics = Analytics::getAnalyticsService();
            $options = [];
            $options['max-results'] = 10000;
            $options['dimensions'] = $dimensions;

            $data = $analytics->data_ga->get($view, $start_date, $end_date, $metrics,
                $options
            );
    
            $res = isset($data['rows']) ? $data['rows'] : [];
    
        }catch( Exception $ex ){
            return Response::json([
                'status'	=> 0,
                'code'		=> 2,
                'message'	=> 'Google analytics internal server error: (Technical details) ' . $ex->getErrors()[0]['message']
            ]);
        }//catch
    
        return $res;
    }//report


    static function getSiteList($view_id, $dimensions, $metrics, $start_date, $end_date, $cmp_id)
    {
        // to make the request quicker
        
        // query the last month analytics

        // if( !is_array( $dimensions ) )
        //  $dimensions = array( $dimensions );
        $view ="ga:".$view_id;
    
        $dimensions = implode( ",", $dimensions );
        $metrics = implode( ",", $metrics );
    
        try{
            
            $analytics = Analytics::getAnalyticsService();
            $options = [];
    
            $options['dimensions'] = $dimensions;
            $options['filters'] = "ga:adContent%3D%3D$cmp_id";


            $data = $analytics->data_ga->get( $view, $start_date, $end_date, $metrics,
                $options
            );
    
            $res = isset($data['rows']) ? $data['rows'] : [];
    
        }catch( Exception $ex ){
            return Response::json([
                'status'    => 0,
                'code'      => 2,
                'message'   => 'Google analytics internal server error: (Technical details) ' . $ex->getErrors()[0]['message']
            ]);
        }//catch
    
        return $res;
    }//report


}