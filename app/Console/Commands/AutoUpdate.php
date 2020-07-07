<?php

namespace DLW\Console\Commands;

use Illuminate\Console\Command;
use DLW\Models\Report;
use Illuminate\Support\Facades\DB;
use DLW\Libraries\GoogleAnalytics;

class AutoUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:autoupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Campaign sites auto block and bid value change';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //First get to init data for cron update.
        $run_date = date('Y-m-d');
        $calc_date = date('Y-m-d', strtotime("-1 days"));
        
        $res_admins = DB::table('admins')->where('is_super', 1)->get();
        $viewids = json_decode($res_admins[0]->view_id, true);
        $viewidLst = [];
        foreach($viewids as $row)
        {
            $value = $row['value'];
            array_push($viewidLst, trim(explode(':', $value)[0]));
        }

        $res = DB::table('cron_init')->where('date', $run_date)->get();
        
        if(sizeof($res) == 0) return;
        
        $token = $res[0]->token;
        $currencyRate = explode(":", $res[0]->currency)[0];
        $currencyMaxRate = explode(":", $res[0]->currency)[1];

        
        $curCmp = DB::table('cron_campaign')->where('flag', 0)->get();
        if(sizeof($curCmp) == 0) return;
        $curCmpid = $curCmp[0]->cmpid;

        //Get Cur campaign margin value
        $curMargin = DB::table('margins')->where('cmpid', $curCmpid)->get();
        if(sizeof($curMargin) == 0)
        {
            $curMargin = 30;
        } else
        {
            $curMargin = $curMargin[0]->value;
        }

        //Get campaign Date
        $curCmp = $this->getTaboolaCampaignValue($token, $curCmpid);

        $cmpBidAmount = $curCmp['cpc'];
        $cmpName = $curCmp['name'];
        $cmpSpent = $curCmp['spent'];
        $cmpBlockList = $curCmp['publisher_targeting']['value'];
        $cmpCstBoost = $curCmp['publisher_bid_modifier']['values'];

        //Get site list
        $curSites = $this->getTaboolaSites($token, $curCmpid, $calc_date, $calc_date)['results'];

        //Get Google Analysitic Site List
        $dementionLst = ['ga:medium','ga:adContent'];
        $matrixLst = ['ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM'];

        $result = [];

        foreach ($viewidLst as $key => $value) {
            $result = array_merge($result, GoogleAnalytics::getSiteList($value, $dementionLst, $matrixLst, $calc_date, $calc_date, $curCmpid));
        }

        $sitestatus_list = DB::table('site_status')->get();
        $sitestatus = [];
        foreach ($sitestatus_list as $key => $value) {
            $sitestatus[$value->siteid] = $value->status;
        }


        foreach ($curSites as $key => $value)
        {
            $sitetitle = $value['site'];
            $siteid = $value['site_id'];
            $spent = $value['spent'];
            $clicks = $value['clicks'];
            $cpc = floatval($value['cpc']);

            if(array_key_exists($siteid, $sitestatus))
            {
                if($sitestatus[$siteid] == 0)
                    continue;
            }

            $findVal = $this->findSite($result, $sitetitle, $siteid);
            if(empty($findVal)) continue;

            
            $spent = floatval($spent);
            $gSpent = $findVal[2]*$currencyRate;
            $rMax = $gSpent/$currencyRate*$currencyMaxRate;
            $lMin = $gSpent - $spent;
            $lMax = $gSpent/$currencyRate * $currencyMaxRate - $spent;

            if($spent == 0)
            {
                $roiMin = $lMin / 100;
                $roiMax = $lMax / 100;
            } else
            {
                $roiMin = $lMin / $spent  * 100;
                $roiMax = $lMax / $spent * 100;
            }

            if($cpc > 0 && $clicks > 0)
                $cpc= $spent/$clicks;
            //$actual_cpc = $cpc * 1000;
            $bidActual = floatval($cpc);
            $roiMin = round($roiMin, 0);

            if($roiMin == 0) continue;

            $marginVal = (100 - floatval($curMargin)) / 100;
            $bidMax = $rMax * $marginVal;

            if($clicks > 0)
                $bidMax = $rMax / $clicks * $marginVal;

            if($curMargin / $roiMin <= 5 && $bidMax < 0.025 && $clicks > 10)
            {
                if(!in_array($sitetitle, $cmpBlockList))
                {
                    array_push($cmpBlockList, $sitetitle);
                }
            }
            //Bid amount update condition
            if($roiMax > 30) continue;

            $bidValue = $bidMax / $cmpBidAmount;

            if($bidMax < 0.025)
            {
                $bidValue = 0.025 / $cmpBidAmount;
            } else if($bidValue > 2)
            {
                $bidValue = 2; 
            }
            
            if($clicks < 10)
            {
                if($roiMax < 0)
                {
                    $bidValue = 0.025 / $cmpBidAmount;
                } else
                {
                    if($bidValue > 1.2) $bidValue = 1.2;
                    if($bidMax < 0.025) $bidValue = 0.025 / $cmpBidAmount;
                }
            }

            $bidValue = round($bidValue, 2);

            $found = array_filter($cmpCstBoost, function($v,$k) use ($sitetitle){
                        return $v['target'] == $sitetitle;
                    }, ARRAY_FILTER_USE_BOTH);

            if(sizeof($found) == 0)
            {
                $curbidValue = 1;
                if($roiMax < 0)
                {
                    if($curbidValue * $cmpBidAmount > 0.05)
                    {
                        $bidValue = 0.5;
                    } else
                    {
                        $bidValue = 0.8;
                    }
                    if($cmpBidAmount * $bidValue < 0.025)
                    {
                        $bidValue = 0.025 / $cmpBidAmount;
                    }
                    $bidValue = round($bidValue, 2);
                } 
                array_push($cmpCstBoost, [ "target" => $sitetitle, "cpc_modification" => $bidValue]);
            } else
            {
                $curbidValue = $found[array_keys($found)[0]]["cpc_modification"];
                if($roiMax < 0)
                {
                    if($curbidValue * $cmpBidAmount > 0.05)
                    {
                        $bidValue = 1 - 0.5;
                    } else
                    {
                        $bidValue = 1 - 0.2;
                    }
                    if($cmpBidAmount * $bidValue < 0.025)
                    {
                        $bidValue = 0.025 / $cmpBidAmount;
                    }
                    $bidValue = round($bidValue, 2);
                }
                $cmpCstBoost[array_keys($found)[0]]["cpc_modification"] = $bidValue;
            }

            
            // if($clicks >= 10)
            // {
            //     if($roiMax > 0)
            //     {
            //         $bidValue = $roiMax * $bidActual / 100 / $cmpBidAmount;
            //         if($roiMax * $bidActual / 100 < 0.025)
            //             $bidValue = 0.025 / $cmpBidAmount;
            //     }
            //     else
            //     {
            //         $bidValue = $roiMax * 60 / 100 / $cmpBidAmount;
            //         if($roiMax * 60 / 100 < 0.025)
            //             $bidValue = 0.025 / $cmpBidAmount;
            //     }

            //     // if($bidMax < 0.025)
            //     //     $bidValue = 0.025 / $cmpBidAmount;
            //     $bidValue = round($bidValue, 2);
            //     if($bidValue > 1.3) $bidValue = 1.3;
                
            //     if($bidValue < 0.7) $bidValue = 0.7;
                
            //     $found = array_filter($cmpCstBoost, function($v,$k) use ($sitetitle){
            //         return $v['target'] == $sitetitle;
            //     }, ARRAY_FILTER_USE_BOTH);

            //     if(sizeof($found) == 0) 
            //     {
            //         // if(1 - $bidValue > 0.1)
            //         // {
            //         //     $bidValue = 0.9;
            //         // }
            //         // if($cmpBidAmount * $bidValue < 0.025) 
            //         // {
            //         //     $bidValue = 0.025 / $cmpBidAmount;
            //         //     $bidValue = round($bidValue, 2);
            //         // }
            //         array_push($cmpCstBoost, [ "target" => $sitetitle, "cpc_modification" => $bidValue]);
            //     } else
            //     {
            //         // if(1 - $bidValue > 0.1)
            //         // {
            //         //     $bidValue = $found[array_keys($found)[0]]["cpc_modification"] - 0.1;    
            //         // }
            //         // if($cmpBidAmount * $bidValue < 0.025) 
            //         // {
            //         //     $bidValue = 0.025 / $cmpBidAmount;
            //         //     $bidValue = round($bidValue, 2);
            //         // }
            //         // if($bidValue < 0.7) $bidValue = 0.7;
            //         $cmpCstBoost[array_keys($found)[0]]["cpc_modification"] = $bidValue;
            //     }
            // } else if($roiMin > 0) {    //step by step control
                
            //     $found = array_filter($cmpCstBoost, function($v,$k) use ($sitetitle){
            //         return $v['target'] == $sitetitle;
            //     }, ARRAY_FILTER_USE_BOTH); 
                
            //     if(sizeof($found) == 0)
            //     {
            //         array_push($cmpCstBoost, [ "target" => $sitetitle, "cpc_modification" => 1.1]);
            //     } else
            //     {
            //         $bidValue = $found[array_keys($found)[0]]["cpc_modification"] + 0.1;
                    
            //         $bidValue = round($bidValue, 2);
            //         if($bidValue > 1.5) $bidValue = 1.5;
            //         $cmpCstBoost[array_keys($found)[0]]["cpc_modification"] = $bidValue;
            //     }
            // }
        }

        // $sendVal =  [    
        //                     "publisher_targeting" => [
        //                         "type" => "EXCLUDE", 
        //                         "value" => $cmpBlockList
        //                     ],  
        //                     "publisher_bid_modifier" => [
        //                         "values" => $cmpCstBoost
        //                     ] 
        //                 ];


        $sendVal =  [      
                        "publisher_bid_modifier" => [
                            "values" => $cmpCstBoost
                        ] 
                    ];

        
        $result = $this->updateTaboolaCampaigns($token, $curCmpid, $sendVal);

        if(array_key_exists('id', $result))
        {
            $curMargin = DB::table('cron_campaign')->where('cmpid', $curCmpid)->update(['flag' => 1]);            
            $this->info('Cronjob update been send successfully');
        } else
        {
            $this->info('Cronjob update been send failed');            
        }
    }

    public function getTaboolaCampaignValue($access_token, $cmp_id)
    {
        $base_url =env('TO_API_BASE_URL');
        $account_id = env('TO_ACCOUNT_NAME');
        $url = "$base_url/api/1.0/$account_id/campaigns/$cmp_id";

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $access_token"
        ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result, true);
        return $response;
    }

    public function getTaboolaSites($access_token, $cmp_id, $start_date, $end_date)
    {
        $account_id = env('TO_ACCOUNT_NAME');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/reports/campaign-summary/dimensions/site_breakdown?start_date=$start_date&end_date=$end_date&campaign=$cmp_id";

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $access_token"
        ),
        ));

        $result = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($result, true);
        return $response;
    }

    public function updateTaboolaCampaigns($access_token, $id, $value)
    {

        $account_id = env('TO_ACCOUNT_NAME');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/$id";

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($value),
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $access_token",
            "Content-Type: application/json"
          ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result, true);
        return $response;
    }

    public function findSite($data, $site, $siteid)
    {
        foreach ($data as $key => $value) {
            if(preg_match("/\b$site\b/i", $value[0]) || preg_match("/\b$siteid\b/i", $value[0]))
                return $value;
        }
        return [];   
    }
}
