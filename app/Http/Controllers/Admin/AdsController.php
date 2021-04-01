<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Models\Report;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;

class AdsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.guard');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if((sizeof(session('permissions')) > 0 && session('permissions')['ads_page'] == 0) || session()->get('cur_balance') < 100)
        {
            if(Auth::guard('admin')->user()->id !== 1)
                return abort( 404);
        }

        $start_date = session()->get('rep_start_date');
        $end_date = session()->get('rep_end_date');
        $adsStatus = DB::table('ads_status')->pluck('status', 'ads_id')->toArray();
        if(!isset($start_date))
        {
            $start_date = date('Y-m-d', strtotime("-1 days"));
            $end_date = date('Y-m-d', strtotime("-1 days"));
        }
        $res = session()->get('all_cmp_ads_list');
        $id = session()->get('cur_cmpid') ?? '';
//        if(!isset($res) || $res === null)
//            $res = Report::getTaboolaAllCampaign()['results'] ?? [];
        $cmplist = [];
        $cmplist['all'] = __('globals.campaigns.all_campaign');
        foreach ($res as $val)
        {
            $cmplist[$val['id']] = $val['id'].' ['.$val['name'].']';
        }

        $id = session()->get('cur_cmpid') ?? 'all';
        $res = Report::getTopTaboolaCampaignAds($id, $start_date, $end_date)['results'] ?? [];
        return view('admin.ads.index', ['title' => __('globals.ads.top_campaign_ads'), 'result' => $res, 'cmplist' => $cmplist, 'cmpid' => $id, 'rep_start_date' => $start_date, 'rep_end_date' => $end_date, 'ads_status' => $adsStatus]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if((sizeof(session('permissions')) > 0 && session('permissions')['ads_page'] == 0) || session()->get('cur_balance') < 100)
        {
            if(Auth::guard('admin')->user()->id !== 1)
                return abort( 404);
        }
        $res = session()->get('all_cmp_ads_list');
        $id = session()->get('cur_cmpid') ?? '';
//        if(!isset($res) || $res === null)
//            $res = Report::getTaboolaAllCampaign()['results'] ?? [];
        $cmplist = [];
        foreach ($res as $val)
        {
            $cmplist[$val['id']] = $val['id'].' ('.$val['name'].')';
        }
        return view('admin.ads.create', ['title' => __('globals.ads.create_campaign_ads'), 'cmplist' => $cmplist, 'cmpid' => $id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'campaign_id' => 'required',
                'title' => 'required',
                'url'  => 'required|url',
                'file' => 'required',
            ]
        );

        $id = $request->campaign_id;
        $title = $request->title;
        $url = $request->url;

        if ($request->hasFile('file')) {
            $photoPath = $request->file('file')->store('images', 'public');
            $path = storage_path('app/public/'.$photoPath);
            $info = pathinfo($path);
            $ext = $info['extension'];
            $curlFilePath = Report::getCurlRealFilePath($path, 'image/jpeg', round(microtime(true) * 1000).'.'.$ext);
            $uploadedPath = Report::uploadTaboolaThumbnail($curlFilePath);
            Storage::delete($photoPath);
            if(array_key_exists('value', $uploadedPath))
            {
                $resUrl = $uploadedPath['value'];
                $resUrl  = str_replace('http', 'https', $resUrl);
                $res = Report::massTaboolaAdsInsert($id, [ 'collection' => [['url' => $url, 'title' => $title, 'thumbnail_url' => $resUrl]]]);
                if(!array_key_exists('http_status', $res)) {
                    return redirect()->route('ads.edit', $id)->with('success', __('globals.msg.save_success'));
                } else
                {
                    return redirect()->route('ads.edit', $id)->with('error', $res['message']);
                }
            }
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
        if((sizeof(session('permissions')) > 0 && session('permissions')['ads_page'] == 0) || session()->get('cur_balance') < 100)
        {
            if(Auth::guard('admin')->user()->id !== 1)
                return abort( 404);
        }
        $res = [];
        $res = Report::getTaboolaCampaignAds($id)['results'] ?? [];
        session()->put('cur_cmpid', $id);
        return view('admin.ads.edit', ['title' => __('globals.ads.edit_campaign_ads') . ' - ' . $id, 'result' => $res, 'cmpid' => $id]);
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
     * urls mass add.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massAdd(Request $request, $id)
    {
        if(request()->ajax()) {
            $data = $request->data;
            $urlList = explode("\n", str_replace("\r", "", $data));
            $confirmedUrls = [];
            foreach ($urlList as $url)
            {
                $url = filter_var($url, FILTER_VALIDATE_URL);
                if(!$url) continue;
                $confirmedUrls[] = [
                    "url" => $url,
                    "thumbnail_url" => asset('/assets/img/ads/default_ads.jpg'),
                    "title" => __('globals.ads.input_title')
                ];
            }
            $sendVal = [ 'collection' => $confirmedUrls];
            Report::massTaboolaAdsInsert($id, $sendVal);
            return new JsonResponse([], 200);
        }
    }

    /**
     * set session start_date and end_date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSetSessionDate(Request $request)
    {
        if(request()->ajax()) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            session()->put('rep_start_date', $startDate);
            session()->put('rep_end_date', $endDate);
            return new JsonResponse([], 200);
        }
    }

    /**
     * set session cur campaign id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSetSessionCampaignId(Request $request)
    {
        if(request()->ajax()) {
            $id = $request->cmpid;
            session()->put('cur_cmpid', $id);
            return new JsonResponse([], 200);
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

    }

    /**
     * Remove ads item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxAdsRemove(Request $request, $id)
    {
        if (request()->ajax()) {
            $adsId = $request->id;
            Report::removeTaboolaCampaignAds($id, $adsId);
            return new JsonResponse([], 200);
        }
    }

    /**
     * multi Image upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxMultiImageUpload(Request $request)
    {
        if ($request->hasFile('file')) {
            $photoPath = $request->file('file')->store('images', 'public');
            $path = storage_path('app/public/'.$photoPath);
            $info = pathinfo($path);
            $ext = $info['extension'];
            $curlFilePath = Report::getCurlRealFilePath($path, 'image/jpeg', round(microtime(true) * 1000).'.'.$ext);
            $uploadedPath = Report::uploadTaboolaThumbnail($curlFilePath);
            Storage::delete($photoPath);
            if(array_key_exists('value', $uploadedPath)) {
                $resUrl = $uploadedPath['value'];
                $resUrl = str_replace('http', 'https', $resUrl);
                return new JsonResponse(['url' => $resUrl], 200);
            }
        }
    }

    /**
     * Ajax save ads
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSaveAds(Request $request)
    {
        if (request()->ajax()) {
            $url_list = $request->url_list;
            $image_list = $request->image_list;
            $title_list = $request->title_list;
            $cmp_id = $request->cmp_id;
            $confirmedData = [];
            foreach ($url_list as $url)
            {
                foreach ($image_list as $img)
                {
                    foreach ($title_list as $title)
                    {
                        $confirmedData[] = [
                            "url" => $url,
                            "thumbnail_url" => $img,
                            "title" => $title
                        ];
                    }
                }
            }
            $sendVal = [ 'collection' => $confirmedData];
            Report::massTaboolaAdsInsert($cmp_id, $sendVal);
            $request->session()->flash('success', __('globals.msg.save_success'));
            return new JsonResponse([], 200);
        }
    }



    /**
     * Remove ads item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxAdsUpdate(Request $request, $id)
    {
        if (request()->ajax()) {
            $adsId = $request->id;
            $type = $request->type;
            $value = $request->value;
            $update_date = date('Y-m-d H:i:s');
            if($type === 'thumbnail_url')
            {
                if ($request->hasFile('file')) {
                    $photoPath = $request->file('file')->store('images', 'public');
                    $path = storage_path('app/public/'.$photoPath);
                    $info = pathinfo($path);
                    $ext = $info['extension'];
                    $curlFilePath = Report::getCurlRealFilePath($path, 'image/jpeg', round(microtime(true) * 1000).'.'.$ext);
                    $uploadedPath = Report::uploadTaboolaThumbnail($curlFilePath);
                    Storage::delete($photoPath);
                    if(array_key_exists('value', $uploadedPath))
                    {
                        $resUrl = $uploadedPath['value'];
                        $resUrl  = str_replace('http', 'https', $resUrl);
                        Report::updateTaboolaCampaignAds($id, $adsId, [$type => $resUrl]);
                        return new JsonResponse(['url' => $resUrl], 200);
                    } else {
                        return new JsonResponse([], 400);
                    }
                }
            } else if($type === 'is_active')
            {
                DB::table('ads_status')
                    ->updateOrInsert(
                        ['ads_id' => $adsId],
                        ['status' => $value == 'false' ? 0 : 1, 'update_at' => $update_date]
                    );
            }
            $res = Report::updateTaboolaCampaignAds($id, $adsId, [$type => $value]);
            return new JsonResponse([], 200);
        }
    }
}
