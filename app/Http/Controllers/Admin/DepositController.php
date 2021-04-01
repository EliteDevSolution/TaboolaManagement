<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DLW\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DLW\Models\Deposit;
use DLW\Models\Admin;
use DLW\Models\ClientDetail;
use DB;



class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.guard');
        $this->middleware('issuper');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(sizeof(session('permissions')) > 0 && session('permissions')['financial_setting'] == 0)
        {
            return abort( 404);
        }

        $userslist = Admin::select(DB::raw("CONCAT(name, '[', email, ']') as username"), 'id')->get()->pluck('username', 'id')->toArray();
        $users = $userslist;
        $users[0] = __('globals.finance.all_users');
        ksort($users);
        $sel_user = session()->get('sel_user');

        if(!isset($sel_user) || $sel_user == '')
            $sel_user = 0;
        return view('admin.deposit.index', ['title'=>__('globals.finance.deposits'), 'users' => $users, 'sel_user' => $sel_user, 'user_list' => $userslist]);

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

    }

    /**
     * Ajax edit data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxEditData(Request $request)
    {
        if(request()->ajax()) {
            $user_id = $request->user_id;
            $id = $request->id;
            if($request->change_flag == 'false')
            {
                $validator = Validator::make($request->all(),
                    [
                        'id' => 'required',
                        'made_date' => 'required',
                        'amount' => 'required|numeric',
                    ]
                );
            } else {
                $validator = Validator::make($request->all(),
                    [
                        'id' => 'required',
                        'made_date' => 'required|unique:deposits,made_date,NULL,id,user_id,'.$user_id,
                        'amount' => 'required|numeric',
                    ]
                );
            }

            if ($validator->fails()){
                $error_messages = $validator->errors()->messages();
                return response()->json(['status' => $error_messages]);
            }

            $deposits = new Deposit;
            $updateData = [
                "made_date" => $request->made_date,
                "amount" => $request->amount,
                "user_id" => $user_id,
                "description" => $request->description ?? ''
            ];
            $deposits->whereId($id)->update($updateData);
            $balance = Report::getTotalBalance(session()->get('all_pay_day_list'));
            session()->put('cur_balance', $balance);
            $curBalance = "R$ " . number_format(session()->get('cur_balance'), 2, '.', ',');

            return response()->json(['status' => 200, 'cur_balance' => $curBalance, 'real_balance' => $balance]);
        }
    }


    /**
     * Ajax save data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSaveData(Request $request)
    {
        if(request()->ajax()) {
            $user_id = $request->user_id;
            $validator = Validator::make($request->all(),
                [
                    'made_date' => 'required|unique:deposits,made_date,NULL,id,user_id,'.$user_id,
                    'amount' => 'required|numeric',
                ]
            );
            if ($validator->fails()){
                $error_messages = $validator->errors()->messages();
                return response()->json(['status' => $error_messages]);
            }

            $deposits = new Deposit;
            $deposits->user_id = $user_id;
            $deposits->made_date = $request->made_date;
            $deposits->amount = $request->amount;
            $deposits->description = $request->description ?? '';
            $deposits->save();

            $balance = Report::getTotalBalance(session()->get('all_pay_day_list'));
            session()->put('cur_balance', $balance);
            $curBalance = "R$ " . number_format(session()->get('cur_balance'), 2, '.', ',');
            return response()->json(['status' => 200, 'cur_balance' => $curBalance, 'real_balance' => $balance]);
        }
    }

    /**
     * Remove deposits item.
     *
     * @param  Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function ajaxDepositsRemove(Deposit $deposit)
    {
        if (request()->ajax()) {
            $deposit->delete();
            $balance = Report::getTotalBalance(session()->get('all_pay_day_list'));
            session()->put('cur_balance', $balance);
            $curBalance = "R$ " . number_format(session()->get('cur_balance'), 2, '.', ',');

            return response()->json(['status' => 200, 'cur_balance' => $curBalance, 'real_balance' => $balance]);
        }
    }


    /**
     * Get all deposits data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetAllDeposits(Request $request)
    {
        $user_id = session()->get('sel_user');
        if(request()->ajax()) {
            if($user_id == 0)
            {
                $list = Deposit::with('admin', 'admin.client_details')->orderBy('made_date', 'desc')->get();
            } else
            {
                $list = Deposit::where(['user_id' => $user_id])->with('admin', 'admin.client_details')->orderBy('made_date', 'desc')->get();
            }
            return response()->json(['results' => $list]);
        }
    }

    /**
     * Set session user id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSetSessionUserId(Request $request)
    {
        if(request()->ajax()) {
            session()->put('sel_user', $request->cur_userid);
            return response()->json(['status' => 200]);
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
