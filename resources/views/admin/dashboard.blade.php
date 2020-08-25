@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')

<div class="page-content-wrapper ">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="m-b-10 list-inline float-right" id="dashdate" style="border-bottom: 1px solid;border-bottom-color: #aeaeae;cursor: pointer;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
                Viewids:
                <select class="minimal" id="selviewids" class="m-b-12 col-md-3 list-inline" style="margin-top:-20px;border:none;background-color: #fafafa;color: #292b2c">
                    @if($cur_view_id == "0")    
                        <option value="0" selected> All View Ids</option>
                    @else
                        <option value="0"> All View Ids</option>
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
                        <span class="counter" id="s_spend_total">R$ 0</span>
                        Total Spends
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="mini-stat clearfix bg-primary">
                    <span class="mini-stat-icon"><i class="mdi mdi-cart-outline"></i></span>
                    <div class="mini-stat-info text-right text-white">
                        <span class="counter" id="s_rmax_total">R$ 0</span>
                        Total Received Max
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="mini-stat clearfix bg-primary">
                    <span class="mini-stat-icon"><i class="mdi mdi-scale-balance"></i></span>
                    <div class="mini-stat-info text-right text-white">
                        <span class="counter" id="s_profit_total">R$ 0</span>
                        Total Profit Max
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="mini-stat clearfix bg-primary">
                    <span class="mini-stat-icon"><i class="mdi mdi-cube-outline"></i></span>
                    <div class="mini-stat-info text-right text-white">
                        <span class="counter" id="s_roimax_total">0 %</span>
                        ROI Max
                    </div>
                </div>
            </div>
        </div>

        <div class="btm-tbl">
            <div class="card m-b-20">
                <div class="card-block">
                    <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab-received-spend" role="tab">Received & Spend</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-roi" role="tab">ROI</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-profit" role="tab">Profit</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-all-analysis" role="tab">All(Received, Spend, ROI, Profit)</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active p-3" id="tab-received-spend" role="tabpanel">
                                <canvas id="chart-received-spend" height="120"></canvas>
                            </div>
                            <div class="tab-pane p-3" id="tab-roi" role="tabpanel">
                                <canvas id="chart-roi" height="120"></canvas>
                            </div>
                            <div class="tab-pane p-3" id="tab-profit" role="tabpanel">
                                <canvas id="chart-profit" height="120"></canvas>
                            </div>
                            <div class="tab-pane p-3" id="tab-all-analysis" role="tabpanel">
                                <canvas id="chart-all-analysis" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--  <div class="container">
            <div class="card m-b-20">
                <div class="card-block">
                    <h4 class="mt-0 m-b-15 header-title">All Analysis Collection</h4>
                    <table id="datatable1" class="table table-bordered table-hover">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>  --}}
    </div>

</div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/morris/morris.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/c3/c3.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/chartist/css/chartist.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-2.0.2.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    </style>
@endpush
 
