<?php

namespace DLW\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use DLW\Libraries\GoogleAnalytics;
use Illuminate\Support\Facades\Auth;
use DLW\Models\Report;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Integer;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if((sizeof(session('permissions')) > 0 && session('permissions')['content_page'] == 0))
        {
                return abort( 404);
        }

        $start_date = session()->get('rep_start_date');
        $end_date = session()->get('rep_end_date');

        $view_ids = session()->get('view_ids');
        $view_id_urls = session()->get('view_id_urls');

        $cur_site_url = session()->get('cur_site_url') ?? $view_id_urls[0];
        $cur_view_id = session()->get('cur_view_id') ?? $view_ids[0];

        if(!isset($start_date))
        {
            $start_date = Carbon::now()->subWeek()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
        }

        $wPosts = session()->get('all_wp_post_'.$cur_view_id);

        if(!isset($wPosts))
        {
            $wPosts =  Report::getWordPressPost($cur_site_url);
            session()->put('all_wp_post_'.$cur_view_id, $wPosts);
        }

        //Calculating Page View with Google Analysis...
        $dementionLst = ['ga:landingPagePath'];
        $matrixLst = ['ga:pageviews'];

        //dd($cur_view_id);

        $result = GoogleAnalytics::report('ga:'.$cur_view_id, $dementionLst, $matrixLst, $start_date, $end_date, 1, 10000, [], 'ga:landingPagePath');
        //$totalViews = $result['totalForResults']['ga:pageviews'];
        $totalViews = 0;
        $resPosts = [];

        $adItems = $result['items'];

        $userName = Auth::guard('admin')->user()->account_name;

        if($result['totalResults'] > 10000)
        {
            for ($index = 10001; $index < $result['totalResults']; $index += 10000)
            {
                $newRes = GoogleAnalytics::report('ga:'.$cur_view_id, $dementionLst, $matrixLst, $start_date, $end_date, $index, 10000, [], '');
                $adItems = array_merge($adItems, $newRes['items']);
            }
        }

        $resSlugs = [];
        $totalView = 0;
        if(sizeof($adItems) > 1)
        {
            for($index = 0; $index < sizeof($adItems); $index++)
            {
                $url = $adItems[$index][0];
                $totalView += $adItems[$index][1];
                $slugCnt = sizeof(explode('/', $url));
                $slug = explode('/', $url)[$slugCnt - 2] ?? '';
                $nextUrl = $adItems[$index + 1][0];
                $nextSlug = explode('/', $nextUrl)[1] ?? '';
                if($index == sizeof($adItems) - 2)
                {
                    if($slug != $nextSlug)
                    {
                        $resSlugs[$slug] = $totalView;
                        $resSlugs[$nextSlug] = $adItems[$index + 1][1];
                    } else
                    {
                        $resSlugs[$slug] = $totalView + $adItems[$index + 1][1];
                    }
                    break;
                }
                if($slug != $nextSlug)
                {
                    $resSlugs[$slug] = $totalView;
                    $totalView = 0;
                }
            }
        } else if(sizeof($adItems) == 1) {
            $url = $adItems[0][0];
            $totalView += $adItems[0][1];
            $slug = explode('/', $url)[1] ?? '';
            $resSlugs[$slug] = $totalView;
        }

        if(!array_key_exists('code' , $wPosts))
        {
            foreach ($wPosts as $key => $row)
            {
                //top-20-dos-carros-mais-economicos-para-rodar-na-estrada
                $row['page_views'] = number_format($resSlugs[$row['slug']] ?? 0);
                $resPosts[] = $row;
                $totalViews += $resSlugs[$row['slug']] ?? 0;
            }
        }

        return view('admin.contents.index', ['title' => __('globals.content_page.contents'), 'view_ids' => $view_ids, 'view_id_urls' => $view_id_urls, 'cur_view_id' => $cur_view_id, 'rep_start_date' => $start_date, 'rep_end_date' => $end_date, 'wp_posts' => $resPosts, 'total_views' => $totalViews, 'user_name' => $userName]);
    }

    /**
     * set session site url.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSetSessionSiteData(Request $request)
    {
        if(request()->ajax()) {
            $curSiteUrl = $request->site_url;
            $curViewId = $request->viwe_id;
            session()->put('cur_site_url', $curSiteUrl);
            session()->put('cur_view_id', $curViewId);
            return new JsonResponse([], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
     * Find Slug.
     *
     * @param  array $data
     * @param  string $slug
     * @return integer
     */
    public function getTotalPageViews($data, $slug)
    {
        $pageViews = 0;
        foreach ($data as $key => $value) {
            if(preg_match("/\b$slug\b/i", $value[0]))
                $pageViews += $value[1];
        }
        return $pageViews;
    }
}
