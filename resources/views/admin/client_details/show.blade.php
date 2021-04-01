@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')

<div class="page-content-wrapper ">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show col-sm-7" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>{{ __('globals.msg.well_done') }}</strong> {{ session('success') }}
                            </div>
                        @endif
                        <form method="post" action="{{ route('profile.client_details.update', $detail->id) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group mb-2">
                                <h4 class="row ml-1">{{ __('globals.wizard.account') }}</h4>
                            </div>
                            <hr/>
                            <div class="form-group row
                            @if($errors->has('name'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.name') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="name" class="form-control" type="text" value="{{ old('name', $detail->name) }}">
                                    @if ($errors->has('name'))
                                        <div class="form-control-feedback" >{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('email'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.email') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="email" class="form-control" type="email" value="{{ old('email', $detail->email) }}" required>
                                    @if ($errors->has('email'))
                                        <div class="form-control-feedback" >{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('business_name'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.business_name') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="business_name" class="form-control" type="text" value="{{ old('business_name', $detail->business_name) }}" required>
                                    @if ($errors->has('business_name'))
                                        <div class="form-control-feedback" >{{ $errors->first('business_name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('cnpj'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.cnpj') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="cnpj" class="form-control" type="text" value="{{ old('cnpj', $detail->cnpj) }}" required>
                                    @if ($errors->has('cnpj'))
                                        <div class="form-control-feedback" >{{ $errors->first('cnpj') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('address'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.address') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="address" class="form-control" type="text" value="{{ old('address', $detail->address) }}" required>
                                    @if ($errors->has('address'))
                                        <div class="form-control-feedback" >{{ $errors->first('address') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('phone'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.phone') }} </label>
                                <div class="col-sm-5">
                                    <input name="phone" class="form-control" type="text" value="{{ old('phone', $detail->phone_number) }}">
                                    @if ($errors->has('phone'))
                                        <div class="form-control-feedback" >{{ $errors->first('phone') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group mt-lg-5">
                                <h4 class="row ml-1">{{ __('globals.wizard.bank_data') }}</h4>
                            </div>
                            <hr/>

                            <div class="form-group row
                            @if($errors->has('bank_name'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.bank_name') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="bank_name" class="form-control" type="text" value="{{old('bank_name', $detail->bank_name) }}" required>
                                    @if ($errors->has('bank_name'))
                                        <div class="form-control-feedback" >{{ $errors->first('bank_name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('bank_proxy'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.bank_proxy') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="bank_proxy" class="form-control" type="text" value="{{ old('bank_proxy', $detail->bank_proxy_name) }}" required>
                                    @if ($errors->has('bank_proxy'))
                                        <div class="form-control-feedback" >{{ $errors->first('bank_proxy') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('bank_confirm'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.bank_confirm') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="bank_confirm" class="form-control" type="text" value="{{ old('bank_confirm', $detail->bank_account_confirm) }}" required>
                                    @if ($errors->has('bank_confirm'))
                                        <div class="form-control-feedback" >{{ $errors->first('bank_confirm') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('cpf_cnpj'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.cpf_cnpj') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input name="cpf_cnpj" class="form-control" type="text" value="{{ old('cpf_cnpj', $detail->bank_cpf_cnpj) }}" required>
                                    @if ($errors->has('bank_confirm'))
                                        <div class="form-control-feedback" >{{ $errors->first('cpf_cnpj') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('other_info'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.wizard.other_info') }} </label>
                                <div class="col-sm-5">
                                    <textarea name="other_info" class="form-control" rows="3">{{ old('other_info', $detail->note) }}</textarea>
                                    @if ($errors->has('other_info'))
                                        <div class="form-control-feedback" >{{ $errors->first('other_info') }}</div>
                                    @endif
                                </div>
                            </div>






                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-5">
                                    <div class="button-items">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Save changes</button>
                                        <button class="btn btn-secondary waves-effect" type="button" onclick="history.back(1);">Cancel</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="profile" value="profile"/>
                        </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div><!-- container -->

</div>
@endsection

@push('css')
    <link href="{{ asset('assets/admin/css/tagify.css') }}" rel="stylesheet" type="text/css" />
    <!-- Dropify css -->
    <link href="{{ asset('assets/admin/plugins/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <!-- Jquery Tagify -->
    <script src="{{ asset('assets/admin/js/jQuery.tagify.min.js') }}"></script>
    <!-- Dropify Library js -->
    <script src="{{ asset('assets/admin/plugins/dropify/dropify.min.js') }}"></script>
@endpush
