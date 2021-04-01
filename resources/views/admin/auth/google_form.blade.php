<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Smart Publishers Admin Panel :: Form</title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="ThemeDesign" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link href="{{ asset('assets/wizard/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/wizard/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/wizard/css/form-elements.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/wizard/css/style.css') }}">
</head>

<body>
<!-- Top content -->
    <div class="top-content">
        <div class="container">
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 form-box">
                    <form role="form" action="{{ route('client_details.savedata') }}" method="post" class="f1">
                        {{ csrf_field() }}
                        <h3>{{ __('globals.wizard.contact_info') }}</h3>
                        <p></p>
                        <div class="f1-steps">
                            <div class="f1-progress">
                                <div class="f1-progress-line" data-now-value="16.66" data-number-of-steps="3" style="width: 16.66%;"></div>
                            </div>
                            <div class="f1-step active">
                                <div class="f1-step-icon"><i class="fa fa-user"></i></div>
                                <p>{{ __('globals.wizard.account') }}</p>
                            </div>
                            <div class="f1-step">
                                <div class="f1-step-icon"><i class="fa fa-credit-card"></i></div>
                                <p>{{ __('globals.wizard.bank_data') }}</p>
                            </div>
                            <div class="f1-step">
                                <div class="f1-step-icon"><i class="fa fa-lock"></i></div>
                                <p>{{ __('globals.wizard.privacy_policy') }}</p>
                            </div>
                        </div>

                        <fieldset>
                            <h4><span class="text-danger">*</span> {{ __('globals.wizard.contact_desc') }}:</h4>
                            <div class="form-group">
                                <label class="sr-only" for="name">{{ __('globals.wizard.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('globals.wizard.name') }}..." class="form-control" id="name">
                                @if ($errors->has('name'))
                                    <label class="validate-error">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="email">{{ __('globals.wizard.email') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('globals.wizard.email') }}..." class="form-control" id="email">
                                @if ($errors->has('email'))
                                    <label class="validate-error">{{ $errors->first('email') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="business_name">{{ __('globals.wizard.business_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="{{ __('globals.wizard.business_name') }}..." class="form-control" id="business_name">
                                @if ($errors->has('business_name'))
                                    <label class="validate-error">{{ $errors->first('business_name') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="cnpj">{{ __('globals.wizard.cnpj') }} <span class="text-danger">*</span></label>
                                <input type="text" name="cnpj"  value="{{ old('cnpj') }}" placeholder="{{ __('globals.wizard.cnpj') }}..." class="form-control" id="cnpj">
                                @if ($errors->has('cnpj'))
                                    <label class="validate-error">{{ $errors->first('cnpj') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="address">{{ __('globals.wizard.address') }} <span class="text-danger">*</span></label>
                                <input type="text" name="address"  value="{{ old('address') }}" placeholder="{{ __('globals.wizard.address') }}..." class="form-control" id="address">
                                @if ($errors->has('address'))
                                    <label class="validate-error">{{ $errors->first('address') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="phone">{{ __('globals.wizard.phone') }} </label>
                                <input type="text" name="phone"  value="{{ old('phone') }}" placeholder="{{ __('globals.wizard.phone') }}..." class="form-control" id="phone">
                            </div>

                            <div class="f1-buttons">
                                <button type="button" class="btn btn-primary" id="btn-firstpage">{{ __('globals.wizard.previous') }}</button>
                                <button type="button" class="btn btn-next">{{ __('globals.wizard.next') }}</button>
                            </div>
                        </fieldset>

                        <fieldset>
                            <h4><span class="text-danger">*</span> {{ __('globals.wizard.bank_info') }}:</h4>
                            <div class="form-group">
                                <label class="sr-only" for="bank_name">{{ __('globals.wizard.bank_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="{{ __('globals.wizard.bank_name') }}..." class="form-control" id="bank_name">
                                @if ($errors->has('bank_name'))
                                    <label class="validate-error">{{ $errors->first('bank_name') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="bank_proxy">{{ __('globals.wizard.bank_proxy') }} <span class="text-danger">*</span></label>
                                <input type="text" name="bank_proxy" value="{{ old('bank_proxy') }}" placeholder="{{ __('globals.wizard.bank_proxy') }}..." class="form-control" id="bank_proxy">
                                @if ($errors->has('bank_proxy'))
                                    <label class="validate-error">{{ $errors->first('bank_proxy') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="bank_confirm">{{ __('globals.wizard.bank_confirm') }} <span class="text-danger">*</span></label>
                                <input type="text" name="bank_confirm" value="{{ old('bank_confirm') }}" placeholder="{{ __('globals.wizard.bank_confirm') }}..." class="form-control" id="bank_confirm">
                                @if ($errors->has('bank_confirm'))
                                    <label class="validate-error">{{ $errors->first('bank_confirm') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="cpf_cnpj">{{ __('globals.wizard.cpf_cnpj') }} <span class="text-danger">*</span></label>
                                <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj') }}" placeholder="{{ __('globals.wizard.cpf_cnpj') }}..." class="form-control" id="cpf_cnpj">
                                @if ($errors->has('cpf_cnpj'))
                                    <label class="validate-error">{{ $errors->first('cpf_cnpj') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="other_info">{{ __('globals.wizard.other_info') }}</label>
                                <textarea name="other_info" placeholder="{{ __('globals.wizard.other_info') }}..."
                                          class="form-control" id="other_info">{{ old('other_info') }}</textarea>
                            </div>

                            <div class="f1-buttons">
                                <button type="button" class="btn btn-previous">{{ __('globals.wizard.previous') }}</button>
                                <button type="button" class="btn btn-next">{{ __('globals.wizard.next') }}</button>
                            </div>
                        </fieldset>

                        <fieldset>
                            <h3>Termos de Uso Smart Publishers</h3>
                            <h5>Você deve ler e ACEITAR os Termos De Uso da Smart Publishers para prosseguir.</h5>
                            <iframe src="https://docs.google.com/document/d/e/2PACX-1vTzEc-ddArSB4m_Jp6mxB_g0DSWbgVAcgWxFe7pSc0n1e6nePl0qOMZW8FN5OIOTphDe1N6GRlLkimf/pub?embedded=true" width="100%" frameborder="0" marginheight="0" marginwidth="0" height="250px"></iframe>
                            <div>
                                <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                    <input type="checkbox" class="custom-control-input" id="accept_1" name="accept_1" {{ old('accept_1') ? 'checked' : '' }}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description"> Eu aceito os Termos de Uso da Smart Publishers</span>
                                    <span class="text-danger">*</span>
                                </label>
                            </div>
                            <h3>Política de Privacidade Smart Publishers</h3>
                            <h5>Você deve ler e ACEITAR a Política de Privacidade da Smart Publishers para prosseguir.</h5>
                            <iframe src="https://docs.google.com/document/d/e/2PACX-1vQtbtivDQQkyaiYUBP2iVQ00RixF9j-ZqZYxal2XZAresMjsUyRWjEAW8uJc9Mxaw1qlk1LRvspzC4W/pub?embedded=true" width="100%" frameborder="0" marginheight="0" marginwidth="0" height="250px"></iframe>
                            <div>
                                <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                    <input type="checkbox" class="custom-control-input" id="accept_2" name="accept_2" {{ old('accept_2') ? 'checked' : '' }}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description"> Eu aceito a política de privacidade da Smart Publishers</span>
                                    <span class="text-danger">*</span>
                                </label>
                            </div>
                            <div class="f1-buttons">
                                <button type="button" class="btn btn-previous">{{ __('globals.wizard.previous') }}</button>
                                <button type="submit" id="btn_submit" class="btn btn-submit" disabled>{{ __('globals.wizard.submit') }}</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <p class="text-muted text-center font-18 m-t-15">© Copyright 2020 Smart Publishers</p>
        </div>
    </div>

    <script src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/wizard/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/wizard/js/jquery.backstretch.min.js') }}"></script>
    <script src="{{ asset('assets/wizard/js/retina-1.1.0.min.js') }}"></script>
    <script src="{{ asset('assets/wizard/js/scripts.js') }}"></script>

</body>
</html>