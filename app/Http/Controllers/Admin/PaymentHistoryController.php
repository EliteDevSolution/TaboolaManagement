<?php

namespace DLW\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use DLW\Models\Deposit;
use DLW\Models\Report;


class PaymentHistoryController extends Controller
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
        //dd(session('all_pay_day_list'));
        if(sizeof(session('permissions')) > 0 && (session('permissions')['financial_setting'] == 0 || session('permissions')['payment_history'] == 0))
        {
            return abort( 404);
        }
        $start_date = session('fin_start_date');
        $end_date = session('fin_end_date');
        $cur_type = session('fin_cur_date_type');
        //session()->put('fin_cur_date_type', 'Day');

        $searchTypes = [__('globals.payment_history.day') => __('globals.payment_history.day'), __('globals.payment_history.week') => __('globals.payment_history.week'), __('globals.payment_history.month') => __('globals.payment_history.month')];

        if(!isset($start_date))
        {
            $start_date = date('Y-m-d', strtotime("-60 days"));
            $end_date = date('Y-m-d');
        }

        if(!isset($cur_type))
        {
            $cur_type = __('globals.payment_history.day');
        }



        ///Fetch transaction history.
        $transactionHistory = Report::getPaymentTransactionHistory($cur_type, $start_date, $end_date);

        return view('admin.payment_history.index', ['title'=>__('globals.payment_history.payment_history'), 'fin_start_date' => $start_date, 'fin_end_date' => $end_date,
            'search_type' => $searchTypes, 'cur_date_type' => $cur_type, 'final_res' => $transactionHistory]);
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

    public function ajaxSetSessionDate(Request $request)
    {
        if(request()->ajax()) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            session()->put('fin_start_date', $startDate);
            session()->put('fin_end_date', $endDate);
            return new JsonResponse([], 200);
        }
    }

    /**
     * set session cur campaign id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSetSessionDateType(Request $request)
    {
        if(request()->ajax()) {
            $type = $request->cur_type;
            session()->put('fin_cur_date_type', $type);
            return new JsonResponse([], 200);
        }
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
}
