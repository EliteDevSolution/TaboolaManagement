<?php

namespace DLW\Http\Controllers\Admin;
use DLW\Models\Report;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DLW\Libraries\GoogleAnalytics;
use Illuminate\Support\Facades\DB;


use Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;
use Currency;

use function PHPSTORM_META\type;

class SheetController extends Controller
{
    public function __construct(){
        $this->middleware('admin.guard');
    }

    public function index()
    {
        
        $currencies = ['USD','BRL'];
        $cur_currency = 'BRL';
        $prev_currency = session('cur_currency');

        $start_date = session('rep_start_date');
        $end_date = session('rep_end_date');

        if(!isset($start_date))
        {
            //$end_date = date('Y-m-d');
            //$start_date = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime("-1 days"));
            $end_date = date('Y-m-d', strtotime("-1 days"));

        }

        if(isset($prev_currency) && $prev_currency != "")
        {
            $cur_currency = $prev_currency;
        }

        //Report::getCurrenciesRate("BRL");

        return view('admin.sheet.index', ['title'=>'Analysis Sheet', 'currencies' => $currencies, 'curcurrency' => $cur_currency, 'rep_start_date' => $start_date, 'rep_end_date' => $end_date]); 
    }

    public function getSiteData(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $currency = $request->get('currency');
        $cmp_id = $request->get('campaign_id');
        $cmp_margin = $request->get('margin');
        session()->put("rep_start_date", $start_date);
        session()->put("rep_end_date", $end_date);


        //////////////////.........Currency String Processor.........../////////////////
        $currencyStr = 'R$';
        if($currency == 'USD')
            $currencyStr = '$';

        $siteData = [];

        $currencyType = intval(session('currency_type'));

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

        $htmlContent = "";

        
        $curCampaignData = Report::getTaboolaCampaignValue($cmp_id);
        
        $bidAmountLimit = round(0.025/floatval($braRate)*$currencyRate, 3);

        
        $cmpBidAmount = $curCampaignData['cpc'];
        $cmpName = $curCampaignData['name'];
        $cmpSpent = $curCampaignData['spent'];
        $cmpSpent = number_format(round($cmpSpent, 2), 2, '.', ',');
        $cmpbidAmount = $cmpBidAmount / floatval($braRate)*$currencyRate;;
        $cmpbidAmount = number_format(round($cmpbidAmount, 3), 3, '.', ',');


        $cmpBlockList = $curCampaignData['publisher_targeting']['value'];
        $cmpCstBoost = $curCampaignData['publisher_bid_modifier']['values'];

        //Session value register campaign data 
        session()->put("site_blocklist", $cmpBlockList);
        session()->put("site_cstboost", $cmpCstBoost);


        $siteLst = Report::getTaboolaSites($cmp_id, $start_date, $end_date)['results'];



        $dementionLst = ['ga:medium','ga:adContent'];
        $matrixLst = ['ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM'];
        
        
        
        $s_spent = 0;
        $s_gSpent = 0;
        $s_rMax = 0;
        $s_roiMin = 0;
        $s_roiMax = 0;
        $s_lMin = 0;
        $s_lMax = 0;
        $s_clicks = 0;
        $s_bidAcutal = 0;
        $s_bidAmount = 0;
        $s_bidMax = 0;
        $count = 0;
        $view_ids = session('view_ids');
        
        $result = [];
        
        foreach ($view_ids as $key => $value) {
            $result = array_merge($result, GoogleAnalytics::getSiteList($value, $dementionLst, $matrixLst, $start_date, $end_date, $cmp_id));
        }

        $site_status_list = session('site_status_list');

        foreach ($siteLst as $key => $value) {
            
            $site_name = $value['site'];

            $href = "http://".$site_name.".com";

            if(preg_match("/\b-\b/i", $site_name))
            {
                 $lastIndex = sizeof(explode("-", $site_name)) - 1;
                 $href = "http://". explode("-", $site_name)[$lastIndex].".com";
            }

            //$href = "#";

            $site_id = $value['site_id'];
            $site_title = $value['site_name'];
            $spent = $value['spent'];
            $clicks = $value['clicks'];
            $cpc = floatval($value['cpc']);

            $findVal = $this->findSite($result, $site_name, $site_id);
            if(empty($findVal)) continue;

            
            $spent = floatval($spent)/floatval($braRate)*$currencyRate;
            $gSpent = $findVal[2]*$currencyRate;
            

            $rMax = $gSpent/$currencyRate*$currecyMaxRate;
            
            
            $lMin = $gSpent - $spent;
            $lMax = $gSpent/$currencyRate * $currecyMaxRate - $spent;

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
            $cpc = floatval($cpc)/floatval($braRate)*$currencyRate;
            //$actual_cpc = $cpc * 1000;
            $bidActual = $cpc;
            $cstboost = 1;
            $cstboost_percent = 'Default';
            $site_block = false;
            $r_cstboost = 0;

            if(!empty($cmpBlockList))
                $site_block = $this->isSiteBlock($cmpBlockList, $site_name);

            if(!empty($cmpCstBoost))
            {
                $cstboost = $this->findSiteBoostValue($cmpCstBoost, $site_name);
                $r_cstboost = ($cstboost - 1) * 100;
                if($cstboost != 1 && round($r_cstboost, 3) != 0)
                {
                    $cstboost_percent = strval($r_cstboost).'%';    
                }
            }

            $bidAmount = $cmpBidAmount * $cstboost / floatval($braRate)*$currencyRate;

            $marginVal = (100 - floatval($cmp_margin)) / 100;
            $bidMax = $rMax * $marginVal;
            
            if($clicks > 0)
                $bidMax = $rMax / $clicks * $marginVal;

            $curBidAmount = round($bidAmount, 3);

            $siteData[$site_id]['site_id'] = $site_id;
            $siteData[$site_id]['site_name'] = $site_name;
            $siteData[$site_id]['spent'] = $spent;
            
            if($currency == "USD") //USD case
            {
                $siteData[$site_id]['bid_actual'] = $bidActual * $braRate;
                $siteData[$site_id]['bid_max'] = $bidMax * $braRate;       
                $siteData[$site_id]['default_bid'] = $cmpBidAmount * $braRate;
                $siteData[$site_id]['receive_max'] = $rMax * $braRate;

            } else
            {
                $siteData[$site_id]['bid_actual'] = $bidActual;
                $siteData[$site_id]['bid_max'] = $bidMax;
                $siteData[$site_id]['default_bid'] = $cmpBidAmount;
                $siteData[$site_id]['receive_max'] = $rMax;
            }

            $siteData[$site_id]['roi_min'] = round($roiMin, 2);
            $siteData[$site_id]['roi_max'] = round($roiMax, 2);

            //$siteData[$site_id]['cmp_margin'] = $cmp_margin;
            $siteData[$site_id]['clicks'] = $clicks;
            $siteData[$site_id]['r_boost'] = $cstboost;

            $f_spent = number_format(round($spent, 2), 2, '.', ',');
            $f_gSpent = number_format(round($gSpent, 2), 2, '.', ',');
            $f_rMax = number_format(round($rMax, 2), 2, '.', ',');
            $f_roiMin = number_format(round($roiMin, 2), 2, '.', ',');
            $f_roiMax = number_format(round($roiMax, 2), 2, '.', ',');
            $f_lMin = number_format(round($lMin, 2), 2, '.', ',');
            $f_lMax = number_format(round($lMax, 2), 2, '.', ',');
            $f_clicks = number_format(floatval($clicks), 0, '.', ',');
            $f_bidAcutal = number_format(round($bidActual, 3), 3, '.', ',');
            $f_bidAmount = number_format(round($bidAmount, 3), 3, '.', ',');
            $f_bidMax = number_format(round($bidMax, 3), 3, '.', ',');



            $s_spent += $spent;
            $s_gSpent += $gSpent;
            $s_rMax += $rMax;
            $s_roiMin += $roiMin;
            $s_roiMax += $roiMax;
            $s_lMin += $lMin;
            $s_lMax += $lMax;
            $s_clicks += $clicks;


            $s_bidAcutal += round($bidActual, 3);
            $s_bidAmount += round($bidAmount, 3);
            $s_bidMax += round($bidMax, 3);

            $count++;

            $btn_blockHtml = "<button id='btn_block_$site_id' site-id='$site_id' data-id='$site_name' status='unblock' class='btn btn-danger waves-effect waves-light btn-sm' onclick='setSiteBlock(this)'><i class='mdi mdi-block-helper'></i></button>";

            if($site_block)
            {
                $btn_blockHtml = "<button id='btn_block_$site_id' site-id='$site_id'  data-id='$site_name' status='block' class='btn btn-success waves-effect waves-light btn-sm' onclick='setSiteBlock(this)'><i class='mdi mdi-reload'></i></button>";
            }

            $btn_decreseHtml = "<button id='btn_dec_$site_id' site-id='$site_id'  data-id='$site_name' class='btn btn-secondary waves-effect waves-light btn-sm' onclick='bidDecrease(this)'><i class='mdi mdi-minus'></i></button>";

            if($curBidAmount <= $bidAmountLimit)
            {
                $btn_decreseHtml = "<button id='btn_dec_$site_id' site-id='$site_id'  data-id='$site_name' class='btn btn-secondary waves-effect waves-light btn-sm' disabled='disabled'><i class='mdi mdi-minus'></i></button>";
            }
            
            $btn_increaseHtml = "<button id='btn_inc_$site_id' site-id='$site_id'  data-id='$site_name' class='btn btn-secondary waves-effect waves-light btn-sm' onclick='bidIncrease(this)'><i class='mdi mdi-plus'></i></button>";

            $btn_playHtml = "<button id='btn_status_$site_id' site-id='$site_id' status='play' data-id='$site_name' class='btn btn-success waves-effect waves-light btn-sm' onclick='siteActivate(this)'><i class='mdi mdi-play'></i></button>";

            $btn_pauseHtml = "<button id='btn_status_$site_id' site-id='$site_id' status='pause' data-id='$site_name' class='btn btn-danger waves-effect waves-light btn-sm' onclick='siteActivate(this)'><i class='mdi mdi-pause'></i></button>";

            $btn_site_stautsHtml = $btn_pauseHtml;

            if(array_key_exists($site_id, $site_status_list))
            {
                if($site_status_list[$site_id] == 0)
                    $btn_site_stautsHtml = $btn_playHtml;
            }

            $htmlContent .= "<tr>";
            $htmlContent .= "<td><a style='color: #0b54c6;' href='$href' target='_blank' title='$site_title'>$site_id</a></td><td>$currencyStr $f_spent</td><td>$currencyStr $f_gSpent</td><td>$currencyStr $f_rMax</td><td>$f_roiMin%</td><td>$f_roiMax%</td><td>$currencyStr $f_lMin</td><td>$currencyStr $f_lMax</td><td>$f_clicks</td><td data-toggle='popover'>$currencyStr $f_bidAcutal</td><td><a data-id='$site_name' boost='$r_cstboost' id='$site_id' class='popover_toggle' data-toggle='popover' onclick='showPopover(this)'>$currencyStr $f_bidAmount($cstboost_percent)</a><br>$btn_decreseHtml $btn_increaseHtml $btn_blockHtml</td><td>$currencyStr $f_bidMax</td><td>$btn_site_stautsHtml</td>";
            $htmlContent .= "</tr>";
        }

        $foot = "<tr><th>Total</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>-</th></tr>";

        if($count > 0)
        {
            if($s_spent != 0)
            {
                $s_roiMin = number_format(round($s_lMin/$s_spent*100, 2), 2, '.', ',');
                $s_roiMax = number_format(round($s_lMax/$s_spent*100, 2), 2, '.', ',');
            } else
            {
                $s_roiMin = number_format(round($s_lMin/1*100, 2), 2, '.', ',');
                $s_roiMax = number_format(round($s_lMax/1*100, 2), 2, '.', ',');
            }
            $s_spent = number_format(round($s_spent, 2), 2, '.', ',');
            $s_gSpent = number_format(round($s_gSpent, 2), 2, '.', ',');
            $s_rMax = number_format(round($s_rMax/$count, 2), 2, '.', ',');
            $s_lMin = number_format(round($s_lMin, 2), 2, '.', ',');
            $s_lMax = number_format(round($s_lMax, 2), 2, '.', ',');
            $s_clicks = number_format(floatval($s_clicks), 0, '.', ',');
            $s_bidAcutal = number_format($s_bidAcutal/$count, 3, '.', ',');
            $s_bidAmount = number_format($s_bidAmount/$count, 3, '.', ',');
            $s_bidMax = number_format($s_bidMax/$count, 3, '.', ',');

            $foot = "<tr><th>Total</th><th>$currencyStr $s_spent</th><th>$currencyStr $s_gSpent</th><th>$currencyStr $s_rMax</th><th>$s_roiMin%</th><th>$s_roiMax%</th><th>$currencyStr $s_lMin</th><th>$currencyStr $s_lMax</th><th>$s_clicks</th><th>$currencyStr $s_bidAcutal</th><th>$currencyStr $s_bidAmount</th><th>$currencyStr $s_bidMax</th><th>-</th></tr>";
        }

        session()->put("site_data", $siteData);

        return response()->json(['status'=>true, 'cmpname'=>$cmpName, 'cmpspent'=>$cmpSpent, 'cmpbidamount'=>$cmpbidAmount, 'cmpbidamountlimit'=>$bidAmountLimit, 'data'=>$htmlContent, 'foot'=>$foot]);

    }

