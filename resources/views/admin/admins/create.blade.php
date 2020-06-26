@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')

<div class="page-content-wrapper ">

    <div class="container">

        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        <form method="post" action="{{ route('admins.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group row
                            @if($errors->has('name'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input name="name" class="form-control" type="text" value="{{ old('name') }}" id="example-text-input" required>
                                    @if ($errors->has('name'))
                                        <div class="form-control-feedback" >{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group row
                            @if($errors->has('email'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input name="email" class="form-control" type="email" id="example-email-input" value="{{ old('email') }}" required>
                                    @if ($errors->has('email'))
                                        <div class="form-control-feedback" >{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('view_id'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">View ID</label>
                                <div class="col-sm-10">
                                    <input name="view_id" class="form-control" id="example-view_id-input" value="{{ old('view_id') }}" required>
                                    @if ($errors->has('view_id'))
                                        <div class="form-control-feedback" >{{ $errors->first('view_id') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('client_id'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Client ID</label>
                                <div class="col-sm-10">
                                    <input name="client_id" class="form-control" id="example-view_id-input" value="{{ old('client_id') }}" required>
                                    @if ($errors->has('client_id'))
                                        <div class="form-control-feedback" >{{ $errors->first('client_id') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('client_secret'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Client Secret</label>
                                <div class="col-sm-10">
                                    <input name="client_secret" class="form-control" id="example-view_id-input" value="{{ old('client_secret') }}" required>
                                    @if ($errors->has('client_secret'))
                                        <div class="form-control-feedback" >{{ $errors->first('client_secret') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('account_name'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Taboola Account Name</label>
                                <div class="col-sm-10">
                                    <input name="account_name" class="form-control" id="example-view_id-input" value="{{ old('account_name') }}" required>
                                    @if ($errors->has('account_name'))
                                        <div class="form-control-feedback" >{{ $errors->first('account_name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('avatar'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Avatar</label>
                                <div class="col-sm-10">
                                    <input name="avatar" class="form-control" type="file" id="example-file-input">
                                    @if ($errors->has('avatar'))
                                        <div class="form-control-feedback" >{{ $errors->first('avatar') }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group row
                            @if($errors->has('password'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input name="password" class="form-control" type="password" value="{{ old('password') }}" id="example-password-input" required>
                                    @if ($errors->has('password'))
                                        <div class="form-control-feedback" >{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                            </div>
                        
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <div class="button-items">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Save</button>
                                        <button class="btn btn-secondary waves-effect" type="button" onclick="history.back(1);">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div><!-- container -->


</div>

@endsection

