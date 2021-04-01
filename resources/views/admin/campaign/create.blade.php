@extends('admin.layout')

@section('content')

    @include('admin.partials.top-bar')

    <div class="page-content-wrapper ">

        <div class="container">

            <div class="row">
                <div class="col-12">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <form method="post" action="{{ route('campaigns.store') }}" id="cmp_form">
                                {{ csrf_field() }}
                                <div class="form-group row
                                    @if($errors->has('name'))
                                        has-danger
                                    @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.name') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-6">
                                        <input name="name" class="form-control" type="text" value="{{ old('name') }}" id="cmp_name" required>
                                        @if ($errors->has('name'))
                                            <div class="form-control-feedback" >{{ $errors->first('name') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row
                                    @if($errors->has('branding_text'))
                                        has-danger
                                    @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.branding_text') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-6">
                                        <input name="branding_text" class="form-control" type="text" id="branding_text" value="{{ old('branding_text') }}" required>
                                        @if ($errors->has('branding_text'))
                                            <div class="form-control-feedback" >{{ $errors->first('branding_text') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row
                                    @if($errors->has('schedule'))
                                        has-danger
                                    @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.campaign_schedule') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn btn-secondary active">
                                                <input type="radio" name="schedule_options" id="sch_24_7" value="24_7" autocomplete="off" checked /> 24/7
                                            </label>
                                            <label class="btn btn-secondary">
                                                <input type="radio" name="schedule_options" id="sch_custom" value="custom" autocomplete="off" /> {{ __('globals.campaigns.custom') }}
                                            </label>
                                        </div>
                                        <div id="sch_custom_detail">
                                            <div class="col-md-12 row" >
                                                <select class="custom-select col-md-3" id="sch_custom_type">
                                                    <option value="WEEKDAYS">{{ __('globals.campaigns.weekdays_only_mon_fri') }}</option>
                                                    <option value="WEEKENDS">{{ __('globals.campaigns.weekend_only_sat_sun') }}</option>
                                                    <option value="EVERY_DAY">{{ __('globals.campaigns.every_day') }}</option>
                                                </select>
                                                <span class="text-vcenter"> {{ __('globals.campaigns.from') }}: </span>
                                                <select class="custom-select col-md-1" id="sch_custom_from">
                                                    @for($index = 0; $index < 24; $index++)
                                                        @if($index < 13)
                                                            @if($index == 12)
                                                                <option value="{{ $index }}">{{ $index }} PM</option>
                                                            @elseif($index == 0)
                                                                <option value="{{ $index }}">12 AM</option>
                                                            @else
                                                                <option value="{{ $index }}">{{ $index }} AM</option>
                                                            @endif
                                                        @else
                                                            <option value="{{ $index }}">{{ $index - 12 }} PM</option>
                                                        @endif
                                                    @endfor
                                                </select>
                                                <span class="text-vcenter"> {{ __('globals.campaigns.until') }}: </span>
                                                <select class="custom-select col-md-1" id="sch_custom_until">
                                                    @for($index = 1; $index < 25; $index++)
                                                        @if($index < 13)
                                                            @if($index < 12)
                                                                <option value="{{ $index }}">{{ $index }} AM</option>
                                                            @else
                                                                <option value="{{ $index }}">{{ $index }} PM</option>
                                                            @endif
                                                        @elseif($index == 24)
                                                            <option value="{{ $index }}" selected>{{ $index - 12 }} AM</option>
                                                        @else
                                                            <option value="{{ $index }}">{{ $index - 12 }} PM</option>
                                                        @endif
                                                    @endfor
                                                </select>
                                                <button class="btn btn-secondary ml-2" type="button" id="btn_sch_apply">{{ __('globals.campaigns.apply') }} <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                                            </div>
                                            <div class="row col-md-7 mt-2 table-responsive">
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ __('globals.campaigns.day') }}</th>
                                                        <th>{{ __('globals.campaigns.schedule_rule') }}</th>
                                                        <th>{{ __('globals.campaigns.from') }}</th>
                                                        <th>{{ __('globals.campaigns.until') }}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($weekdays as $key => $week)
                                                        <tr>
                                                            <td>{{ $week }}</td>
                                                            <td>
                                                                <select class="custom-select sch-rule" id="{{ $key }}_rule" name="{{ $key }}_rule">
                                                                    <option value="ALL_DAY">{{ __('globals.campaigns.all_day') }}</option>
                                                                    <option value="SPECIFIC_HOURS">{{ __('globals.campaigns.specific_hours') }}</option>
                                                                    <option value="EXCLUDE_THIS">{{ __('globals.campaigns.exclude_day') }}</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="custom-select" id="{{ $key }}_from" disabled="disabled" name="{{ $key }}_from">
                                                                    @for($index = 0; $index < 24; $index++)
                                                                        @if($index < 13)
                                                                            @if($index == 12)
                                                                                <option value="{{ $index }}">{{ $index }} PM</option>
                                                                            @elseif($index == 0)
                                                                                <option value="{{ $index }}">12 AM</option>
                                                                            @else
                                                                                <option value="{{ $index }}">{{ $index }} AM</option>
                                                                            @endif
                                                                        @else
                                                                            <option value="{{ $index }}">{{ $index - 12 }} PM</option>
                                                                        @endif
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="custom-select" id="{{ $key }}_until" disabled="disabled" name="{{ $key }}_until">
                                                                    @for($index = 1; $index < 25; $index++)
                                                                        @if($index < 13)
                                                                            @if($index < 12)
                                                                                <option value="{{ $index }}">{{ $index }} AM</option>
                                                                            @else
                                                                                <option value="{{ $index }}">{{ $index }} PM</option>
                                                                            @endif
                                                                        @elseif($index == 24)
                                                                            <option value="{{ $index }}" selected>{{ $index - 12 }} AM</option>
                                                                        @else
                                                                            <option value="{{ $index }}">{{ $index - 12 }} PM</option>
                                                                        @endif
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-12 row mt-2">
                                                <span class="mt-2 mr-2"> {{ __('globals.campaigns.time_zone') }}: </span>
                                                <select class="custom-select" id="timezone" name="timezone">
                                                    @foreach($timezone as $zone)
                                                        <option value="{{ $zone }}">{{ $zone }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.location') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-7">
                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn btn-secondary active">
                                                <input type="radio" name="location_option" id="target_location" value="target_location" autocomplete="off" checked /> {{ __('globals.campaigns.target_location') }}
                                            </label>
                                            <label class="btn btn-secondary">
                                                <input type="radio" name="location_option" id="all_location" value="all_location" autocomplete="off" /> {{ __('globals.campaigns.all_location') }}
                                            </label>
                                        </div>
                                        <div id="select2-countries">
                                            {!! Form::select('countries[]', [], old('countries'), array('id'=>'countries', 'class'=> 'custom-select col-md-3', 'multiple' => 'multiple')) !!}
                                            @if ($errors->has('countries'))
                                                <div class="form-control-feedback" >{{ $errors->first('countries') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.block_publishers') }} </label>
                                    <div class="col-sm-7">
                                        <div id="select2-sites">
                                            <select id="site_list" class="custom-select col-md-3">
                                                <option value="">&nbsp;</option>
                                                @foreach($all_sites as $row)
                                                    @if($row['blocking_level'] === 'NONE')
                                                        <option data-id="{{ $row['site_id'] }}" value="{{ $row['site'] }}">{{ $row['site_name'] }} ({{ $row['site_id']  }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <label class="font-weight-bold mt-2">{{ __('globals.campaigns.block_site_list') }}</label>
                                            <div class="block-content">
                                            </div>
                                            @if ($errors->has('site_list'))
                                                <div class="form-control-feedback" >{{ $errors->first('site_list') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.platform') }}</label>
                                    <div class="col-sm-10">
                                        <label class="custom-control custom-checkbox">
                                            {!! Form::checkbox('plat_desktop', null, old('plat_desktop'), array('id'=>'plat_desktop', 'class'=> 'custom-control-input', 'checked' => 'checked')) !!}
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('globals.campaigns.desktop') }}</span>
                                        </label>

                                        <label class="custom-control custom-checkbox">
                                            {!! Form::checkbox('plat_smartphone', null, old('plat_smartphone'), array('id'=>'plat_smartphone', 'class'=> 'custom-control-input', 'checked' => 'checked')) !!}
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('globals.campaigns.smartphone') }}</span>
                                        </label>

                                        <label class="custom-control custom-checkbox">
                                            {!! Form::checkbox('plat_tablet', null, old('plat_tablet'), array('id'=>'plat_tablet', 'class'=> 'custom-control-input', 'checked' => 'checked')) !!}
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('globals.campaigns.tablet') }}</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row
                                    @if($errors->has('bid_amount'))
                                        has-danger
                                    @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.bid_amount') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-3 input-group">
                                        <span class="input-group-addon bg-custom b-1">R$</span>
                                        <input name="bid_amount" type="number" class="form-control" id="bid_amount" value="{{ old('bid_amount') }}" min="0.055" step="0.001" required>
                                    </div>
                                    @if ($errors->has('bid_amount'))
                                        <div class="form-control-feedback" >{{ $errors->first('bid_amount') }}</div>
                                    @endif
                                </div>

                                <div class="form-group row
                                    @if($errors->has('spend_limit'))
                                        has-danger
                                    @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.spending_limit') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-3 input-group">
                                        <span class="input-group-addon bg-custom b-1">R$</span>
                                        <input name="spend_limit" type="number" class="form-control" id="spend_limit" value="{{ old('spend_limit') }}" min="0" required>
                                    </div>
                                    @if ($errors->has('spend_limit'))
                                        <div class="form-control-feedback" >{{ $errors->first('spend_limit') }}</div>
                                    @endif
                                </div>

                                <div class="form-group row
                                    @if($errors->has('daily_cap'))
                                        has-danger
                                    @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.daily_cap') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-3 input-group">
                                        <span class="input-group-addon bg-custom b-1">R$</span>
                                        <input name="daily_cap" type="number" class="form-control" id="daily_cap" value="{{ old('daily_cap') }}" min="0" required>
                                    </div>
                                    @if ($errors->has('daily_cap'))
                                        <div class="form-control-feedback" >{{ $errors->first('daily_cap') }}</div>
                                    @endif
                                </div>

                                <div class="form-group row
                                    @if($errors->has('tracking_code'))
                                        has-danger
                                    @endif"
                                     @if(Auth::guard('admin')->user()->id !== 1)
                                     style="display: none"
                                        @endif>
                                    <label class="col-sm-2 col-form-label">{{ __('globals.campaigns.tracking_code') }} <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <textarea name="tracking_code" class="form-control" id="tracking_code" rows="4">{{ old('tracking_code', 'utm_source={campaign_name}&utm_medium={site}&utm_campaign={title}&utm_term={thumbnail}&utm_content={campaign_id}') }}</textarea>
                                        @if ($errors->has('tracking_code'))
                                            <div class="form-control-feedback" >{{ $errors->first('tracking_code') }}</div>
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

@push('css')
    <!-- Alertify css -->
    <link href="{{ asset('assets/admin/plugins/alertify/css/alertify.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Toastr css -->
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .multi-input {
            position: absolute !important;
            top: 8px !important;
            right: 10px !important;;
        }
        .mb-25
        {
            margin-bottom: -25px !important;
        }
        .block-content
        {
            margin-left: -20px;
            margin-top:-10px !important;
        }
        .custom-control
        {
            margin-top: 8px;
        }
        .custom-select
        {
            min-width: 100px;
        }
        .active
        {
            background-color: #2196f3 !important;
            color: white !important;
        }
        span.text-vcenter
        {
            margin-left: 10px;
            margin-top: 5px;
            margin-right: 5px;
        }
        .select2-container {
            border-radius: 2px;
            height: 40px;
        }
        .select2-selection {
            height: 38px !important;
            padding-left: 2px;
        }
        .select2-selection__rendered,
        .select2-selection__arrow {
            margin-top: 4px;
        }
        #sch_custom_detail{
            display: none;
        }
        #timezone{
            width: 325px;
        }
        .table-responsive
        {
            min-width: 750px !important;
        }
        .form-control-feedback{
            color: red;
        }
        @media only screen and (max-width: 400px) {
            .table-responsive
            {
                min-width: auto !important;
            }
            #btn_sch_apply{
                width: 100%;
                margin-top:10px;
                margin-left:0px !important;
            }
        }

    </style>