    public function setMarginValue(Request $request)
    {
        $cur_date = $request->get('cur_date');
        $value = $request->get('value');
        $cmp_id = $request->get('cmp_id');
        $update_date = date('Y-m-d H:i:s');

        DB::table('margins')
        ->updateOrInsert(
            ['cmpid' => $cmp_id],
            ['value' => $value, 'date' => $update_date] 
        );
        return response()->json(['status'=>true]);
    }

    public function getCurrencyInfo()
    {
       $currency_setting = DB::table('currency')
                     ->where("admin_id", Auth::guard('admin')->user()->id)
                     ->get();

      if(sizeof($currency_setting) > 0)
      {
          return response()->json(['type'=>$currency_setting[0]->type, 'minval'=>$currency_setting[0]->min_value, 'maxval'=>$currency_setting[0]->max_value ]);
      } else
      {
        return response()->json(['type'=>0, 'minval'=>'', 'maxval'=>'' ]);
      }
    }

    public function changeSiteStatus(Request $request)
    {
        $curStatus = $request->get('status');
        $siteId = $request->get('siteid');

        if($curStatus == 0)
        {
            DB::table('site_status')
            ->updateOrInsert(
                ['siteid' => $siteId],
                ['status' => $curStatus] 
            );
            
            $site_status_list = session('site_status_list');
            $site_status_list[$siteId] = intval($curStatus);
            session()->put('site_status_list', $site_status_list);    
        } else if($curStatus == 1)
        {
            DB::table('site_status')->where('siteid', $siteId)->delete();
            $site_status_list = session('site_status_list');
            unset($site_status_list[$siteId]);
            session()->put('site_status_list', $site_status_list);    
        }
        
        return response()->json(['status'=>true]);
    }


