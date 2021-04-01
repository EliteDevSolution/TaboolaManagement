@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')

<div class="page-content-wrapper ">
    <div class="container">
        <div class="btm-tbl mt-2">
            <div class="card m-b-20">
                <div class="card-block">
                    <h4 class="ml-3 mt-0 header-title list-inline">
                        <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                    </h4>
                    <h4 class="mt-0 ml-3 header-title row mt-4">
                        <input id="analysis_input" class="form-control col-md-3 mr-2 border-radius-50" autofocus placeholder="{{ __('globals.common.input_analysis_title') }}...">
                        <button class="btn btn-primary waves-effect waves-light mr-2" id="btn_analysis">
                            <i class="ion-search"></i> {{ __('globals.common.analysis_title') }}
                        </button>
                        <button class="btn btn-danger waves-effect waves-light" id="btn_reset">
                            <i class="mdi mdi-refresh"></i> {{ __('globals.common.reset') }}
                        </button>
                    </h4>
                    <div id="title_analysis" class="mt-4 p-3 box effect8"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
    <link href="{{ asset('assets/admin/css/main.css') }}" rel="stylesheet" type="text/css" />
    <!-- Toastr css -->
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #btn_analysis, #btn_reset
        {
            border-radius: 30px ;
            cursor: pointer;
        }
        .border-radius-50
        {
            border-radius: 50px !important;
        }
        tspan
        {
            font-size: 12px;
            font-weight: bold;
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
    <script>
        //Apex Chart rendering//
        let apexChartAnalysis;

        let colors = ["#00E396"];

        let data = [];

        let labels = [];

        if(localStorage.getItem('data_list') != null && localStorage.getItem('data_list') != '')
        {
            data = JSON.parse(localStorage.getItem('data_list'));
        }

        if(localStorage.getItem('labels') != null && localStorage.getItem('labels') != '')
        {
            labels = JSON.parse(localStorage.getItem('labels'));
        }

        {{--let options_roi = {--}}
        {{--    series: [--}}
        {{--        {--}}
        {{--            name: "{{ __('globals.common.predicted_ctr') }}",--}}
        {{--            type: "bar",--}}
        {{--            data: data,--}}
        {{--        },--}}
        {{--    ],--}}
        {{--    chart: {--}}
        {{--        height: 520,--}}
        {{--        type: "bar"--}}
        {{--    },--}}
        {{--    stroke: {--}}
        {{--        width: [2,2]--}}
        {{--    },--}}
        {{--    plotOptions: {--}}
        {{--        bar: {--}}
        {{--            columnWidth: "50%",--}}
        {{--        }--}}
        {{--    },--}}
        {{--    colors: colors,--}}
        {{--    dataLabels: {--}}
        {{--        enabled: !0,--}}
        {{--        enabledOnSeries: [0]--}}
        {{--    },--}}
        {{--    labels: labels,--}}
        {{--    legend: {--}}
        {{--        offsetY: 7--}}
        {{--    },--}}
        {{--    grid: {--}}
        {{--        padding: {--}}
        {{--            bottom: 20,--}}
        {{--            left: 30--}}
        {{--        }--}}
        {{--    },--}}
        {{--    fill: {--}}
        {{--        type: "gradient",--}}
        {{--        gradient: {--}}
        {{--            shade: "light",--}}
        {{--            type: "horizontal",--}}
        {{--            shadeIntensity: .25,--}}
        {{--            gradientToColors: 0,--}}
        {{--            inverseColors: !0,--}}
        {{--            opacityFrom: .75,--}}
        {{--            opacityTo: .75,--}}
        {{--            stops: [0, 0, 0]--}}
        {{--        }--}}
        {{--    },--}}
        {{--    yaxis: [{--}}
        {{--        title: {--}}
        {{--            text: "{{ __('globals.common.predicted_ctr') }}",--}}
        {{--        }--}}
        {{--    }]--}}
        {{--};--}}

        let options_roi = {
            series: [{
                name: "{{ __('globals.common.predicted_ctr') }}",
                data: data,
            }],
            chart: {
                type: 'bar',
                height: 500
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    columnWidth: "50%",
                }
            },
            fill: {
                type: "gradient",
                gradient: {
                    shade: "light",
                    type: "vertical",
                    shadeIntensity: .25,
                    gradientToColors: 0,
                    inverseColors: !0,
                    opacityFrom: .75,
                    opacityTo: .75,
                    stops: [0, 0, 0]
                }
            },
            colors: colors,
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: labels,
                title: {
                    text: "{{ __('globals.common.predicted_ctr') }}",
                }
            }
        };


        $(document).ready(function() {

            $('#btn_reset').click(function(){
                localStorage.setItem('data_list', '');
                localStorage.setItem('labels', '');
                location.reload();
            });

            $('#btn_analysis').click(function()
            {
                let labels_temp = labels.map(function(x){ return x.toUpperCase() });
                if($('#analysis_input').val() == '')
               {
                   toastr.warning('{{ __('globals.msg.input_require') }}', '{{ __('globals.msg.oh_snap') }}');
                   $('#analysis_input').focus();
                   return false;
               } else if(labels_temp.includes($('#analysis_input').val().toUpperCase()) > 0)
               {
                   toastr.warning('{{ __('globals.msg.already_exist') }}', '{{ __('globals.msg.oh_snap') }}');
                   $('#analysis_input').focus();
                   return false;
               } else if(labels_temp.length > 20)
                {
                    oastr.warning('{{ __('globals.msg.analysis_cnt_limit') }}', '{{ __('globals.msg.oh_snap') }}');
                    $('#analysis_input').focus();
                    return false;
                }
               getScore();
            });

            $("#analysis_input").on('keyup', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    let labels_temp = labels.map(function(x){ return x.toUpperCase() });
                    if($(this).val() == '')
                    {
                        toastr.warning('{{ __('globals.msg.input_require') }}', '{{ __('globals.msg.oh_snap') }}');
                        return false;
                    } else if(labels_temp.includes($('#analysis_input').val().toUpperCase()) > 0)
                    {
                        toastr.warning('{{ __('globals.msg.already_exist') }}', '{{ __('globals.msg.oh_snap') }}');
                        return false;
                    } else if(labels_temp.length > 20)
                    {
                        toastr.warning('{{ __('globals.msg.analysis_cnt_limit') }}', '{{ __('globals.msg.oh_snap') }}');
                        $('#analysis_input').focus();
                        return false;
                    }
                    getScore();
                }
            });

            apexChartAnalysis = new ApexCharts(document.querySelector('#title_analysis'), options_roi);
            apexChartAnalysis.render();
        });

        let getScore = () => {
            blockUI();
            let curTitle = $('#analysis_input').val();
            let new_labels = [];
            $.post("{{ route('analysis.site_score') }}", { title: curTitle },
                function (resp, textStatus, jqXHR) {
                    if(resp.result.status == 'ok')
                    {
                        let score = resp.result.score_a;
                        data.push(parseFloat(score.toFixed(3)));
                        labels.push(curTitle);

                        //var old_data = data.slice(0);
                        //data.sort(function(a, b){return b-a});
                        $(labels).each(function(index, item){
                            new_labels.push(  { id: item, data: data[index] } );
                        });

                        new_labels.sort(function(a, b) {
                            return parseFloat(b.data) - parseFloat(a.data);
                        });

                        let newdata = [];
                        let lastlabels = [];

                        $(new_labels).each(function(index, item){
                            lastlabels.push(item.id);
                            newdata.push(item.data);
                        });

                        $('#analysis_input').val('');
                        localStorage.setItem('data_list', JSON.stringify(newdata));
                        localStorage.setItem('labels', JSON.stringify(lastlabels));
                        buildingApex(newdata, lastlabels);
                    } else
                    {
                        toastr.warning('{{ __('globals.msg.operation_fail') }}', '{{ __('globals.msg.oh_snap') }}');
                    }
                    $.unblockUI();
                }
            );
        };

        let buildingApex = (datas, labels) => {

            // apexChartAnalysis.updateOptions(
            //     {
            //         labels: labels,
            //     }
            // );

            apexChartAnalysis.updateOptions(
                {
                    xaxis: {
                        categories: labels
                    }
                }
            );

            apexChartAnalysis.updateSeries([
                {
                    data: datas,
                }
            ]);
        }

    </script>
@endpush