@endpush

@push('scripts')
    <!-- Countries Library Js -->
    <script src="{{ asset('assets/admin/plugins/countries/countries.js') }}"></script>
    <!-- Select2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>
    <!-- Alertify Library js -->
    <script src="{{ asset('assets/admin/plugins/alertify/js/alertify.js') }}"></script>
    <!-- Toastr Library js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>


    <script>
        toastr.options.progressBar = true;
        toastr.options.closeButton = true;
        toastr.options.closeDuration = 300;
        toastr.options.timeOut = 1000; // How long the toast will display without user interaction

        $(document).ready(function()
        {
            $('#timezone').select2({
                allowClear: false,
                dropdownAutoWidth: true,
                width: 'element',
                minimumResultsForSearch: 10, //prevent filter input
                maximumSelectionSize: 20, // prevent scrollbar,
            });

            $('#site_list').select2({
                allowClear: false,
                dropdownAutoWidth: true,
                width: 'element',
                minimumResultsForSearch: 10, //prevent filter input
                maximumSelectionSize: 20, // prevent scrollbar,
            });

            py_all_countires.countries.forEach((value) => {
                $('#countries').append($("<option></option>").text(value.name).attr("value", value.sortname));
            })

            $('#countries').select2({
                allowClear: false,
                dropdownAutoWidth: true,
                width: 'element',
                minimumResultsForSearch: 10, //prevent filter input
                maximumSelectionSize: 20, // prevent scrollbar,
                placeholder: "{{ __('globals.campaigns.select_country') }}"
            });

            $('input[name=schedule_options]').change(function(evt)
            {
                if($(this).attr('id') === 'sch_custom')
                    $('#sch_custom_detail').show();
                else
                    $('#sch_custom_detail').hide();
            });

            $('input[name=location_option]').change(function(evt)
            {
                if($(this).attr('id') === 'target_location')
                    $('#select2-countries').show();
                else
                    $('#select2-countries').hide();
            });

            $('#btn_sch_apply').click(function(evt){
                let sch_custom_type = $('#sch_custom_type').val();
                let sch_custom_from = $('#sch_custom_from').val();
                let sch_custom_until = $('#sch_custom_until').val();
                if(sch_custom_type === 'WEEKDAYS')
                {
                    @foreach($weekdays as $key => $week)
                    @if($key == 'sat' || $key == 'sun')
                    $('#{{ $key }}_rule').val('EXCLUDE_THIS');
                    $('#{{ $key }}_from').attr('disabled', true);
                    $('#{{ $key }}_until').attr('disabled', true);
                    @else
                    $('#{{ $key }}_rule').val('SPECIFIC_HOURS');
                    $('#{{ $key }}_from').attr('disabled', false);
                    $('#{{ $key }}_until').attr('disabled', false);
                    $('#{{ $key }}_from').val(sch_custom_from);
                    $('#{{ $key }}_until').val(sch_custom_until);
                    @endif
                    @endforeach
                } else if(sch_custom_type === 'WEEKENDS')
                {
                    @foreach($weekdays as $key => $week)
                    @if($key == 'sat' || $key == 'sun')
                    $('#{{ $key }}_rule').val('SPECIFIC_HOURS');
                    $('#{{ $key }}_from').attr('disabled', false);
                    $('#{{ $key }}_until').attr('disabled', false);
                    $('#{{ $key }}_from').val(sch_custom_from);
                    $('#{{ $key }}_until').val(sch_custom_until);
                    @else
                    $('#{{ $key }}_rule').val('EXCLUDE_THIS');
                    $('#{{ $key }}_from').attr('disabled', true);
                    $('#{{ $key }}_until').attr('disabled', true);
                    @endif
                    @endforeach
                } else
                {
                    @foreach($weekdays as $key => $week)
                    $('#{{ $key }}_rule').val('SPECIFIC_HOURS');
                    $('#{{ $key }}_from').attr('disabled', false);
                    $('#{{ $key }}_until').attr('disabled', false);
                    $('#{{ $key }}_from').val(sch_custom_from);
                    $('#{{ $key }}_until').val(sch_custom_until);
                    @endforeach
                }
            });

            $('#site_list').change(function(){
                let curVal = $(this).val();
                if(curVal == '') return false;
                let site_list = $('div.block-content').find("input[name='target_sites[]']");
                let existFlag = false;
                site_list.each(function(row)
                {
                    if($(this).val() == curVal)
                    {
                       toastr.warning('{{ __('globals.msg.already_exist') }}', '{{ __('globals.msg.oh_snap') }}');
                       existFlag = true;
                       return false;
                    }
                });
                if(!existFlag)
                {
                    $('div.block-content').append(
                        `<div class="alert alert-dismissible fade show mb-25" role="alert">
                        <input class="form-control" type="text" readonly value="${$('#site_list option:selected').text()}">
                        <input type="hidden" name="target_sites[]" class="form-control"  value="${curVal}">
                        <button type="button" class="multi-input close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>`
                    );
                }
            });


            $('select.sch-rule').change(function(evt){
                let curRule = $(this).val();
                let curWeek = $(this).find(':selected').parent().attr('id').split('_')[0];
                if(curRule === 'SPECIFIC_HOURS')
                {
                    $(`#${curWeek}_from`).attr('disabled', false);
                    $(`#${curWeek}_until`).attr('disabled', false);
                } else
                {
                    $(`#${curWeek}_from`).attr('disabled', true);
                    $(`#${curWeek}_until`).attr('disabled', true);
                }
            });

            $('#cmp_form').submit(function(evt)
            {
                evt.preventDefault();
                alertify
                    .okBtn("{{ __('globals.campaigns.submit') }}")
                    .confirm("{{ __('globals.msg.are_you_sure') }}",
                        function()
                        {
                            $('#cmp_form').unbind('submit').submit();
                        },
                        function(){}
                    );
            });

            $('#bid_amount').focusout(function(evt){
                if(parseFloat($(this).val()) >= 0.08)
                {
                    alertify
                        .okBtn("{{ __('globals.campaigns.approve') }}")
                        .confirm("{{ __('globals.msg.higher_bid_amount') }}",
                            function(){
                            },
                            function(){
                                $('#bid_amount').val('');
                                $('#bid_amount').focus();
                            }
                        );
                }
                if(parseFloat($(this).val()) < 0.055)
                {
                    alertify.okBtn("{{ __('globals.campaigns.close') }}").alert("{{ __('globals.msg.lower_bid_amount') }}", function(){
                        $('#bid_amount').val('');
                        $('#bid_amount').focus();
                    });
                }
            });

        });
    </script>
@endpush