    public function setCurrencyValue(Request $request)
    {

        $type = $request->get('type');
        $admin_id = Auth::guard('admin')->user()->id;
        $min_value = $request->get('min_value');
        $max_value = $request->get('max_value');
        $update_at = date('Y-m-d H:i:s');

        if($type == 0) //auto
        {
            DB::table('currency')
            ->updateOrInsert(
                ['admin_id' => $admin_id],
                ['type' => $type, 'update_at' => $update_at] 
            );

            session()->put("currency_type", $type);

        } else
        {
            DB::table('currency')
            ->updateOrInsert(
                ['admin_id' => $admin_id],
                ['type' => $type, 'min_value' => $min_value, 'max_value' => $max_value, 'update_at' => $update_at] 
            );

            session()->put("currency_type", $type);
            session()->put('currency_m_BRL', $min_value);
            session()->put('currency_m_max_BRL', $max_value);
        }
        
        return response()->json(['status'=>true]);
    } 

    public function getTableData(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $currency = $request->get('currency');

        session()->put("rep_start_date", $start_date);
        session()->put("rep_end_date", $end_date);

        $margin_res = DB::table('margins')->get();

        
        $currencyType = intval(session('currency_type'));

        $currencyStr = 'R$';
        if($currency == 'USD')
            $currencyStr = '$';


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

        $res = Report::getTaboolaCampaigns($start_date, $end_date);

        if(sizeof($res) == 0)
            return response()->json(['status'=>false]); 
        

        $htmlContent = "";

        $dementionLst = ['ga:adContent','ga:source'];
        $matrixLst = ['ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:adsenseCTR', 'ga:adsenseECPM'];
        
        $view_ids = session('view_ids');
        
        $result = [];

        foreach ($view_ids as $key => $value) {
            $result = array_merge($result, GoogleAnalytics::getAllCampaign($value, $dementionLst, $matrixLst, $start_date, $end_date));
        }

        
        $bidAmountLimit = round(0.025/floatval($braRate)*$currencyRate, 3);
        $dailyLimit = round(2/floatval($braRate)*$currencyRate, 3);

        $s_spent = 0;
        $s_gSpent = 0;
        $s_rMax = 0;
        $s_roiMin = 0;
        $s_roiMax = 0;
        $s_lMin = 0;
        $s_lMax = 0;
        $s_clicks = 0;
        $s_bidAcutal = 0;
        $s_bidMax = 0;
        $s_margin = 0;
        $s_daily = 0;
        $s_strategy = 0;
        $count = 0;

        $selectHtml = "";

        $allCmpValueLst = session('all_cmp_list');

        $cmpData = [];

        foreach($res['results'] as $value)
        {
            $cmp_id = $value['campaign'];
            $spent = $value['spent'];
            $clicks = $value['clicks'];
            $tcmpname = $value['campaign_name'];
            $cpc = floatval($value['cpc']);
            
            if(floatval($spent) == 0 && floatval($clicks) == 0)
                continue;

            $findVal = $this->findCampaign($result, $cmp_id);
            if(empty($findVal)) continue;

            $start_date = $allCmpValueLst[$cmp_id]['start_date'];
            $is_active = $allCmpValueLst[$cmp_id]['is_active'];
            
            $daily = floatval($allCmpValueLst[$cmp_id]['daily_cap']) / floatval($braRate) * $currencyRate;
            $bid_strategy = floatval($allCmpValueLst[$cmp_id]['cpc']) / floatval($braRate) * $currencyRate;
            $bid_type = $allCmpValueLst[$cmp_id]['bid_type'];
            $spent = floatval($spent)/floatval($braRate)*$currencyRate;
            $gSpent = $findVal[2]*$currencyRate;

            $rMax = $gSpent/$currencyRate*$currecyMaxRate;
             
            $lMin = $gSpent - $spent;
            $lMax = $gSpent/$currencyRate*$currecyMaxRate - $spent;

            $roiMin = $lMin / $spent  * 100;
            $roiMax = $lMax / $spent * 100;   

            $campaignName = $findVal[1];


            if($cpc != 0)
                $cpc= $spent/$clicks;
            $cpc = floatval($cpc)/floatval($braRate)*$currencyRate;
            
            $actual_cpc =$cpc * 1000;

            $bidActual = $cpc;


            $margin_pro = $this->findMarginValue($margin_res, $cmp_id);

            $margin = (100 - $margin_pro) / 100;

            $bidMax = $rMax/$clicks*$margin;

            $cmpData[$cmp_id]['spent'] = $spent;
            $cmpData[$cmp_id]['cpc'] = $cpc;
            $cmpData[$cmp_id]['clicks'] = $clicks;
            $cmpData[$cmp_id]['is_active'] = $is_active;


            $f_daily = number_format(round($daily, 2), 0, '.', ',');
            $f_spent = number_format(round($spent, 2), 2, '.', ',');
            $f_gSpent = number_format(round($gSpent, 2), 2, '.', ',');
            $f_rMax = number_format(round($rMax, 2), 2, '.', ',');
            $f_roiMin = number_format(round($roiMin, 2), 2, '.', ',');
            $f_roiMax = number_format(round($roiMax, 2), 2, '.', ',');
            $f_lMin = number_format(round($lMin, 2), 2, '.', ',');
            $f_lMax = number_format(round($lMax, 2), 2, '.', ',');
            $f_clicks = number_format(floatval($clicks), 0, '.', ',');
            $f_bidAcutal = number_format(round($bidActual, 3), 3, '.', ',');
            $f_bidStrategy = number_format(round($bid_strategy, 3), 3, '.', ',');
            $f_bidMax = number_format(round($bidMax, 3), 3, '.', ',');

            $f_daily_val = round($daily);
            $f_bidStrategy_val = round($bid_strategy, 3);
            

            $s_daily += $daily;
            $s_spent += $spent;
            $s_gSpent += $gSpent;
            $s_rMax += $rMax;
            $s_roiMin += $roiMin;
            $s_roiMax += $roiMax;
            $s_lMin += $lMin;
            $s_lMax += $lMax;
            $s_clicks += $clicks;
            $s_bidAcutal += round($bidActual, 3);
            $s_strategy += $bid_strategy;
            $s_bidMax += round($bidMax, 3);
            $s_margin += $margin_pro;

            $count++;

            $selectHtml .= "<option value='$cmp_id' margin='$margin_pro'>$cmp_id [$tcmpname]</option>";

            $btn_statusHtml = "<button id='btn_status_$cmp_id' cmp-id='$cmp_id' status='pause' class='btn btn-danger waves-effect waves-light btn-sm' onclick='setCmpPause(this)'><i class='mdi mdi-pause'></i></button>";

            if(!$is_active)
            {
                $btn_statusHtml = "<button id='btn_status_$cmp_id' cmp-id='$cmp_id' status='play' class='btn btn-success waves-effect waves-light btn-sm' onclick='setCmpPause(this)'><i class='mdi mdi-play'></i></button>";
            }

            $htmlContent .= "<tr>";
            $htmlContent .= "<td><a id='cmp_$cmp_id' style='color: #0b54c6;' onclick='goSiteData($cmp_id)' title='$campaignName'>$cmp_id</a></td><td id='bid_daily_$cmp_id' cmp-id='$cmp_id' class='popover_toggle' data-value='$f_daily_val' data-toggle='popover' onclick='showDailyPopover(this)'>$currencyStr $f_daily</td><td>$currencyStr $f_spent</td><td>$currencyStr $f_gSpent</td><td>$currencyStr $f_rMax</td><td>$f_roiMin%</td><td>$f_roiMax%</td><td>$currencyStr $f_lMin</td><td>$currencyStr $f_lMax</td><td>$f_clicks</td><td>$currencyStr $f_bidAcutal</td><td id='bid_strategy_$cmp_id' cmp-id='$cmp_id' class='popover_toggle' data-value='$f_bidStrategy_val' data-toggle='popover' onclick='showStrategyPopover(this)'>$currencyStr $f_bidStrategy<br>($bid_type)</td><td>$currencyStr $f_bidMax</td><td id='$cmp_id' class='popover_toggle' data-toggle='popover' date-last='$end_date' onclick='showMarginPopover(this)'>$margin_pro%</td><td>$start_date</td><td>$btn_statusHtml</td>";
            $htmlContent .= "</tr>";
        }

        $foot = "<tr><th>Total</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>0</th><th>-</th><th>-</th></tr>";

        if($count > 0)
        {
            if($s_spent != 0)
            {
                $s_roiMin = number_format(round($s_lMin/$s_spent*100, 2), 2, '.', ',');
                $s_roiMax = number_format(round($s_lMax/$s_spent*100, 2), 2, '.', ',');
            } else
            {
                $s_roiMin = number_format(round($s_lMin/1*100, 2), 2, '.', ',');
                $s_roiMax = number_format(round($s_lMax/1*100, 2), 2, '.', ',');
            }
            
            $s_daily = number_format(round($s_daily/$count, 2), 0, '.', ',');
            $s_spent = number_format(round($s_spent, 2), 2, '.', ',');
            $s_gSpent = number_format(round($s_gSpent, 2), 2, '.', ',');
            $s_rMax = number_format(round($s_rMax, 2), 2, '.', ',');
            $s_lMin = number_format(round($s_lMin, 2), 2, '.', ',');
            $s_lMax = number_format(round($s_lMax, 2), 2, '.', ',');
            $s_clicks = number_format(floatval($s_clicks), 0, '.', ',');
            $s_bidAcutal = number_format($s_bidAcutal/$count, 3, '.', ',');
            $s_strategy = number_format(round($s_strategy/$count, 3), 3, '.', ',');

            $s_bidMax = number_format($s_bidMax/$count, 3, '.', ',');
            $s_margin = number_format($s_margin / $count, 2, '.', ',');
            $foot = "<tr><th>Total</th><th>$currencyStr $s_daily</th><th>$currencyStr $s_spent</th><th>$currencyStr $s_gSpent</th><th>$currencyStr $s_rMax</th><th>$s_roiMin%</th><th>$s_roiMax%</th><th>$currencyStr $s_lMin</th><th>$currencyStr $s_lMax</th><th>$s_clicks</th><th>$currencyStr $s_bidAcutal</th><th>$currencyStr $s_strategy</th><th>$currencyStr $s_bidMax</th><th>$s_margin%</th><th>-</th><th>-</th></tr>";
        }

        session()->put("cmp_data", $cmpData);
        return response()->json(['status'=>true, 'bidamountlimit'=>$bidAmountLimit, 'dailylimit'=>$dailyLimit, 'data'=>$htmlContent, 'selectlist'=>$selectHtml, 'foot'=>$foot]); 
    }

