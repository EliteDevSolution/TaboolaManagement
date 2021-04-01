@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')

<div class="page-content-wrapper ">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="m-b-10 list-inline float-right" id="dashdate">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
                {{ __('globals.common.viewids') }}:
                <select class="minimal" id="selviewids" class="m-b-12 col-md-3 list-inline">
                    @if($cur_view_id == "0")
                        <option value="0" selected> {{ __('globals.common.all_view_ids') }}</option>
                    @else
                        <option value="0"> {{ __('globals.common.all_view_ids') }}</option>
                    @endif
                    @foreach ($view_ids as $key => $val)
                        @if($val == $cur_view_id)
                            <option value="{{ $val }}" selected>{{ $val }} ( {{ 'https://'.$view_id_urls[$key] }} )</option>
                        @else
                            <option value="{{ $val }}">{{ $val }} ( {{ 'https://'.$view_id_urls[$key] }} )</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="mini-stat clearfix bg-primary">
                    <span class="mini-stat-icon"><i class="mdi mdi-currency-usd"></i></span>
                    <div class="mini-stat-info text-right text-white">
                        <span class="counter" id="s_spend_total">R$ {{ $sum_spent }}</span>
                        {{ __('globals.common.total_spends') }}
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="mini-stat clearfix bg-primary">
                    <span class="mini-stat-icon"><i class="mdi mdi-cart-outline"></i></span>
                    <div class="mini-stat-info text-right text-white">
                        <span class="counter" id="s_rmax_total">R$ {{ $sum_benefit }}</span>
                        {{ __('globals.common.total_received_max') }}
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="mini-stat clearfix bg-primary">
                    <span class="mini-stat-icon"><i class="mdi mdi-scale-balance"></i></span>
                    <div class="mini-stat-info text-right text-white">
                        <span class="counter" id="s_profit_total">R$ {{ $sum_profit }}</span>
                        {{ __('globals.common.total_profit_max') }}
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="mini-stat clearfix bg-primary">
                    <span class="mini-stat-icon"><i class="mdi mdi-cube-outline"></i></span>
                    <div class="mini-stat-info text-right text-white">
                        <span class="counter" id="s_roimax_total">{{ $sum_roi }} %</span>
                        {{ __('globals.common.roi_max') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="btm-tbl mt-2">
            <div class="card m-b-20">
                <div class="card-block effect8">
                    <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab-received-spend" role="tab">{{ __('globals.common.receive_spent') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-roi" role="tab">{{ __('globals.common.roi') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-profit" role="tab">{{ __('globals.common.profit') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-all-analysis" role="tab">{{ __('globals.common.all_analysis') }}</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active p-3" id="tab-received-spend" role="tabpanel">
                                <div id="chart-received-spend" height="120"></div>
                            </div>
                            <div class="tab-pane p-3" id="tab-roi" role="tabpanel">
                                <div id="chart-roi" height="120"></div>
                            </div>
                            <div class="tab-pane p-3" id="tab-profit" role="tabpanel">
                                <div id="chart-profit" height="120"></div>
                            </div>
                            <div class="tab-pane p-3" id="tab-all-analysis" role="tabpanel">
                                <div id="chart-all-analysis" height="120"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
    <link href="{{ asset('assets/admin/css/main.css') }}" rel="stylesheet" type="text/css" />
    <!-- Datarangepicker css -->
    <link href="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Toastr css -->
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .daterangepicker.opensright:before {
            right:34px;
            left: unset;
        }
        .daterangepicker.opensright:after
        {
            right:35px;
            left: unset;
        }
        #dashdate
        {
            border-bottom: 1px solid;
            border-bottom-color: #aeaeae;
            cursor: pointer;
        }
        #selviewids
        {
            margin-top:-20px;
            border:none;
            background-color: #fafafa;
            color: #292b2c
        }

    </style>
@endpush

@push('scripts')
    <!--  Apex chart JS Library -->
    <script src="{{ asset('assets/admin/plugins/apexcharts/apexcharts.min.js') }}"></script>
    <!-- Toastr Library js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    <!-- Date Range Picker Js -->
    <script src="{{ asset('assets/admin/plugins/datarangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.min.js') }}"></script>
    <script>
        $(function() {
            var start = new Date("{{ $rep_start_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
            start = moment(start);
            var end = new Date("{{ $rep_end_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
            end = moment(end);
            $('#dashdate span').html(start.format('MMMM D, YYYY') + '~' + end.format('MMMM D, YYYY'));
            function cb(cstart, cend) {
               $('#dashdate span').html(cstart.format('MMMM D, YYYY') + '~' + cend.format('MMMM D, YYYY'));

                // Grab the datatables input box and alter how it is bound to events
                start_date = cstart.format('YYYY-MM-DD');
                end_date = cend.format('YYYY-MM-DD');
                start = cstart;
                end = cend;

                $.ajax({
                    url: "{{ route('dashboard.changedate') }}",
                    type : "POST",
                    data : {
                        startDate:start_date,
                        endDate:end_date
                    },
                    success : function(res) {
                        location.href = "{{ route('dashboard') }}";
                    },
                    error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                    }
                });
            }

            $('#dashdate').daterangepicker({
                startDate: start,
                endDate: end,
                showDropdowns: false,
                linkedCalendars: true,
                maxDate: moment().format('MM/DD/YYYY'),
                minDate: moment().subtract(2, 'years').format('MM/DD/YYYY'),
                ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            $('#selviewids').on('change', function(evt)
            {
                let cur_view_id = $('#selviewids').val();
                $.ajax({
                    url: "{{ route('dashboard.changeallviewid') }}",
                    type : "POST",
                    data : {
                        cur_all_view_id: cur_view_id
                    },
                    success : function(res) {
                        location.href = "{{ route('dashboard') }}";
                    },
                    error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                    }
                });
            });
        });

        function getTotalValues(startDate, endDate)
        {
            blockUI();
            $.ajax({
                url: "{{ route('dashboard.gettotal') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {
                    startDate:startDate,
                    endDate:endDate
                },
                success : function(res) {
                    if(res.status === false)
                    {
                        $('#s_spend_total').text('R$ 0');
                        $('#s_rmax_total').text('R$ 0');
                        $('#s_profit_total').text('R$ 0');
                        $('#s_roimax_total').text('0 %');
                    } else
                    {
                        $('#s_spend_total').text('R$ ' + res.s_spent);
                        $('#s_rmax_total').text('R$ ' + res.s_rmax);
                        $('#s_profit_total').text('R$ ' + res.s_lmax);
                        $('#s_roimax_total').text(res.s_roimax + ' %');
                    }
                    $.unblockUI();
                },
                error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                }
            });
        }

        $(document).ready(function() {
            //Init total values//
            //getTotalValues("{{ $rep_start_date }}", "{{ $rep_end_date }}");
            //Apex Chart rendering//

            let colors = ["#00E396", "#000000"];

            let data_recevie = [
                @foreach($send_val as $key=>$val)
                    '{{ $val['total_recevie'] }}',
                @endforeach
            ];

            let data_spent = [
                @foreach($send_val as $key=>$val)
                    '{{ $val['total_spent'] }}',
                @endforeach
            ];

            let data_profit = [
                @foreach($send_val as $key=>$val)
                    '{{ $val['total_profit'] }}',
                @endforeach
            ];

            let data_roi = [
                @foreach($send_val as $key=>$val)
                    '{{ $val['total_roi'] }}',
                @endforeach
            ];

            let labels = [
                @foreach($send_val as $key=>$val)
                    '{{ $key }}',
                @endforeach
            ];

            let options = {
                series: [
                    {
                        name: "{{ __('globals.common.received') }} (R$)",
                        type: "line",
                        data: data_recevie,
                    },
                    {
                        name: "{{ __('globals.common.spent') }} (R$)",
                        type: "line",
                        data: data_spent,
                    }
                ],
                chart: {
                    height: 420,
                    type: "line"
                },
                stroke: {
                    width: [2,2]
                },
                plotOptions: {
                    bar: {
                        columnWidth: "50%"
                    }
                },
                colors: colors,
                dataLabels: {
                    enabled: !0,
                    enabledOnSeries: [0,1]
                },
                labels: labels,
                xaxis: {
                    type: "datetime"
                },
                legend: {
                    offsetY: 7
                },
                grid: {
                    padding: {
                        bottom: 20,
                        left: 30
                    }
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "light",
                        type: "horizontal",
                        shadeIntensity: .25,
                        gradientToColors: 0,
                        inverseColors: !0,
                        opacityFrom: .75,
                        opacityTo: .75,
                        stops: [0, 0, 0]
                    }
                },
                yaxis: [{
                    title: {
                        text: "{{ __('globals.common.received') }} & {{ __('globals.common.spent') }} (R$)"
                    }
                }]
            };
            let apexChart = new ApexCharts(document.querySelector('#chart-received-spend'), options);
            apexChart.render();

            colors = ["#E50000"];

            let options_roi = {
                series: [
                    {
                        name: "{{ __('globals.common.roi_max') }} (R$)",
                        type: "line",
                        data: data_roi,
                    },
                ],
                chart: {
                    height: 420,
                    type: "line"
                },
                stroke: {
                    width: [2,2]
                },
                plotOptions: {
                    bar: {
                        columnWidth: "50%"
                    }
                },
                colors: colors,
                dataLabels: {
                    enabled: !0,
                    enabledOnSeries: [0]
                },
                labels: labels,
                xaxis: {
                    type: "datetime"
                },
                legend: {
                    offsetY: 7
                },
                grid: {
                    padding: {
                        bottom: 20,
                        left: 30
                    }
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "light",
                        type: "horizontal",
                        shadeIntensity: .25,
                        gradientToColors: 0,
                        inverseColors: !0,
                        opacityFrom: .75,
                        opacityTo: .75,
                        stops: [0, 0, 0]
                    }
                },
                yaxis: [{
                    title: {
                        text: "{{ __('globals.common.roi_max') }} (R$)"
                    }
                }]
            };
            let apexChartRoi = new ApexCharts(document.querySelector('#chart-roi'), options_roi);
            apexChartRoi.render();

            colors = ["#727272"];

            let options_profit = {
                series: [
                    {
                        name: "{{ __('globals.common.profit') }} (%)",
                        type: "line",
                        data: data_profit,
                    },
                ],
                chart: {
                    height: 420,
                    type: "line"
                },
                stroke: {
                    width: [2,2]
                },
                plotOptions: {
                    bar: {
                        columnWidth: "50%"
                    }
                },
                colors: colors,
                dataLabels: {
                    enabled: !0,
                    enabledOnSeries: [0]
                },
                labels: labels,
                xaxis: {
                    type: "datetime"
                },
                legend: {
                    offsetY: 7
                },
                grid: {
                    padding: {
                        bottom: 20,
                        left: 30
                    }
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "light",
                        type: "horizontal",
                        shadeIntensity: .25,
                        gradientToColors: 0,
                        inverseColors: !0,
                        opacityFrom: .75,
                        opacityTo: .75,
                        stops: [0, 0, 0]
                    }
                },
                yaxis: [{
                    title: {
                        text: "{{ __('globals.common.profit') }} (R$)"
                    }
                }]
            };
            let apexChartProfit = new ApexCharts(document.querySelector('#chart-profit'), options_profit);
            apexChartProfit.render();

            colors = ["#00E396", "#000000", "#E50000", "#727272"];

            let options_all = {
                series: [
                    {
                        name: "{{ __('globals.common.received') }} (R$)",
                        type: "line",
                        data: data_recevie,
                    },
                    {
                        name: "{{ __('globals.common.spent') }} (R$)",
                        type: "line",
                        data: data_spent,
                    },
                    {
                        name: "{{ __('globals.common.roi_max') }} (%)",
                        type: "line",
                        data: data_roi,
                    },
                    {
                        name: "{{ __('globals.common.profit') }} (R$)",
                        type: "line",
                        data: data_profit,
                    }
                ],
                chart: {
                    height: 420,
                    type: "line"
                },
                stroke: {
                    width: [2,2,2,2]
                },
                plotOptions: {
                    bar: {
                        columnWidth: "50%"
                    }
                },
                colors: colors,
                dataLabels: {
                    enabled: !0,
                    enabledOnSeries: [0,1,2,3]
                },
                labels: labels,
                xaxis: {
                    type: "datetime"
                },
                legend: {
                    offsetY: 7
                },
                grid: {
                    padding: {
                        bottom: 20,
                        left: 30
                    }
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "light",
                        type: "horizontal",
                        shadeIntensity: .25,
                        gradientToColors: 0,
                        inverseColors: !0,
                        opacityFrom: .75,
                        opacityTo: .75,
                        stops: [0, 0, 0]
                    }
                },
                yaxis: [{
                    title: {
                        text: "{{ __('globals.common.all_analysis') }}"
                    }
                }]
            };
            let apexChartAll = new ApexCharts(document.querySelector('#chart-all-analysis'), options_all);
            apexChartAll.render();
        });
    </script>
@endpush