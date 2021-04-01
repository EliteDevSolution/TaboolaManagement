<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Models\Report;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public $weekdays;
    public $timezone;


    public function __construct()
    {
        $this->middleware('admin.guard');
        $this->weekdays = ['mon' => __('globals.campaigns.mon'), 'tue' => __('globals.campaigns.tue'), 'wed' => __('globals.campaigns.wed'), 'thu' => __('globals.campaigns.thu'),
            'fri' => __('globals.campaigns.fri'), 'sat' => __('globals.campaigns.sat'), 'sun' => __('globals.campaigns.sun')];

        $this->timezone = ['US/Eastern', 'Israel', 'US/Pacific', 'US/Mountain', 'US/Central', 'Europe/Paris', 'Europe/Berlin', 'Europe/London',
            'Australia/Melbourne', 'Europe/Warsaw', 'Asia/Calcutta', 'Africa/Nairobi', 'Asia/Bangkok', 'Asia/Karachi', 'UTC', 'Asia/Tokyo',
            'Asia/Singapore', 'Asia/Manila', 'Asia/Kuala Lumpur', 'Asia/Shanghai', 'Europe/Moscow', 'Europe/Rome', 'America/Bogota',
            'America/Mexico_City', 'America/Sao_Paulo', 'Asia/Seoul', 'Pacific/Auckland', 'Europe/Athens', 'Europe/Madrid', 'Asia/Dubai'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(sizeof(session('permissions')) > 0 && session('permissions')['campaign_management_page'] == 0)
        {
            return abort( 404);
        }

        //$allCampaigns = Report::getTaboolaAllCampaign()['results'] ?? [];
        $allCampaigns = session()->get('all_cmp_ads_list');
        ////////////// All Campaign Puase Balance < 100 Beta //////////////////////////////////
        if(session()->get('cur_balance') < 100 && Auth::guard('admin')->user()->id !== 1)
        {
            $sendVal =  [
                'is_active' => false
            ];
            foreach($allCampaigns as $key => $value)
            {
                if($value['is_active'])
                {
                    $allCampaigns[$key] = Report::updateTaboolaCampaigns($value['id'], $sendVal);
                }
            }
        }
        /////////////////////////////////////////////////////////////////////////////
        session()->put('all_cmp_ads_list', $allCampaigns);
        return view('admin.campaign.index', ['title'=>__('globals.campaigns.campaign_management'), 'allCampaigns' => $allCampaigns]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if((sizeof(session('permissions')) > 0 && session('permissions')['campaign_management_page'] == 0) || session()->get('cur_balance') < 100)
        {
            if(Auth::guard('admin')->user()->id !== 1)
                return abort( 404);
        }
        $all_sitelist = session()->get('all_sites');
        if(!isset($all_sitelist))
        {
            $all_sitelist = Report::getTaboolaAllSites('2019-01-01', date('Y-m-d'))['results'];
            session()->put('all_sites', $all_sitelist);
        }
        return view('admin.campaign.create', ['title' => __('globals.campaigns.new_campaign'), 'weekdays' => $this->weekdays, 'timezone' => $this->timezone, 'all_sites' => $all_sitelist]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $location_opt = $request->location_option;
        $countires = $request->countries;
        $country_targeting = [
            "type" => "INCLUDE",
            "value" => $countires,
            "href" => null
        ];
        $target_sites = [];
        if($request->has('target_sites'))
        {
            $target_sites = $request->target_sites;
        }

        $target_sites_val =  [
            "type" => "ALL"
        ];

        if(sizeof($target_sites) > 0)
        {
            $target_sites_val =  [
                "type" => "EXCLUDE",
                "value" => array_values($target_sites)
            ];
        }

        if($location_opt === 'all_location')
        {
            $request->validate(
                [
                    'name' => 'required',
                    'branding_text' => 'required',
                    'bid_amount' => 'required|numeric|min:0.055',
                    'spend_limit' => 'required|numeric',
                    'daily_cap' => 'required|numeric',
                    'tracking_code' => 'required'
                ]
            );
            $country_targeting = [
                "type" => "ALL",
            ];
        } else
        {

            $request->validate(
                [
                    'name' => 'required',
                    'branding_text' => 'required',
                    'countries'  => 'required',
                    'bid_amount' => 'required|numeric|min:0.055',
                    'spend_limit' => 'required|numeric',
                    'daily_cap' => 'required|numeric',
                    'tracking_code' => 'required'
                ]
            );

        }

        $name = $request->name;
        $branding_text = $request->branding_text;
        $schedule_opt = $request->schedule_options;
        $plat_desk = $request->plat_desktop ?? 'off';
        $plat_smartphone = $request->plat_smartphone ?? 'off';
        $plat_tablet = $request->plat_tablet ?? 'off';
        $bid_amount = $request->bid_amount;
        $spending_limit = $request->spend_limit;
        $daily_cap = $request->daily_cap;
        $tracking_code = $request->tracking_code;
        $timezone = $request->timezone;

        if(Auth::guard('admin')->user()->id !== 1)
        {
            $tracking_code = "utm_source={campaign_name}&utm_medium={site}&utm_campaign={title}&utm_term={thumbnail}&utm_content={campaign_id}";
        }


        $platform_targeting = [];
        $platform_list = ['PHON' => $plat_smartphone, 'DESK' => $plat_desk, 'TBLT' => $plat_tablet];
        $sch_weeks = [
            'MONDAY' => [ $request->mon_rule, $request->mon_from ?? 0, $request->mon_until ?? 24 ],
            'TUESDAY' => [ $request->tue_rule, $request->tue_from ?? 0, $request->tue_until ?? 24 ],
            'WEDNESDAY' => [ $request->wed_rule, $request->wed_from ?? 0, $request->wed_until ?? 24 ],
            'THURSDAY' => [ $request->thu_rule, $request->thu_from ?? 0, $request->thu_until ?? 24 ],
            'FRIDAY' => [ $request->fri_rule, $request->fri_from ?? 0, $request->fri_until ?? 24 ],
            'SATURDAY' => [ $request->sat_rule, $request->sat_from ?? 0, $request->sat_until ?? 24 ],
            'SUNDAY' => [ $request->sun_rule, $request->sun_from ?? 0, $request->sun_until ?? 24 ],
        ];

        if($plat_desk == 'on' && $plat_smartphone == 'on' && $plat_tablet == 'on')
        {
            $platform_targeting = [
                "type" => "INCLUDE",
                "value" => ['PHON', 'DESK', 'TBLT'],
                "href" => null
            ];
        } else {
            $platforms = [];
            foreach($platform_list as $key => $val) {
                if($val === 'off') continue;
                $platforms[] = $key;
            }
            if(sizeof($platforms) === 0 || sizeof($platforms) === 3)
            {
                $platform_targeting = [
                    "type" => "ALL",
                ];
            } else
            {
                $platform_targeting = [
                    "type" => "INCLUDE",
                    "value" => $platforms,
                    "href" => null
                ];
            }
        }

        $activity_schedule = [];
        if($schedule_opt === '24_7')
        {
            $activity_schedule = [
                "mode" => "ALWAYS",
                "rules" => [],
                "time_zone" => "America/Sao_Paulo"
            ];
        } else
        {
            $rules = [];
            foreach ($sch_weeks as $key => $value)
            {
                if($value[0] === 'ALL_DAY') continue;
                if($value[0] === 'EXCLUDE_THIS')
                {
                    $rules[] = [
                        "type" => "EXCLUDE",
                        "day" => $key,
                        "from_hour" => 0,
                        "until_hour" => 24,
                    ];
                } else if($value[0] === 'SPECIFIC_HOURS')
                {
                    $rules[] = [
                        "type" => "INCLUDE",
                        "day" => $key,
                        "from_hour" => $value[1],
                        "until_hour" => $value[2],
                    ];
                }
            }

            $activity_schedule = [
                "mode" => "CUSTOM",
                "rules" => $rules,
                "time_zone" => $timezone
            ];
        }

        $createValue = [
            "name" => $name,
            "branding_text" => $branding_text,
            "cpc" => $bid_amount,
            "spending_limit" => $spending_limit,
            "spending_limit_model" => "MONTHLY",
            "marketing_objective" => "DRIVE_WEBSITE_TRAFFIC",
            "daily_cap" => $daily_cap,
            "bid_strategy" => "FIXED",
            "daily_ad_delivery_model" => "STRICT",
            "traffic_allocation_mode" => "OPTIMIZED",
            "country_targeting" => $country_targeting,
            "publisher_targeting" => $target_sites_val,
            "tracking_code" => $tracking_code,
            "pricing_model" => "CPC",
            'platform_targeting' => $platform_targeting,
            'start_date' => date('Y-m-d', strtotime("+1 days")),
            'activity_schedule' => $activity_schedule
        ];

        $res = Report::createTaboolaCampaigns($createValue);

        if(array_key_exists('http_status', $res))
        {
            if($res['http_status'] === 400)
                return redirect()->route('campaigns.index')->with('error', $res['message']);
        } else
        {
            $allCmp = session()->get('all_cmp_ads_list');
            array_push($allCmp, $res);
            session()->put('all_cmp_ads_list', $allCmp);
            return redirect()->route('campaigns.index')->with('success', __('globals.msg.save_success'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $res = [];
        $allCmp = session()->get('all_cmp_ads_list');
        foreach ($allCmp   as $key => $item) {
            if($item['id'] == $id)
            {
                $res = $item;
                break;
            }
        }

        $all_sitelist = session()->get('all_sites');
        if(!isset($all_sitelist))
        {
            $all_sitelist = Report::getTaboolaAllSites('2019-01-01', date('Y-m-d'))['results'];
            session()->put('all_sites', $all_sitelist);
        }

        $block_list = $res['publisher_targeting']['value'] ?? [];
        $block_sites = [];
        foreach ($block_list as $row)
        {
            $found = array_filter($all_sitelist, function($v,$k) use ($row){
                return $v['site'] == $row;
            }, ARRAY_FILTER_USE_BOTH);
            if(sizeof($found) > 0)
            {
                array_push($block_sites, $all_sitelist[array_keys($found)[0]]);
            }
        }

        return view('admin.campaign.edit', ['title' => __('globals.campaigns.edit_campaign') . ' - ' . $id, 'weekdays' => $this->weekdays, 'timezone' => $this->timezone, 'result' => $res, 'all_sites' => $all_sitelist, 'block_sites' => $block_sites]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDuplicate($id)
    {
        if((sizeof(session('permissions')) > 0 && session('permissions')['campaign_management_page'] == 0) || session()->get('cur_balance') < 100)
        {
            if(Auth::guard('admin')->user()->id !== 1)
                return abort( 404);
        }
        $res = [];
        $allCmp = session()->get('all_cmp_ads_list');
        foreach ($allCmp   as $key => $item) {
            if($item['id'] == $id)
            {
                $res = $item;
                break;
            }
        }

        $all_sitelist = session()->get('all_sites');
        if(!isset($all_sitelist))
        {
            $all_sitelist = Report::getTaboolaAllSites('2018-01-01', date('Y-m-d'))['results'];
            session()->put('all_sites', $all_sitelist);
        }

        $block_list = $res['publisher_targeting']['value'] ?? [];
        $block_sites = [];
        foreach ($block_list as $row)
        {
            $found = array_filter($all_sitelist, function($v,$k) use ($row){
                return $v['site'] == $row;
            }, ARRAY_FILTER_USE_BOTH);
            if(sizeof($found) > 0)
            {
                array_push($block_sites, $all_sitelist[array_keys($found)[0]]);
            }
        }

        return view('admin.campaign.deplicate', ['title' => __('globals.campaigns.duplicate_title_campaign'), 'weekdays' => $this->weekdays, 'timezone' => $this->timezone, 'result' => $res, 'all_sites' => $all_sitelist, 'block_sites' => $block_sites]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $location_opt = $request->location_option;
        $countires = $request->countries;
        $country_targeting = [
            "type" => "INCLUDE",
            "value" => $countires,
            "href" => null
        ];

        $target_sites = [];
        if($request->has('target_sites'))
        {
            $target_sites = $request->target_sites;
        }

        $target_sites_val =  [
            "type" => "ALL"
        ];

        if(sizeof($target_sites) > 0)
        {
            $target_sites_val =  [
                "type" => "EXCLUDE",
                "value" => array_values($target_sites)
            ];
        }

        if($location_opt === 'all_location')
        {
            $request->validate(
                [
                    'name' => 'required',
                    'branding_text' => 'required',
                    'bid_amount' => 'required|numeric|min:0.055',
                    'spend_limit' => 'required|numeric',
                    'daily_cap' => 'required|numeric',
                    'tracking_code' => 'required'
                ]
            );
            $country_targeting = [
                "type" => "ALL",
            ];
        } else
        {
            $request->validate(
                [
                    'name' => 'required',
                    'branding_text' => 'required',
                    'countries'  => 'required',
                    'bid_amount' => 'required|numeric|min:0.055',
                    'spend_limit' => 'required|numeric',
                    'daily_cap' => 'required|numeric',
                    'tracking_code' => 'required'
                ]
            );
        }

        $name = $request->name;
        $branding_text = $request->branding_text;
        $schedule_opt = $request->schedule_options;
        $plat_desk = $request->plat_desktop ?? 'off';
        $plat_smartphone = $request->plat_smartphone ?? 'off';
        $plat_tablet = $request->plat_tablet ?? 'off';
        $bid_amount = $request->bid_amount;
        $spending_limit = $request->spend_limit;
        $daily_cap = $request->daily_cap;
        $tracking_code = $request->tracking_code;
        $timezone = $request->timezone;

        if(Auth::guard('admin')->user()->id !== 1)
        {
            $tracking_code = "utm_source={campaign_name}&utm_medium={site}&utm_campaign={title}&utm_term={thumbnail}&utm_content={campaign_id}";
        }

        $platform_targeting = [];
        $platform_list = ['PHON' => $plat_smartphone, 'DESK' => $plat_desk, 'TBLT' => $plat_tablet];
        $sch_weeks = [
            'MONDAY' => [ $request->mon_rule, $request->mon_from ?? 0, $request->mon_until ?? 24 ],
            'TUESDAY' => [ $request->tue_rule, $request->tue_from ?? 0, $request->tue_until ?? 24 ],
            'WEDNESDAY' => [ $request->wed_rule, $request->wed_from ?? 0, $request->wed_until ?? 24 ],
            'THURSDAY' => [ $request->thu_rule, $request->thu_from ?? 0, $request->thu_until ?? 24 ],
            'FRIDAY' => [ $request->fri_rule, $request->fri_from ?? 0, $request->fri_until ?? 24 ],
            'SATURDAY' => [ $request->sat_rule, $request->sat_from ?? 0, $request->sat_until ?? 24 ],
            'SUNDAY' => [ $request->sun_rule, $request->sun_from ?? 0, $request->sun_until ?? 24 ],
        ];

        if($plat_desk == 'on' && $plat_smartphone == 'on' && $plat_tablet == 'on')
        {
            $platform_targeting = [
                "type" => "INCLUDE",
                "value" => ['PHON', 'DESK', 'TBLT'],
                "href" => null
            ];
        } else {
            $platforms = [];
            foreach($platform_list as $key => $val) {
                if($val === 'off') continue;
                $platforms[] = $key;
            }
            if(sizeof($platforms) === 0 || sizeof($platforms) === 3)
            {
                $platform_targeting = [
                    "type" => "ALL",
                ];
            } else
            {
                $platform_targeting = [
                    "type" => "INCLUDE",
                    "value" => $platforms,
                    "href" => null
                ];
            }
        }

        $activity_schedule = [];
        if($schedule_opt === '24_7')
        {
            $activity_schedule = [
                "mode" => "ALWAYS",
                "rules" => [],
                "time_zone" => "America/Sao_Paulo"
            ];
        } else
        {
            $rules = [];
            foreach ($sch_weeks as $key => $value)
            {
                if($value[0] === 'ALL_DAY') continue;
                if($value[0] === 'EXCLUDE_THIS')
                {
                    $rules[] = [
                        "type" => "EXCLUDE",
                        "day" => $key,
                        "from_hour" => 0,
                        "until_hour" => 24,
                    ];
                } else if($value[0] === 'SPECIFIC_HOURS')
                {
                    $rules[] = [
                        "type" => "INCLUDE",
                        "day" => $key,
                        "from_hour" => $value[1],
                        "until_hour" => $value[2],
                    ];
                }
            }

            $activity_schedule = [
                "mode" => "CUSTOM",
                "rules" => $rules,
                "time_zone" => $timezone
            ];
        }

        $createValue = [
            "name" => $name,
            "branding_text" => $branding_text,
            "cpc" => $bid_amount,
            "spending_limit" => $spending_limit,
            "spending_limit_model" => "MONTHLY",
            "marketing_objective" => "DRIVE_WEBSITE_TRAFFIC",
            "daily_cap" => $daily_cap,
            "bid_strategy" => "FIXED",
            "daily_ad_delivery_model" => "STRICT",
            "traffic_allocation_mode" => "OPTIMIZED",
            "country_targeting" => $country_targeting,
            "publisher_targeting" => $target_sites_val,
            "tracking_code" => $tracking_code,
            "pricing_model" => "CPC",
            'platform_targeting' => $platform_targeting,
            'start_date' => date('Y-m-d', strtotime("+1 days")),
            'activity_schedule' => $activity_schedule
        ];

        $res = Report::duplicateTaboolaCampaigns($id, $createValue);

        if(array_key_exists('http_status', $res))
        {
            if($res['http_status'] === 400)
                return redirect()->route('campaigns.index')->with('error', $res['message']);
        } else
        {
            $allCmp = session()->get('all_cmp_ads_list');
            array_push($allCmp, $res);
            session()->put('all_cmp_ads_list', $allCmp);
            return redirect()->route('campaigns.index')->with('success', __('globals.msg.save_success'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $location_opt = $request->location_option;
        $countires = $request->countries;
        $country_targeting = [
            "type" => "INCLUDE",
            "value" => $countires,
            "href" => null
        ];
        $target_sites = [];
        if($request->has('target_sites'))
        {
            $target_sites = $request->target_sites;
        }

        $target_sites_val =  [
            "type" => "ALL"
        ];

        if(sizeof($target_sites) > 0)
        {
            $target_sites_val =  [
                "type" => "EXCLUDE",
                "value" => array_values($target_sites)
            ];
        }

        if($location_opt === 'all_location')
        {
            $request->validate(
                [
                    'name' => 'required',
                    'branding_text' => 'required',
                    'bid_amount' => 'required|numeric|min:0.055',
                    'spend_limit' => 'required|numeric',
                    'daily_cap' => 'required|numeric',
                    'tracking_code' => 'required'
                ]
            );
            $country_targeting = [
                "type" => "ALL",
            ];
        } else
        {
            $request->validate(
                [
                    'name' => 'required',
                    'branding_text' => 'required',
                    'countries'  => 'required',
                    'bid_amount' => 'required|numeric|min:0.055',
                    'spend_limit' => 'required|numeric',
                    'daily_cap' => 'required|numeric',
                    'tracking_code' => 'required'
                ]
            );
        }

        $name = $request->name;
        $branding_text = $request->branding_text;
        $schedule_opt = $request->schedule_options;
        $plat_desk = $request->plat_desktop ?? 'off';
        $plat_smartphone = $request->plat_smartphone ?? 'off';
        $plat_tablet = $request->plat_tablet ?? 'off';
        $bid_amount = $request->bid_amount;
        $spending_limit = $request->spend_limit;
        $daily_cap = $request->daily_cap;
        $tracking_code = $request->tracking_code;
        $timezone = $request->timezone;



        $platform_targeting = [];
        $platform_list = ['PHON' => $plat_smartphone, 'DESK' => $plat_desk, 'TBLT' => $plat_tablet];
        $sch_weeks = [
            'MONDAY' => [ $request->mon_rule, $request->mon_from ?? 0, $request->mon_until ?? 24 ],
            'TUESDAY' => [ $request->tue_rule, $request->tue_from ?? 0, $request->tue_until ?? 24 ],
            'WEDNESDAY' => [ $request->wed_rule, $request->wed_from ?? 0, $request->wed_until ?? 24 ],
            'THURSDAY' => [ $request->thu_rule, $request->thu_from ?? 0, $request->thu_until ?? 24 ],
            'FRIDAY' => [ $request->fri_rule, $request->fri_from ?? 0, $request->fri_until ?? 24 ],
            'SATURDAY' => [ $request->sat_rule, $request->sat_from ?? 0, $request->sat_until ?? 24 ],
            'SUNDAY' => [ $request->sun_rule, $request->sun_from ?? 0, $request->sun_until ?? 24 ],
        ];

        if($plat_desk == 'on' && $plat_smartphone == 'on' && $plat_tablet == 'on')
        {
            $platform_targeting = [
                "type" => "INCLUDE",
                "value" => ['PHON', 'DESK', 'TBLT'],
                "href" => null
            ];
        } else {
            $platforms = [];
            foreach($platform_list as $key => $val) {
                if($val === 'off') continue;
                $platforms[] = $key;
            }
            if(sizeof($platforms) === 0 || sizeof($platforms) === 3)
            {
                $platform_targeting = [
                    "type" => "ALL",
                ];
            } else
            {
                $platform_targeting = [
                    "type" => "INCLUDE",
                    "value" => $platforms,
                    "href" => null
                ];
            }
        }

        $activity_schedule = [];
        if($schedule_opt === '24_7')
        {
            $activity_schedule = [
                "mode" => "ALWAYS",
                "rules" => [],
                "time_zone" => "America/Sao_Paulo"
            ];
        } else
        {
            $rules = [];
            foreach ($sch_weeks as $key => $value)
            {
                if($value[0] === 'ALL_DAY') continue;
                if($value[0] === 'EXCLUDE_THIS')
                {
                    $rules[] = [
                        "type" => "EXCLUDE",
                        "day" => $key,
                        "from_hour" => 0,
                        "until_hour" => 24,
                    ];
                } else if($value[0] === 'SPECIFIC_HOURS')
                {
                    $rules[] = [
                        "type" => "INCLUDE",
                        "day" => $key,
                        "from_hour" => $value[1],
                        "until_hour" => $value[2],
                    ];
                }
            }

            $activity_schedule = [
                "mode" => "CUSTOM",
                "rules" => $rules,
                "time_zone" => $timezone
            ];
        }

        if(Auth::guard('admin')->user()->id == 1)
        {
            $updateValue = [
                "name" => $name,
                "branding_text" => $branding_text,
                "cpc" => $bid_amount,
                "spending_limit" => $spending_limit,
                "spending_limit_model" => "MONTHLY",
                "marketing_objective" => "DRIVE_WEBSITE_TRAFFIC",
                "daily_cap" => $daily_cap,
                "bid_strategy" => "FIXED",
                "publisher_targeting" => $target_sites_val,
                "daily_ad_delivery_model" => "STRICT",
                "traffic_allocation_mode" => "OPTIMIZED",
                "country_targeting" => $country_targeting,
                "tracking_code" => $tracking_code,
                'platform_targeting' => $platform_targeting,
                'activity_schedule' => $activity_schedule
            ];
        } else
        {
            $updateValue = [
                "name" => $name,
                "branding_text" => $branding_text,
                "cpc" => $bid_amount,
                "spending_limit" => $spending_limit,
                "spending_limit_model" => "MONTHLY",
                "marketing_objective" => "DRIVE_WEBSITE_TRAFFIC",
                "daily_cap" => $daily_cap,
                "publisher_targeting" => $target_sites_val,
                "bid_strategy" => "FIXED",
                "daily_ad_delivery_model" => "STRICT",
                "traffic_allocation_mode" => "OPTIMIZED",
                "country_targeting" => $country_targeting,
                'platform_targeting' => $platform_targeting,
                'activity_schedule' => $activity_schedule
            ];
        }

        $res = Report::updateTaboolaCampaigns($id, $updateValue);

        if(array_key_exists('http_status', $res))
        {
            if($res['http_status'] === 400)
                return redirect()->route('campaigns.index')->with('error', $res['message']);
        } else
        {
            $allCmp = session()->get('all_cmp_ads_list');

            foreach ($allCmp   as $key => $item) {
                if($item['id'] == $res['id'])
                {
                    $allCmp[$key] = $res;
                    break;
                }
            }
            session()->put('all_cmp_ads_list', $allCmp);
            return redirect()->route('campaigns.index')->with('success', __('globals.msg.save_success'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Ajax campaign Update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxCampaignUpdate(Request $request)
    {
        if(request()->ajax()) {
            $type = $request->type;
            $val = $request->value;
            $id = $request->cmpid;
            $sendVal = [
                $type => $val
            ];
            $res = Report::updateTaboolaCampaigns($id, $sendVal);
            if(array_key_exists('http_status', $res))
            {
                if($res['http_status'] === 400)
                    return response()->json(['status' => 400]);

            } else
            {
                $allCmp = session()->get('all_cmp_ads_list');

                foreach ($allCmp   as $key => $item) {
                    if($item['id'] == $res['id'])
                    {
                        $allCmp[$key] = $res;
                        break;
                    }
                }
                session()->put('all_cmp_ads_list', $allCmp);
                return response()->json(['status' => 200]);
            }
        }
    }
}
