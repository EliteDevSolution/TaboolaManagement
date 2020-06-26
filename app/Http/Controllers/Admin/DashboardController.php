<?php

namespace DLW\Http\Controllers\Admin;

use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use DLW\Libraries\GoogleAnalytics;
use DLW\Models\User;
use DLW\Models\Score;

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

        $allUserCount = User::all()->count();
        $newUserCount = User::newUserCountRecently(1);
        $time_tool = GoogleAnalytics::getTimeTool();
        $allCampaigns = GoogleAnalytics::getCampaigns();
        $time_reference = GoogleAnalytics::getTimeReference();
        $activeUsers = GoogleAnalytics::activeUsersNow();
        $activePages = GoogleAnalytics::activePagesNow();
        $topDevices = GoogleAnalytics::topDevices();
        $rsCountry = Score::rankByCountry();
        $rsActivity = Score::rankByActivity();
        $rsStore = Score::rankByStore();
        $anaUsers = GoogleAnalytics::analyticUsers();
        $returnUsers = GoogleAnalytics::returnUsers();
        $users_country = GoogleAnalytics::usersCountry();
        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'allusercount' => $allUserCount,
            'newusercount' => $newUserCount,
            'time_tool' => $time_tool,
            'time_reference' => $time_reference,
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
        ]);
    }

    public function precho($val)
    {
        echo  "<html><body><pre>";
        var_dump($val);
        echo  "</pre></body></html>";
        exit(0);
    }
}
