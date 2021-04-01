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
                        <a href="#" class="logo logo-admin"><img src="{{ asset('assets/img/login-min.png') }}" height="150" alt="logo"></a>
                    </h3>
                    <div class="p-3">
                        <form class="form-horizontal m-t-20" role="form" method="POST" action="{{ url('admin/login') }}" id="login_form">
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
                            <div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
                                <div class="col-12">
                                    <input type="text" class="form-control" id="txt_login_email" name="email" value="{{ old('email') }}" placeholder="E-mail" autofocus>
                                    @if($errors->has('email'))
                                        <div class="form-control-feedback" style="color: #d9534f;">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('password') ? ' has-error' : '' }}">
                                <div class="col-12">
                                    <input type="password" class="form-control" id="txt_login_password" name="password" placeholder="Password">
                                    @if($errors->has('password'))
                                        <div class="form-control-feedback" style="color: #d9534f;">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Remember me</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group text-center row m-t-20">
                                <div class="col-12">
                                    <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">Log In</button>
                                </div>
                            </div>
                            <div class="form-group mb-0 row">
                                <div class="col-sm-7 m-t-15">
                                    <a href="{{ route('admin.forgot_password') }}" class="text-muted"><i class="mdi mdi-lock"></i> Forgot your password?</a>
                                </div>
                                <div class="col-sm-5 m-t-15">
                                    <a href="{{ route('admin.google_form') }}" class="text-muted"><i class="mdi mdi-account-circle"></i> Create an account</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- Modal for Privacy Policy -->
        <div id="privacy_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <h5>Termos de Uso Smart Publishers</h5>
                        <p>Você deve ler e ACEITAR os Termos De Uso da Smart Publishers para prosseguir.</p>
                        <iframe src="https://docs.google.com/document/d/e/2PACX-1vTzEc-ddArSB4m_Jp6mxB_g0DSWbgVAcgWxFe7pSc0n1e6nePl0qOMZW8FN5OIOTphDe1N6GRlLkimf/pub?embedded=true" width="100%" frameborder="0" marginheight="0" marginwidth="0" height="250px"></iframe>
                        <div>
                            <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                <input type="checkbox" class="custom-control-input" id="accept_1" name="accept_1" {{ old('accept_1') ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Eu aceito os Termos de Uso da Smart Publishers</span>
                            </label>
                        </div>
                        <h5>Política de Privacidade Smart Publishers</h5>
                        <p>Você deve ler e ACEITAR a Política de Privacidade da Smart Publishers para prosseguir.</p>
                        <iframe src="https://docs.google.com/document/d/e/2PACX-1vQtbtivDQQkyaiYUBP2iVQ00RixF9j-ZqZYxal2XZAresMjsUyRWjEAW8uJc9Mxaw1qlk1LRvspzC4W/pub?embedded=true" width="100%" frameborder="0" marginheight="0" marginwidth="0" height="250px"></iframe>
                        <div>
                            <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                <input type="checkbox" class="custom-control-input" id="accept_2" name="accept_2" {{ old('accept_2') ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Eu aceito a política de privacidade da Smart Publishers</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Eu Não Aceito</button>
                        <button id="btn_submit" type="button" class="btn btn-success" disabled>Eu Aceito</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

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
        <script>
            let rembemberStatus = false, firstAccept = false, secondAccept = false;
            $(document).ready(function(){
                let rememberMe = $.cookie("remember_me");
                let login_email = $.cookie("login_email");
                let login_password = $.cookie('login_password');
                if(rememberMe == 'true')
                {
                    rembemberStatus = true;
                    $('#remember').click();
                    $('#txt_login_email').val(login_email);
                    $('#txt_login_password').val(login_password);
                }

                $('#remember').click(function(){
                    if($(this).prop("checked") == true){
                        rembemberStatus = true;
                    }
                    else if($(this).prop("checked") == false){
                        rembemberStatus = false;
                    }
                });

                $('#accept_1').click(function(){
                    if($(this).prop("checked") == true){
                        firstAccept = true;
                        if(firstAccept && secondAccept)
                            $('#btn_submit').removeAttr('disabled');
                    }
                    else if($(this).prop("checked") == false){
                        firstAccept = false;
                        $('#btn_submit').attr('disabled', true);
                    }
                });

                $('#accept_2').click(function(){
                    if($(this).prop("checked") == true){
                        secondAccept = true;
                        if(firstAccept && secondAccept)
                            $('#btn_submit').removeAttr('disabled');
                    }
                    else if($(this).prop("checked") == false){
                        secondAccept = false;
                        $('#btn_submit').attr('disabled', true);
                    }
                });

                $('#btn_submit').click(function(evt) {
                    $('#privacy_modal').modal('toggle');
                    $.post("{{ route('admin.ajax_update_docversion') }}", { email: $('#txt_login_email').val(), doc_version: "{{ env('DOC_VERSION') }}" },
                        function (resp,textStatus, jqXHR) {
                            $('#login_form').unbind('submit').submit();
                    });
                });

                $('#login_form').on('submit', function(evt){
                    evt.preventDefault();
                    if($('#txt_login_email').val() == '' || !/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($('#txt_login_email').val()))
                    {
                        $('#txt_login_email').focus();
                        return false;
                    }

                    setCookieValue();

                    ///Precheck Attach User info and Privacy policy status
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                    $.post("{{ route('admin.ajax_preinfo') }}", { email: $('#txt_login_email').val() },
                        function (resp,textStatus, jqXHR) {
                            if(resp.results === null )
                            {
                                localStorage.setItem('reg_email', $('#txt_login_email').val());
                                $('#login_form').unbind('submit').submit();
                                //location.href = "{{ url('admin/form') }}";
                            } else {
                                if(resp.results.doc_version != "{{ env('DOC_VERSION') }}" || resp.results.accept_status == 0)
                                {
                                    $('#privacy_modal').modal({backdrop:'static', keyboard:false, show:true});
                                } else
                                {
                                    $('#login_form').unbind('submit').submit();
                                }
                            }

                    }).fail(function(res) {
                        console.log("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                        return false;
                    });

                    //$('#privacy_modal').modal({backdrop:'static', keyboard:false, show:true});
                });
            })

            let setCookieValue = () => {
                $.cookie("remember_me", rembemberStatus, { expires : 100000000 });
                if(rembemberStatus)
                {
                    $.cookie("login_email", $('#txt_login_email').val(), { expires : 100000000 });
                    $.cookie("login_password", $('#txt_login_password').val(), { expires : 100000000 });
                } else
                {
                    $.cookie("login_email", '', { expires : 100000000 });
                    $.cookie("login_password", '', { expires : 100000000 });
                }
            };
        </script>
    </body>
</html>