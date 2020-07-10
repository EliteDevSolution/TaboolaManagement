<?php

namespace DLW\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    //
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
        session()->put('currency_'.$symbol, round($minVal,3));
        session()->put('currency_max_'.$symbol, round($maxVal,3));
        return round($minVal, 3);
    }

    static function getLastestCurrencyRate()
    {
        session()->put('cur_lastest_currency_BRL');
        $currencyRate = session('cur_lastest_currency_BRL');//If  currency rate value session to exist.
        if(isset($currencyRate) && $currencyRate != '')
        {
            return $currencyRate;
        }
        $curl = curl_init();
        $currency_url = "https://free.currconv.com/api/v7/convert?q=USD_BRL&compact=ultra&apiKey=296db89cc9e18eb55ead&lastest";
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
        session()->put('cur_lastest_currency_BRL', $response["USD_BRL"]);
        return round($response["USD_BRL"], 2);
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
    
}
