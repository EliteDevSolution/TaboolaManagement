<?php

namespace DLW\Console\Commands;

use Illuminate\Console\Command;
use DLW\Models\Report;
use Illuminate\Support\Facades\DB;



class AutoInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:autoinit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialization for CronJob.';

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
        
        $run_date = date('Y-m-d');
        $calc_date = date('Y-m-d', strtotime("-1 days"));
        $res = DB::table('currency')->where('id',1)->get();
        $currency = "";
        
        $client_id = env('TO_CLIENT_ID');
        $client_secret = env('TO_CLIENT_SECRET');

        if(sizeof($res) > 0 && $res[0]->type == 1) //currency type manual
        {
            $currency = $res[0]->min_value .':'. $res[0]->max_value;
        } else //currency type auto
        {
            $currency = Report::getCronJobCurrenciesRate('BRL');
        }

        $token = $this->taboolaAccess($client_id, $client_secret);

        DB::table('cron_init')
        ->updateOrInsert(
            ['date' => $run_date],
            ['currency' => $currency, 'token' => $token] 
        );

        $cmpList = $this->getTaboolaCampaigns($token, $calc_date, $calc_date);

        $index = 0;
        $insertData = [];
        foreach ($cmpList['results'] as $key => $value) {
            if($value['clicks'] == 0 && $value['spent'] == 0)
                continue;
            array_push($insertData, ['cmpid' => $value['campaign'], 'flag' => 0]);
        }
        DB::table('cron_campaign')->truncate();
        DB::table('cron_campaign')->insert($insertData);
        $this->info('CronInit has been send successfully');
    }


    public function taboolaAccess($to_client_id, $to_client_secret)
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
        return $access_token;
    }

    public function getTaboolaCampaigns($access_token, $start_date, $end_date)
    {
        $account_id = env('TO_ACCOUNT_NAME');
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
}
