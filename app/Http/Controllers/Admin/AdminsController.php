<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Models\Admin;
use Illuminate\Http\Request;
use DLW\Http\Controllers\Controller;
use DLW\Http\Requests\Admin\CreateAdminRequest;
use DLW\Http\Requests\Admin\UpdateAdminRequest;
use Auth;
use Illuminate\Support\Facades\Storage;

class AdminsController extends Controller
{
    public function __construct(){
        $this->middleware('admin.guard');
        $this->middleware('issuper');
    }
    
    public function index()
    {
        //$admins = Admin::where('id', '!=', Auth::guard('admin')->id())->get();
        $admins = Admin::orderBy('id', 'asc')->get();
        return view('admin.admins.index', ['title'=>'Admin Management', 'admins'=>$admins]);
    }

    public function create()
    {
        return view('admin.admins.create', ['title'=>'Create Admin']);
    }

    public function store(CreateadminRequest $request)
    {
        $admin = new Admin;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->view_id = $request->view_id;
        $admin->client_id = $request->client_id;
        $admin->client_secret = $request->client_secret;
        $admin->account_name = $request->account_name;
        $admin->password = bcrypt($request->password);
        if($request->avatar!=null){
            Storage::delete(Admin::find(Auth::guard('admin')->id())->avatar);
            $path = $request->file('avatar')->store('avatars');
            $admin->avatar = $path;
        }
        $admin->save();
        
        return redirect()->route('admins.index');
    }

    public function show(Admin $admin)
    {
        //
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', ['title'=>'Edit admin', 'admin'=>$admin]);
    }

    public function update(UpdateadminRequest $request, Admin $admin)
    {
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->view_id = $request->view_id;
        $admin->client_id = $request->client_id;
        $admin->client_secret = $request->client_secret;
        $admin->account_name = $request->account_name;
        $admin->password = bcrypt($request->password);
        if($request->avatar!=null){
            Storage::delete(Admin::find(Auth::guard('admin')->id())->avatar);
            $path = $request->file('avatar')->store('avatars');
            $admin->avatar = $path;
        }
        $admin->save();

        if(Auth::guard('admin')->user()->email == $request->email)
        {
            $viewids = json_decode($request->view_id, true);
            $viewidLst = [];
            $urlLst = [];
            foreach($viewids as $row)
            {
                $value = $row['value'];
                array_push($viewidLst, trim(explode(':', $value)[0]));
                array_push($urlLst, trim(explode(':', $value)[1]));
            }

            session()->put('cur_view_id', $viewidLst[0]);
            session()->put('view_ids', $viewidLst);
            session()->put('view_id_urls', $urlLst);
        }
        
        return redirect()->route('admins.index');
    }

    public function destroy(Admin $admin)
    {
        Storage::delete($admin->avatar);
        $admin->delete();

        return response()->json(['status'=>true, 'message'=>'Admin deleted successfully.']);
    }
}
