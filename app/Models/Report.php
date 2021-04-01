<?php

namespace DLW\Models;

use DLW\Models\Deposit;
use Illuminate\Database\Eloquent\Model;
use DLW\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;


class Report extends Model
{
    static function getCurrenciesRate($symbol)
    {

        session()->put('cur_currency', $symbol);
        $currencyRate = session('currency_'.$symbol);//If  currency rate value session to exist.
        if(isset($currencyRate) && $currencyRate != '')
        {
            return $currencyRate;
        }

        $curl = curl_init();
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-6 months"));
        //$currency_url = "https://api.exchangeratesapi.io/history?start_at=$start_date&end_at=$end_date&base=USD&symbols=$symbol&access_key=4c9644c15ae88888642382c316e6e395&format=1";
        $currency_url = "http://api.exchangeratesapi.io/v1/latest?access_key=4c9644c15ae88888642382c316e6e395&format=1";
        curl_setopt_array($curl, array(
            CURLOPT_URL => $currency_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 100,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));
        $result = curl_exec($curl);
        $response = json_decode($result, true);
        $arrayVal = [];
//        foreach($response['rates'] ?? [] as $value)
//        {
//            $arrayVal[] = floor($value[$symbol]);
//        }
//        $minVal = min($arrayVal) - 0.5;
//        $maxVal = array_sum($arrayVal) / count($response['rates']);
        $minVal = $response['rates'][$symbol] - 0.5 ?? 4.2;
        $maxVal = $response['rates'][$symbol] ?? 4.2;
        session()->put('currency_'.$symbol, round($minVal,3));
        session()->put('currency_max_'.$symbol, round($maxVal,3));
        return round($minVal, 3);
    }