    public function changeCmpDailyValue(Request $request)
    {
        $value = $request->get('value');
        $cmp_id = $request->get('cmp_id');
        $currency = $request->get('currency');
        $tmpVal = $value;
        
        if($currency == "USD")
        {
            $currencyType = intval(session('currency_type'));
            if($currencyType == 0)  //Auto Method...
            {
                $currencyRate = Report::getCurrenciesRate('BRL');
            } else                  //Manual Method...
            {
                $currencyRate = floatval(session('currency_m_BRL'));
            }
            $value = $value * $currencyRate;
        } 

        $sendVal =  [      
            'daily_cap' => $value
        ];
        
        Report::updateTaboolaCampaigns($cmp_id, $sendVal);

        $allCmp = session('all_cmp_list');
        $allCmp[$cmp_id]['daily_cap'] = $value;
        session()->put('all_cmp_list', $allCmp);

        $f_daily = number_format(round($tmpVal, 0), 0, '.', ',');
        
        return response()->json(['status'=>true, 'daily_cap'=>round($tmpVal), 'f_daily_cap' => $f_daily]);
    
    }

    public function changeCmpStrategyValue(Request $request)
    {
        $value = $request->get('value');
        $cmp_id = $request->get('cmp_id');
        $currency = $request->get('currency');
        $tmpVal = $value;

        if($currency == "USD")
        {
            $currencyType = intval(session('currency_type'));
            if($currencyType == 0)  //Auto Method...
            {
                $currencyRate = Report::getCurrenciesRate('BRL');
            } else                  //Manual Method...
            {
                $currencyRate = floatval(session('currency_m_BRL'));
            }
            $value = $value * $currencyRate;
        } 

        $sendVal =  [      
            'cpc' => $value
        ];
        
        $res = Report::updateTaboolaCampaigns($cmp_id, $sendVal);

        $allCmp = session('all_cmp_list');
        $allCmp[$cmp_id]['cpc'] = $value;
        session()->put('all_cmp_list', $allCmp);

        return response()->json(['status'=>true, 'bid_strategy'=>round($tmpVal, 3), 'bid_type' => $res['bid_type']]);

    }