@push('scripts')
    <script src="{{ asset('assets/admin/plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/raphael/raphael-min.js') }}"></script>

    <script src="{{ asset('assets/admin/plugins/flot-chart/jquery.flot.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/flot-chart/jquery.flot.time.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/flot-chart/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/flot-chart/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/flot-chart/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/flot-chart/jquery.flot.selection.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/flot-chart/jquery.flot.stack.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/flot-chart/curvedLines.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/flot-chart/jquery.flot.crosshair.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/chart.js/chart.min.js') }}"></script>

    <script src="{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/jvectormap/gdp-data.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-us-aea-en.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-uk-mill-en.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-us-il-chicago-mill-en.js') }}"></script>

    <script src="{{ asset('assets/admin/plugins/d3/d3.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/c3/c3.min.js') }}"></script>

    <script src="{{ asset('assets/admin/plugins/chartist/js/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/chartist/js/chartist-plugin-tooltip.min.js') }}"></script>

    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Buttons examples -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.colVis.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Date Range Picker Js -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Toastr Alert Js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
  
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
                let cur_view_id = $('#selviewids').val();
                $.ajax({
                    url: "{{ route('dashboard.changedate') }}",
                    type : "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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

                //getTotalValues(start_date, end_date);
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
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data : {
                        cur_all_view_id: cur_view_id
                    },
                    success : function(res) {
                        console.log(res);
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
                    //$('#datatable1').DataTable().destroy();
                    //$('#datatable1').DataTable({
                    //    "scrollY": '60vh',
                    //    "scrollCollapse": true,
                    //    "order": [[ 1, "desc" ]],
                    //    "searching": false, 
                    //    "info": false
                    //});
                    
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
    
        // Analysis  Chart
        new Chart(document.getElementById("chart-received-spend"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    foreach ($g_benefit as $key => $row){
                        if(session('rep_start_date') == session('rep_end_date'))
                            echo "\"".$key."h"."\",";
                        else
                        echo "\"".$key."\",";
                    }
                    reset($g_benefit);
                    ?>
                ],
                datasets: [
                    { 
                        data: [ 
                            <?php
                                foreach ($g_benefit as $row){
                                    echo $row.",";
                                }
                            ?>
                        ],
                        label: "Received(R$)",
                        borderColor: "#FFF200",
                        fill: false,
                    },
                    { 
                        data: [ 
                            <?php
                                foreach ($t_spent as $row){
                                    echo $row.",";
                                }
                            ?>
                        ],
                        label: "Spent(R$)",
                        borderColor: "#000000",
                        fill: false,
                    }
                ]
            },
            options: {
                title: {
                    display: true,
                },
                responsive: true,
            }
        });


        new Chart(document.getElementById("chart-roi"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    foreach ($roi as $key => $row){
                        if(session('rep_start_date') == session('rep_end_date'))
                            echo "\"".$key."h"."\",";
                        else
                        echo "\"".$key."\",";
                    }
                    reset($roi);
                    ?>
                ],
                datasets: [
                    { 
                        data: [ 
                            <?php
                                foreach ($roi as $row){
                                    echo $row.",";
                                }
                            ?>
                        ],
                        label: "Roi(%)",
                        borderColor: "red",
                        fill: false,
                    }
                ]
            },
            options: {
                title: {
                    display: true,
                },
                responsive: true,
            }
        });

        new Chart(document.getElementById("chart-profit"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    foreach ($profit as $key => $row){
                        if(session('rep_start_date') == session('rep_end_date'))
                            echo "\"".$key."h"."\",";
                        else
                        echo "\"".$key."\",";
                    }
                    reset($profit);
                    ?>
                ],
                datasets: [
                    { 
                        data: [ 
                            <?php
                                foreach ($profit as $row){
                                    echo $row.",";
                                }
                            ?>
                        ],
                        label: "Profit(R$)",
                        borderColor: "#7F7F7F",
                        fill: false,
                    }
                ]
            },
            options: {
                title: {
                    display: true,
                },
                responsive: true,
            }
        });

        new Chart(document.getElementById("chart-all-analysis"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    foreach ($g_benefit as $key => $row){
                        if(session('rep_start_date') == session('rep_end_date'))
                            echo "\"".$key."h"."\",";
                        else
                        echo "\"".$key."\",";
                    }
                    reset($g_benefit);
                    ?>
                ],
                datasets: [
                    { 
                        data: [ 
                            <?php
                                foreach ($g_benefit as $row){
                                    echo $row.",";
                                }
                            ?>
                        ],
                        label: "Received(R$)",
                        borderColor: "#FFF200",
                        fill: false,
                    },
                    { 
                        data: [
                            <?php
                                foreach ($t_spent as $row){
                                    echo $row.",";
                                }
                            ?>
                        ],
                        label: "Spent(R$)",
                        borderColor: "#000000",
                        fill: false,
                    },
                    { 
                        data: [ 
                            <?php
                                foreach ($roi as $row){
                                    echo $row.",";
                                }
                            ?>
                        ],
                        label: "Roi(%)",
                        borderColor: "red",
                        fill: false,
                    },
                    { 
                        data: [ 
                            <?php
                                foreach ($profit as $row){
                                    echo $row.",";
                                }
                            ?>
                        ],
                        label: "Profit(R$)",
                        borderColor: "#7F7F7F",
                        fill: false,
                    }
                ]
            },
            options: {
                title: {
                    display: true,
                },
                responsive: true,
            }
        });

    // Start datatables.init.js
        $(document).ready(function() {
            getTotalValues("{{ $rep_start_date }}", "{{ $rep_end_date }}");
        } );
        
    // End
    
    </script>
@endpush