    static function getTaboolaTrendTitleScore($title)
    {
        $url = "https://trends.taboola.com/algo/test-strings";

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
            CURLOPT_POSTFIELDS => json_encode([ 'string_a' => $title, 'string_b' => '.'])
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result, true);
        return $response;
    }


    static function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    static function getLastestCurrencyRate()
    {
        session()->put('cur_lastest_currency_BRL');
        $currencyRate = session('cur_lastest_currency_BRL');//If  currency rate value session to exist.
        if(isset($currencyRate) && $currencyRate != '')
        {
            return $currencyRate;
        }
//        $curl = curl_init();
//        $currency_url = "https://free.currconv.com/api/v7/convert?q=USD_BRL&compact=ultra&apiKey=296db89cc9e18eb55ead&lastest";
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => $currency_url,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_SSL_VERIFYHOST => 0,
//            CURLOPT_SSL_VERIFYPEER => 0,
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 100,
//            CURLOPT_CUSTOMREQUEST => "GET"
//        ));
//        $result = curl_exec($curl);
//        $response = json_decode($result, true);
        session()->put('cur_lastest_currency_BRL', 4.2);
        return round(4.2, 2);
    }

    static function getCronJobCurrenciesRate($symbol)
    {
        session()->put('cur_currency', $symbol);
        $currencyRate = session('currency_'.$symbol);//If  currency rate value session to exist.
        if(isset($currencyRate) && $currencyRate != '')
        {
            return $currencyRate;
        }

        $curl = curl_init();
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-6 months"));
        $currency_url = "https://api.exchangeratesapi.io/history?start_at=$start_date&end_at=$end_date&base=USD&symbols=$symbol";
        curl_setopt_array($curl, array(
            CURLOPT_URL => $currency_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 100,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));
        $result = curl_exec($curl);
        $response = json_decode($result, true);
        $arrayVal = [];
        foreach($response['rates'] as $value)
        {
            $arrayVal[] = floor($value[$symbol]);
        }
        $minVal = min($arrayVal) - 0.5;
        $maxVal = array_sum($arrayVal) / count($response['rates']);
        return round($minVal,3) . ':' . round($maxVal,3);
    }

    static function getWordPressPost($url)
    {
        $curl = curl_init();
        $url .= '/wp-json/custom/v2/all-posts';

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
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result, true);
        return $response;
    }


    static function getTaboolaCampaignValue($cmp_id)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
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

    static function getTaboolaAllCampaign()
    {
        $account_id = session('account_name');
        $access_token = session('access_token');

        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns";

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

    static function getPaymentTransactionHistory($type, $start_date, $end_date)
    {


        $user_id = Auth::guard('admin')->user()->id;
        $allPayList = session()->get('all_pay_day_list');
        $depositList = Deposit::where(['user_id' => $user_id])->orderBy('made_date', 'desc')->get()->toArray();
        if($type == __('globals.payment_history.day'))
        {
            $preTotalDeposit = Deposit::where([[ 'user_id', '=', $user_id ], [ 'made_date', '<', $start_date ]])->sum('amount');
            $preTotalSpent = 0;
            $finalRes = [];

            foreach ($allPayList as $row)
            {
                $resDate = [];
                $curDate = substr($row['date'], 0, 10);
                if($curDate > $end_date) break;
                if($curDate < $start_date)
                {
                    $preTotalSpent += $row['spent'];
                } else {
                    $found = array_filter($depositList, function($v,$k) use ($curDate){
                        return $v['made_date'] == $curDate;
                    }, ARRAY_FILTER_USE_BOTH);

                    $resDate['date'] = $curDate;
                    $resDate['spent'] = $row['spent'];
                    if(sizeof($found) > 0)
                    {
                        $resDate['deposit'] = $depositList[array_keys($found)[0]]['amount'];
                        $resDate['description'] = $depositList[array_keys($found)[0]]['description'];
                    } else
                    {
                        $resDate['deposit'] = 0;
                        $resDate['description'] = '';
                    }

                    $finalRes[] = $resDate;
                }
            }
            $calDate = date_create_from_format('Y-m-d', $start_date);
            $preDate = date_format(date_modify($calDate, '-1 day'), 'Y-m-d');
            $preBalance = $preTotalDeposit - $preTotalSpent;
            return ['pre_total_deposit' => $preTotalDeposit, 'pre_total_spent' => $preTotalSpent, 'pre_balance' => $preBalance, 'final_res' => $finalRes, 'pre_date' => $preDate];

        } else if($type == __('globals.payment_history.week'))
        {
            $start_date = self::getWeekDateArray($start_date)[0];
            $end_date = self::getWeekDateArray($end_date)[6];
            $preTotalDeposit = Deposit::where([[ 'user_id', '=', $user_id ], [ 'made_date', '<', $start_date ]])->sum('amount');
            $preTotalSpent = 0;
            $finalRes = [];
            $innerSpentTotal = 0;
            $innerDepositTotal = 0;
            foreach ($allPayList as $row)
            {
                $resDate = [];
                $curDate = substr($row['date'], 0, 10);
                if($curDate > $end_date) break;
                if($curDate < $start_date)
                {
                    $preTotalSpent += $row['spent'];
                } else {
                    $found = array_filter($depositList, function($v,$k) use ($curDate){
                        return $v['made_date'] == $curDate;
                    }, ARRAY_FILTER_USE_BOTH);

                    $dayOfWeek = date("l", strtotime($curDate));
                    $innerSpentTotal += $row['spent'];
                    if(sizeof($found) > 0)
                        $innerDepositTotal +=  $depositList[array_keys($found)[0]]['amount'];

                    if($dayOfWeek === 'Saturday')
                    {
                        $resDate['date'] = $curDate;
                        if(sizeof($found) > 0)
                        {
                            $resDate['description'] = $depositList[array_keys($found)[0]]['description'];
                        } else
                        {
                            $resDate['description'] = '';
                        }
                        $resDate['spent'] = $innerSpentTotal;
                        $resDate['deposit'] = $innerDepositTotal;
                        $innerSpentTotal = 0;
                        $innerDepositTotal = 0;
                        $finalRes[] = $resDate;
                    }
                }
            }
            $preBalance = $preTotalDeposit - $preTotalSpent;
            $calDate = date_create_from_format('Y-m-d', $start_date);
            $preDate = date_format(date_modify($calDate, '-1 day'), 'Y-m-d');
            return ['pre_total_deposit' => $preTotalDeposit, 'pre_total_spent' => $preTotalSpent, 'pre_balance' => $preBalance, 'final_res' => $finalRes, 'pre_date' => $preDate];

        } else if($type == __('globals.payment_history.month')) {
            $curYear = date('Y');
            $leafYear = date('L');
            $start_date = "$curYear-01-01";
            $end_date = "$curYear-12-31";
            $preTotalDeposit = Deposit::where([[ 'user_id', '=', $user_id ], [ 'made_date', '<', $start_date ]])->sum('amount');
            $preTotalSpent = 0;
            $finalRes = [];
            $innerSpentTotal = 0;
            $innerDepositTotal = 0;
            foreach ($allPayList as $row)
            {
                $payDate = substr($row['date'], 0, 10);
                if($payDate < $start_date)
                {
                    $preTotalSpent += $row['spent'];
                } else
                {
                    break;
                }
            }

            for ($iMonth = 1; $iMonth < 13; $iMonth ++)
            {
                $curMonth = $iMonth;
                if(strlen($curMonth) == 1) $curMonth = '0'.$curMonth;
                $foundlist = array_filter($allPayList, function($v,$k) use ($curYear, $curMonth){
                    $curYearMonth = $curYear.'-'.$curMonth;
                    return preg_match("/\b$curYearMonth\b/i", $v['date']);
                }, ARRAY_FILTER_USE_BOTH);
                $innerSpentTotal = 0;
                $innerDepositTotal = 0;
                foreach ($foundlist as $row)
                {
                    $curDate = substr($row['date'], 0, 10);

                    $found = array_filter($depositList, function($v,$k) use ($curDate){
                        return $v['made_date'] == $curDate;
                    }, ARRAY_FILTER_USE_BOTH);

                    $innerSpentTotal += $row['spent'];

                    if(sizeof($found) > 0)
                    {
                        $innerDepositTotal +=  $depositList[array_keys($found)[0]]['amount'];
                    }
                }
                $resDate['date'] = $curYear.'-'.$curMonth;
                $resDate['description'] = '';
                $resDate['spent'] = $innerSpentTotal;
                $resDate['deposit'] = $innerDepositTotal;
                $finalRes[] = $resDate;
            }

            $calDate = date_create_from_format('Y-m-d', $start_date);
            $preDate = date_format(date_modify($calDate, '-1 day'), 'Y-m-d');
            $preBalance = $preTotalDeposit - $preTotalSpent;
            return ['pre_total_deposit' => $preTotalDeposit, 'pre_total_spent' => $preTotalSpent, 'pre_balance' => $preBalance, 'final_res' => $finalRes, 'pre_date' => $preDate];
        }
    }

    static function getWeekDateArray($curDate)
    {
        $day_of_week = date('N', strtotime($curDate));

        $given_date = strtotime($curDate);

        $first_of_week =  date('Y-m-d', strtotime("- {$day_of_week} day", $given_date));

        $first_of_week = strtotime($first_of_week);

        for($i=0 ;$i<=7; $i++) {
            $week_array[] = date('Y-m-d', strtotime("+ {$i} day", $first_of_week));
        }
        return $week_array;
    }

    static function getCurlRealFilePath($filename, $contentType, $postname)
    {
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $contentType, $postname);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename};filename=" . $postname;
        if ($contentType) {
            $value .= ';type=' . $contentType;
        }

        return $value;
    }

    static function getTaboolaCampaignAds($id)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/$id/items";

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

    static function getTaboolaSites($cmp_id, $start_date, $end_date)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
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

    static function getTaboolaAllSites($start_date, $end_date, $attach_condition = '')
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/reports/campaign-summary/dimensions/site_breakdown?start_date=$start_date&end_date=$end_date$attach_condition";

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

    static function getTaboolaCampaigns($start_date, $end_date)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/reports/campaign-summary/dimensions/campaign_breakdown?start_date=$start_date&end_date=$end_date";

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

    static function getTaboolaCampaignsDay($start_date, $end_date)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/reports/campaign-summary/dimensions/campaign_day_breakdown?start_date=$start_date&end_date=$end_date";

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

    static function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
    }

    static function getTaboolaDays($start_date, $end_date)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/reports/campaign-summary/dimensions/day?start_date=$start_date&end_date=$end_date";

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

    static function getTotalBalance($payhistory)
    {
        $spentTotal = 0;
        $depositTotal = 0;
        $user_id = Auth::guard('admin')->user()->id;
        foreach ($payhistory as $row)
        {
            $spentTotal += $row['spent'];
        }
        $depositTotal = Deposit::where([ 'user_id' => $user_id ])->sum('amount');
        return $depositTotal - $spentTotal;
    }


    static function getTaboolaByHours($start_date, $end_date)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/reports/campaign-summary/dimensions/by_hour_of_day?start_date=$start_date" . "T00:00:00&end_date=$end_date"."T23:00:00";

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

    static function createTaboolaCampaigns($value)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/";

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

    static function duplicateTaboolaCampaigns($id, $value)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/$id/duplicate";

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

    static function updateTaboolaCampaigns($id, $value)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
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

    static function getAccountLevelAllSiteList()
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/block-publisher";

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

    static function patchTaboolaCampaigns($id, $value)
    {

        $account_id = session('account_name');
        $access_token = session('access_token');
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
            CURLOPT_CUSTOMREQUEST => "PATCH",
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

    static function patchTaboolaSite($value)
    {

        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/block-publisher";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PATCH",
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



    static function createTaboolaAds($id, $value)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/$id/items";

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

    static function massTaboolaAdsInsert($id, $value)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/$id/items/mass";

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

    static function getTopTaboolaCampaignAds($cmpid, $start_date, $end_date)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $cmpIdCondition = '';
        if($cmpid != 'all') $cmpIdCondition = "&campaign=$cmpid";
        $url = "$base_url/api/1.0/$account_id/reports/top-campaign-content/dimensions/item_breakdown?start_date=$start_date" . "&end_date=$end_date" . $cmpIdCondition;

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


    static function removeTaboolaCampaignAds($cmpid, $adsid)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/$cmpid/items/$adsid";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
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

    static function updateTaboolaCampaignAds($cmpid, $adsid, $value)
    {
        $account_id = session('account_name');
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/$cmpid/items/$adsid";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
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

    static function uploadTaboolaThumbnail($cfile)
    {
        $access_token = session('access_token');
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/operations/upload-image";
        $data = ['file' => $cfile];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true, //Request header
            CURLOPT_HEADER => true, //Return header
            CURLOPT_SSL_VERIFYPEER => true, //Don't veryify server certificate
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $access_token",
                "Content-Type: multipart/form-data"
            )
        ));

        $result = curl_exec($curl);
        $header_info = curl_getinfo($curl,CURLINFO_HEADER_OUT);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);
        curl_close($curl);
        $response = json_decode($body, true);
        return $response;
    }
}