    public function updateCampaign(Request $request)
    {
        $cmpid = $request->get('cmp_id');
        $siteid = $request->get('site_id');
        $type = $request->get('type');
        $changeval = $request->get('value');
        //Session value register campaign data 
        $cmpBlockList = session("site_blocklist");
        $cmpCstBoost = session("site_cstboost");

        $result = [];
        $sendVal = [];
        
        if($type == "blocking")
        {
            if($changeval == 'block')
            {
                array_push($cmpBlockList, $siteid);

            } else if($changeval == 'unblock')
            {   
                $cmpBlockList = array_diff($cmpBlockList, array($siteid));
            }

            $sendVal = [
                        "publisher_targeting" => [
                            "type" => "EXCLUDE", 
                            "value" => array_values($cmpBlockList)
                            ]
                        ]; 

            if(empty($cmpBlockList))
            {
                $sendVal = [
                        "publisher_targeting" => [
                            "type" => "ALL"
                            ]
                        ]; 
            }
            $result = Report::updateTaboolaCampaigns($cmpid, $sendVal);
            session()->put("site_blocklist", $result['publisher_targeting']['value']);

        } else if($type == "boost")
        {
            $found = array_filter($cmpCstBoost, function($v,$k) use ($siteid){
                    return $v['target'] == $siteid;
            }, ARRAY_FILTER_USE_BOTH); 
            $method = "";
            
            if(empty($found)) 
            {
                $method = "ADD";

                array_push($cmpCstBoost, [ "target" => $siteid, "cpc_modification" => floatval($changeval) ]);
            } else
            {
                $method = "REPLACE";
                $cmpCstBoost[array_keys($found)[0]]["cpc_modification"] = floatval($changeval);
            }

            $modVal = [[ "target" => $siteid, "cpc_modification" => floatval($changeval) ]];

            $sendVal = [
                "patch_operation" => $method,
                "publisher_bid_modifier" => [
                     "values" => $modVal
                  ] 
            ];

            $result = Report::patchTaboolaCampaigns($cmpid, $sendVal);
            
            $tmpCmpSiteData = session('site_data');

            $site_found = array_filter($tmpCmpSiteData, function($v,$k) use ($siteid){
                return $v['site_name'] == $siteid;
            }, ARRAY_FILTER_USE_BOTH); 

            if(sizeof($site_found) > 0)
            {
                $tmpCmpSiteData[array_keys($site_found)[0]]['r_boost'] = floatval($changeval);
                session()->put('site_data', $tmpCmpSiteData);
            }
            
            if(!array_key_exists('publisher_bid_modifier', $result))
            {
                $res = $result['publisher_bid_modifier'];
                return response()->json(['status'=>false]); 
            }
            session()->put("site_cstboost", $cmpCstBoost);

        } else if($type == "auto")
        {
            $cmpSiteData = session('site_data');
            $tmpCmpSiteData = $cmpSiteData;
            $margin = $changeval;
            $site_status_list = session('site_status_list');

            foreach ($cmpSiteData as $key => $value) {
                
                if(array_key_exists($value['site_id'], $site_status_list))
                {
                    if($site_status_list[$value['site_id']] == 0)
                        continue;
                }

                if($value['roi_min'] == 0) continue;

                $siteid = $value['site_name'];
                
                //Block condition
                if($margin / $value['roi_min'] <= 5 && $value['bid_max'] < 0.025 && $value['clicks'] > 10)
                {
                    if(!in_array($siteid, $cmpBlockList))
                    {
                        array_push($cmpBlockList, $siteid);
                    }
                } 

                //Bid amount update condition
                
                if($value['roi_max'] > 30) continue;
                
                $bidValue = $value['bid_max'] / $value['default_bid'];

                if($value['bid_max'] < 0.025)
                {
                    $bidValue = 0.025 / $value['default_bid'];
                } else if($bidValue > 2)
                {
                    $bidValue = 2; 
                }
                
                if($value['clicks'] < 10)
                {
                    if($value['roi_max'] < 0)
                    {
                        $bidValue = 0.025 / $value['default_bid'];
                    } else
                    {
                        if($bidValue > 1.2) $bidValue = 1.2;
                        if($value['bid_max'] < 0.025) $bidValue = 0.025 / $value['default_bid'];
                    }
                }

                $bidValue = round($bidValue, 2);

                $found = array_filter($cmpCstBoost, function($v,$k) use ($siteid){
                    return $v['target'] == $siteid;
                }, ARRAY_FILTER_USE_BOTH); 

                if(sizeof($found) == 0)
                {
                    $curbidValue = 1;
                    
                    if($curbidValue * $value['default_bid'] > 0.05)
                    {
                        $bidValue = 0.5;
                    } else
                    {
                        $bidValue = 0.8;
                    }
                    if($value['default_bid'] * $bidValue < 0.025)
                    {
                        $bidValue = 0.025 / $value['default_bid'];
                    }
                    $bidValue = round($bidValue, 2);

                    array_push($cmpCstBoost, [ "target" => $siteid, "cpc_modification" => $bidValue]);
                    $tmpCmpSiteData[$key]['r_boost'] = $bidValue;
                } else
                {
                    $curbidValue = $value['r_boost'];
                    
                    if($curbidValue * $value['default_bid'] > 0.05)
                    {
                        $bidValue = 1 - 0.5;
                    } else
                    {
                        $bidValue = 1 - 0.2;
                    }
                    if($value['default_bid'] * $bidValue < 0.025)
                    {
                        $bidValue = 0.025 / $value['default_bid'];
                    }
                    $bidValue = round($bidValue, 2);
                    
                    $cmpCstBoost[array_keys($found)[0]]["cpc_modification"] = $bidValue;
                    $tmpCmpSiteData[$key]['r_boost'] = $bidValue;
                }

                // if($value['clicks'] >= 10) 
                // {
                //     //var_dump( $value['site_id'].':'.$value['site_name'].'  '.$value['clicks'].'  ');
                //     if($value['roi_max'] > 0)
                //     {
                //         $bidValue = $value['roi_max'] * $value['bid_actual'] / 100 / $value['default_bid'];
                //         if($value['roi_max'] * $value['bid_actual'] / 100 < 0.025)
                //             $bidValue = 0.025 / $value['default_bid'];
                //     }
                //     else
                //     {
                //         $bidValue = $value['roi_max'] * 60 / 100 / $value['default_bid'];
                //         if($value['roi_max'] * 60 / 100 < 0.025)
                //             $bidValue = 0.025 / $value['default_bid'];
                //     }

                //     //if($value['bid_max'] < 0.025) 
                //     //$bidValue = 0.025 / $value['default_bid'];
                //     $bidValue = round($bidValue, 2);
                //     if($bidValue > 1.3) $bidValue = 1.3;
                //     if($bidValue < 0.7) $bidValue = 0.7;

                //     $found = array_filter($cmpCstBoost, function($v,$k) use ($siteid){
                //         return $v['target'] == $siteid;
                //     }, ARRAY_FILTER_USE_BOTH); 

                //     if(sizeof($found) == 0)
                //     {
                //         // if(1 - $bidValue > 0.1)
                //         // {
                //         //     $bidValue = 0.9;
                //         // }
                //         // if($value['default_bid'] * $bidValue < 0.025) 
                //         // {
                //         //     $bidValue = 0.025 / $value['default_bid'];
                //         //     $bidValue = round($bidValue, 2);
                //         // }
                //         array_push($cmpCstBoost, [ "target" => $siteid, "cpc_modification" => $bidValue]);
                //         $tmpCmpSiteData[$key]['r_boost'] = $bidValue;
                //     } else
                //     {
                //         // if(1 - $bidValue > 0.1)
                //         // {
                //         //     $bidValue = $value['r_boost'] - 0.1;
                //         // }
                //         // if($value['default_bid'] * $bidValue < 0.025) 
                //         // {
                //         //     $bidValue = 0.025 / $value['default_bid'];
                //         //     $bidValue = round($bidValue, 2);
                //         // }
                        
                //         $cmpCstBoost[array_keys($found)[0]]["cpc_modification"] = $bidValue;
                //         $tmpCmpSiteData[$key]['r_boost'] = $bidValue;
                //     }
                // } else if($value['roi_max'] > 0) {    //step by step control
                //     $bidValue = $value['r_boost'] + 0.1;
                //     $bidValue = round($bidValue, 2);
                //     if($bidValue > 1.5) $bidValue = 1.5;

                //     $found = array_filter($cmpCstBoost, function($v,$k) use ($siteid){
                //         return $v['target'] == $siteid;
                //     }, ARRAY_FILTER_USE_BOTH); 

                //     if(sizeof($found) == 0)
                //     {
                //         array_push($cmpCstBoost, [ "target" => $siteid, "cpc_modification" => $bidValue]);
                //     } else
                //     {
                //         $cmpCstBoost[array_keys($found)[0]]["cpc_modification"] = $bidValue;
                //     }
                //     $tmpCmpSiteData[$key]['r_boost'] = $bidValue;
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


            $result = Report::updateTaboolaCampaigns($cmpid, $sendVal);
            //session()->put("site_blocklist", $result['publisher_targeting']['value']);
            session()->put('site_data', $tmpCmpSiteData);
            session()->put("site_cstboost", $result['publisher_bid_modifier']['values']);
        } else if($type == "reset")
        {
            $cmpSiteData = session('site_data');
            $site_status_list = session('site_status_list');

            foreach ($cmpSiteData as $key => $value) {
                
                if(array_key_exists($value['site_id'], $site_status_list))
                {
                    if($site_status_list[$value['site_id']] == 0)
                        continue;
                }

                $siteid = $value['site_name'];

                $found = array_filter($cmpCstBoost, function($v,$k) use ($siteid){
                    return $v['target'] == $siteid;
                }, ARRAY_FILTER_USE_BOTH); 

                if(sizeof($found) > 0) 
                {
                    $cmpCstBoost[array_keys($found)[0]]["cpc_modification"] = 1;
                }
            }
            
            $sendVal =  [      
                "publisher_bid_modifier" => [
                    "values" => $cmpCstBoost
                ] 
            ];

            $result = Report::updateTaboolaCampaigns($cmpid, $sendVal);
            //session()->put("site_blocklist", $result['publisher_targeting']['value']);
            session()->put("site_cstboost", $result['publisher_bid_modifier']['values']);
        } else if($type == "cmpstatus") //Cmp pause & play
        {
            $cmp_id = $siteid;
            $status = true;
            if($changeval == 0) $status = false;
            $sendVal =  [      
                'is_active' => $status
            ];
            Report::updateTaboolaCampaigns($cmp_id, $sendVal);
            $allCmp = session('all_cmp_list');
            $allCmp[$cmp_id]['is_active'] = $status;
            session()->put('all_cmp_list', $allCmp);
        } else if($type == "all_pause")
        {
            $allCmpLst = session('all_cmp_list');
            $cmpLst = session('cmp_data');
            $sendVal =  [      
                'is_active' => false
            ];
            $count = 0;
            foreach($cmpLst as $key => $value)
            {
                if($value['is_active'])
                {
                    Report::updateTaboolaCampaigns($key, $sendVal);
                    $allCmpLst[$key]['is_active'] = false;
                }
                $count++;
            }
            session()->put('all_cmp_list', $allCmpLst);
        }
        return response()->json(['status'=>true]); 
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
    
    public function isSiteBlock($data, $site)
    {
        return in_array($site, $data);
    }

    public function findSiteBoostValue($data, $site)
    {
        $found = array_filter($data, function($v,$k) use ($site){
          return $v['target'] == $site;
        }, ARRAY_FILTER_USE_BOTH); // With latest PHP third parameter is mandatory.. Available Values:- ARRAY_FILTER_USE_BOTH OR ARRAY_FILTER_USE_KEY  

        if(empty($found)) 
            return 1;
        else
            return array_values($found)[0]["cpc_modification"];
    }

    public function findMarginValue($data, $cmpid)
    {
        foreach ($data as $key => $value) {
            if($value->cmpid == $cmpid)
                return $value->value;
        }
        return 30;
    }


    public function findSite($data, $site, $siteid)
    {
        foreach ($data as $key => $value) {
            if(preg_match("/\b$site\b/i", $value[0]) || preg_match("/\b$siteid\b/i", $value[0]))
                return $value;
        }
        return [];   
    }

    public function create()
    {

    }

    public function store()
    {
        
    }

    public function show()
    {
        //
    }

    public function edit()
    {
    }

    public function update()
    {
       
    }

    public function destroy()
    {
       
    }
}
