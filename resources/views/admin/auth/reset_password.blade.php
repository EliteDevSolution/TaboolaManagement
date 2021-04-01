<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title>Smart Publishers Admin Panel :: Login</title>
        <meta content="Admin Dashboard" name="description" />
        <meta content="ThemeDesign" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
        <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/admin/css/icons.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet" type="text/css">
    </head>

    <body>
        <div class="wrapper-page">
            <div class="card">
                <div class="card-block">
                    <h3 class="text-center mt-3 m-b-15">
                        <a href="/" class="logo logo-admin"><img src="{{ asset('assets/img/login-min.png') }}" height="150" alt="logo"></a>
                    </h3>
                    <h4 class="text-muted text-center font-18"><b>Reset Password</b></h4>
                    <div class="p-3">
                        <form class="form-horizontal m-t-20" role="form" method="POST" action="{{ route('admin.reset_password') }}" id="reset_password_form">
                            {{ csrf_field() }}
                            @if(session()->has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <strong>{{ __('globals.msg.well_done') }}</strong> {{ session('success') }}
                                </div>
                            @endif
                            @if(session()->has('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <strong>{{ __('globals.msg.oh_snap') }}</strong> {{ session('error') }}
                                </div>
                            @endif
                            <div class="form-group row{{ $errors->has('password') ? ' has-error' : '' }}">
                                <div class="col-12">
                                    <input type="password" class="form-control" id="txt_new_password" name="password" placeholder="New Password">
                                    @if($errors->has('password'))
                                        <div class="form-control-feedback" style="color: #d9534f;">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('confirm_password') ? ' has-error' : '' }}">
                                <div class="col-12">
                                    <input type="password" class="form-control" id="txt_confirm_password" name="confirm_password" placeholder="Confirm Password">
                                    @if($errors->has('confirm_password'))
                                        <div class="form-control-feedback" style="color: #d9534f;">{{ $errors->first('confirm_password') }}</div>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" name="email" value="{{ $email }}" />
                            <div class="form-group text-center row m-t-20">
                                <div class="col-12">
                                    <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>

        <p class="text-muted text-center font-18" style="position: fixed; width: 100%; bottom: 0; font-size: 15px;">© Copyright 2020 Smart Publishers</p>

        <script src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/tether.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/modernizr.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/detect.js') }}"></script>
        <script src="{{ asset('assets/admin/js/fastclick.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.blockUI.js') }}"></script>
        <script src="{{ asset('assets/admin/js/waves.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.nicescroll.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.scrollTo.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.cookie.js') }}"></script>
        <script src="{{ asset('assets/admin/js/app.js') }}"></script>
    </body>
</html>