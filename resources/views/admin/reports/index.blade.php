@extends('admin.layout')
@section('content')
@include('admin.partials.top-bar')

<div class="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                Currency:
                <select class="minimal" id="selcurrency" class="m-b-10 col-md-1 list-inline" style="border:none;background-color: #fafafa;color: #292b2c">
                    @foreach ($currencies as $val)
                        @if($val == $curcurrency)
                            <option value="{{ $val }}" selected>{{ $val }}</option>
                        @else
                            <option value="{{ $val }}">{{ $val }}</option>
                        @endif
                    @endforeach
                </select>
                <div class="m-b-10 list-inline float-right" id="reportrange" style="border-bottom: 1px solid;border-bottom-color: #aeaeae;cursor: pointer;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
                <span class="ml-3"></span>
                Viewids:
                <select class="minimal" id="selviewids" class="m-b-10 col-md-3 list-inline" style="border:none;background-color: #fafafa;color: #292b2c">
                    @foreach ($view_ids as $key => $val)
                        @if($val == $cur_view_id)
                            <option value="{{ $val }}" selected>{{ $val }} ( {{ 'https://'.$view_id_urls[$key] }} )</option>
                        @else
                            <option value="{{ $val }}">{{ $val }} ( {{ 'https://'.$view_id_urls[$key] }} )</option>
                        @endif
                    @endforeach
                </select>
            </div>
                <div class="col-md-6">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-users" role="tab">Users</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-sessions" role="tab">Sessions</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-bouncerate" role="tab">Bounce Rate</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-sessionduration" role="tab">Session Duration</a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active p-3" id="tab-users" role="tabpanel">
                                    <canvas id="line-chart1" width="800" height="450"></canvas>
                                </div>
                                <div class="tab-pane p-3" id="tab-sessions" role="tabpanel">
                                    <canvas id="line-chart2" width="800" height="450"></canvas>
                                </div>
                                <div class="tab-pane p-3" id="tab-bouncerate" role="tabpanel">
                                    <canvas id="line-chart3" width="800" height="450"></canvas>
                                </div>
                                <div class="tab-pane p-3" id="tab-sessionduration" role="tabpanel">
                                    <canvas id="line-chart4" width="800" height="450"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card m-b-20" style="background-color: #67a8e4; color: #fff; height: 431.5px;">
                        <div class="card-block">
                            <h4 class="mt-0 header-title">Active Users right now</h4>
                            <h1>{{ $activeusers }}</h1>
                            <div class="row">
                                <div class="col-md-9">
                                    <p style="font-size: 12px;">Top Active Pages</p>
                                    @if( $activepages !=null )
                                        @foreach($activepages as $page)
                                            @if( strlen($page[0])>35 )
                                                <p style="margin: 0;white-space: nowrap;">{{ substr($page[0],0,15) }}...{{substr($page[0],-10)}}</p>
                                            @else
                                                <p style="margin: 0;white-space: nowrap;">{{ $page[0] }}</p>
                                            @endif
                                        @endforeach
                                        @php
                                            reset($activepages);
                                        @endphp
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <p style="font-size: 12px; text-align: right;">Active Users</p>
                                    @if( $activepages !=null )
                                        @foreach($activepages as $page)
                                        <p style="text-align: right; margin: 0;">{{ $page[1] }}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card m-b-20" style="">
                        <div class="card-block">
                            <h4 class="mt-0 header-title">More users returned to your site in
                            @if(sizeof($return_users) > 0)
                            <?php
                                array_pop($return_users);
                                $users_latestMonth = $return_users[sizeof($return_users)-1][1];
                                $newUser_latestMonth = $return_users[sizeof($return_users)-1][2];
                                $letter_latestMonth = date('F', mktime(0, 0, 0, $return_users[sizeof($return_users)-1][0], 10));
                                $divValue = 1;
                                if($users_latestMonth > 0) $divValue = $users_latestMonth;
                                $return_users_latestMonth = round( ( $users_latestMonth - $newUser_latestMonth ) / $divValue * 100 ,2 );
                                echo $letter_latestMonth.".";
                            ?>
                            </h4>
                            <p style="font-size: 14px;">You had {{ $users_latestMonth }} users, {{ $users_latestMonth - $newUser_latestMonth }} came back in {{ $letter_latestMonth }}, which means {{ $return_users_latestMonth }}% of your users returned to your site.</p>
                            @endif
                            <div><canvas id="bar" height="300"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <p class="text-muted m-b-30 font-14">Sessions by country</p>
                            <div class="row">
                                <div class="col-md-7">
                                    <div id="world-map-users" style="height: 400px;"></div>
                                </div>
                                <div class="col-md-5" style="display: flex; align-items: center;">
                                    <canvas id="bar-chart-horizontal" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <p class="text-muted m-b-30 font-14">What are your top devices ?</p>
                            <div id="morris-donut-example" style="height: 300px"></div>
                            <ul class="list-inline widget-chart m-t-20 text-center">
                                <li>
                                    <i class="mdi mdi-desktop-mac"></i>
                                    <p class="text-muted m-b-0">Desktop</p>
                                    <h4 class=""><b>{{ round( $topdevices[0][1] / $total_devices * 100, 1 ) }}%</b></h4>
                                </li>
                                <li>
                                    <i class="mdi mdi-cellphone-iphone"></i>
                                    <p class="text-muted m-b-0">Mobile</p>
                                    <h4 class=""><b>{{ round( $topdevices[1][1] / $total_devices * 100, 1 ) }}%</b></h4>
                                </li>
                                <li>
                                    <i class="mdi mdi-tablet"></i>
                                    <p class="text-muted m-b-0">Tablet</p>
                                    <h4 class=""><b>{{ round( $topdevices[2][1] / $total_devices * 100, 1 ) }}%</b></h4>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 btm-tbl">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <h4 class="mt-0 header-title">AdSense Campaign Data</h4>
                            <table id="datatable_data" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Souce</th>
                                        <th>Users</th>
                                        <th>Bounce Rate</th>
                                        <th>Pages/Session</th>
                                        <th>Avg. Session Duration</th>
                                        <th id="adsenserev">AdSense Revenue({{ $curcurrency }})</th>
                                        <th>AdSense Ads Clicked</th>
                                        <th>AdSense Page Impressions</th>
                                        <th>AdSense CTR</th>
                                        <th id="adsensecpm">AdSense eCPM({{ $curcurrency }})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Total</th>
                                        <th id="total_1"></th>
                                        <th id="total_2"></th>
                                        <th id="total_3"></th>
                                        <th id="total_4"></th>
                                        <th id="total_5"></th>
                                        <th id="total_6"></th>
                                        <th id="total_7"></th>
                                        <th id="total_8"></th>
                                        <th id="total_9"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="modal_title" aria-hidden="true" id="site_show_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="modal_title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <table id="datatable_site_data" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Souce</th>
                                <th>Users</th>
                                <th>Bounce Rate</th>
                                <th>Pages/Session</th>
                                <th>Avg. Session Duration</th>
                                <th id="site_adsenserev">AdSense Revenue({{ $curcurrency }})</th>
                                <th>AdSense Ads Clicked</th>
                                <th>AdSense Page Impressions</th>
                                <th>AdSense CTR</th>
                                <th id="site_adsensecpm">AdSense eCPM({{ $curcurrency }})</th>
                            </tr>
                        </thead>
                        <tbody id="modal_tbody">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Total</th>
                                <th id="site_total_1"></th>
                                <th id="site_total_2"></th>
                                <th id="site_total_3"></th>
                                <th id="site_total_4"></th>
                                <th id="site_total_5"></th>
                                <th id="site_total_6"></th>
                                <th id="site_total_7"></th>
                                <th id="site_total_8"></th>
                                <th id="site_total_9"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Datarangepicker css -->
    <link href="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        #datatable_data tbody tr:hover {
            background-color: #a9e1fc;
            cursor: pointer;
        }

        .modal-lg
        {
            max-width: 80% !important;
        }

        .daterangepicker.opensright:before {
            right:34px;
            left: unset;
        }
        .daterangepicker.opensright:after
        {
            right:35px;
            left: unset;
        }
        .dt-button.buttons-columnVisibility:nth-child(0)
        {
            display: none;
        }
        .dt-button.buttons-columnVisibility:nth-child(1)
        {
            display: none;
        }

        .dt-button-collection
        {
            z-index: 99999 !important;
        }

        @media only screen and (max-width: 1045px) {
            .dt-buttons.btn-group
            {
                display: none;
            }
        }

        @media only screen and (max-width: 550px) {
            .modal-lg
            {
                max-width: 100% !important;
            }
        }
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
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datarangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.min.js') }}"></script>

    <!-- Toastr Alert Js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>

    <script>
        let baseUrl = "{{ url('/') }}";

        $(function() {

            var start = new Date("{{ $rep_start_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
            start = moment(start);
            var end = new Date("{{ $rep_end_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
            end = moment(end);
            var dtable;

            function cb(cstart, cend) {
                $('#reportrange span').html(cstart.format('MMMM D, YYYY') + '~' + cend.format('MMMM D, YYYY'));
                start_date = cstart.format('YYYY-MM-DD');
                end_date = cend.format('YYYY-MM-DD');
                currency = $('#selcurrency').val();
                curviewid = "";
                curviewid = $('#selviewids').val();

                $('#datatable_data').DataTable().destroy();
                dtable = $('#datatable_data').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "scrollY": '60vh',
                    "scrollX": true,
                    "scrollCollapse": true,
                    "dom": 'Bfrtip',
                    "lengthMenu": [
                        [ 10, 50, 100, 500, 1000],
                        [ '10', '50', '100', '500', '1000' ]
                    ],
                    "columnDefs": [ {
                        targets: 1,
                        render: function ( data, type, row ) {
                            return data.length > 50 ?
                                data.substr( 0, 50 ) +'…' :
                                data;
                        }
                    } ],
                    "buttons": [
                        'pageLength',
                        {
                            "extend": 'collection',
                            "text": 'Export',
                            "buttons": [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                            "fade": true
                        },
                        @if(sizeof(session('permissions')) > 0 && session('permissions')['column_visibility'] == 1)
                        'colvis',
                        @endif
                    ],
                    "ajax": {
                        url: "{{ url('admin/getanalysisjson') }}",
                        data: { 'start_date':start_date, 'end_date':end_date, 'currency': currency, 'curviewid': curviewid}
                        },
                    "columns": [
                        { data: '0', name: 'ga:adContent'},
                        { data: '1', name: 'ga:source'},
                        { data: '2', name: 'ga:users'},
                        { data: '3', name: 'ga:bounceRate'},
                        { data: '4', name: 'ga:sessions'},
                        { data: '5', name: 'ga:avgSessionDuration'},
                        { data: '6', name: 'ga:adsenseRevenue'},
                        { data: '7', name: 'ga:adsenseAdsClicks'},
                        { data: '8', name: 'ga:adsensePageImpressions'},
                        { data: '9', name: 'ga:adsenseCTR'},
                        { data: '10', name: 'ga:adsenseECPM'},
                        ],
                    "order": [[ 2, "asc" ]],
                    drawCallback:function(settings)
                    {
                        let theadTag = $('table#datatable_data').find('thead')[0];
                        if($(theadTag).next().prop("tagName") == 'TFOOT') $(theadTag).next().remove();
                        let resTotal = settings.json.total;
                        let index = 0;
                        $.each(resTotal, function(key, data)
                        {
                            index ++;
                            $('th#total_' + index).text(data);
                        });
                    }
                });


                // Grab the datatables input box and alter how it is bound to events
                $(".dataTables_filter input")
                .unbind() // Unbind previous default bindings
                .bind("keydown", function(e) { // Bind our desired behavior
                    // If the length is 3 or more characters, or the user pressed ENTER, search
                    if(e.keyCode == 13 && this.value != "") {
                        // Call the API search function
                        dtable.search(this.value).draw();
                    }
                    return true;
                });

                $(".dataTables_filter input").on('input', function(e)
                {
                    if(this.value == "") {
                        dtable.search("").draw();
                    }
                    return true;
                });

                start = cstart;
                end = cend;
            }

            $('#reportrange').daterangepicker({
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

            $('#datatable_data tbody').on('click', 'tr', function(evt)
            {
                let cmpid = $(this).find('td').first().text();
                let modal_title = $(this).find('td').eq(1).text();
                $('#modal_title').text(`${modal_title} (${cmpid}) Site Data`);
                $('#datatable_site_data').DataTable().destroy();
                $('#modal_tbody').empty();
                let site_dtable = $('#datatable_site_data').DataTable( {
                    "stateSave": true,
                    "processing": true,
                    "serverSide": true,
                    "lengthMenu": [
                        [ 10, 50, 100, 500, 1000],
                        [ '10', '50', '100', '500', '1000' ]
                    ],
                    dom: 'Bfrtip',
                    "buttons": [
                        'pageLength',
                        'copy', 'csv', 'excel', 'pdf', 'print',
                    ],
                    "scrollY": '40vh',
                    "scrollX": true,
                    "scrollCollapse": true,
                    "columnDefs": [ {
                        targets: 0,
                        visible: false,
                    },{
                        targets: 1,
                        render: function ( data, type, row ) {
                            return data.length > 50 ?
                                data.substr( 0, 50 ) +'…' :
                                data;
                        }
                    } ],
                    "ajax": {
                        url: "{{ route('analysis.sitejson') }}",
                        data: { 'start_date':start_date, 'end_date':end_date, 'currency': currency, 'curviewid': curviewid, 'cmpid': cmpid}
                    },
                    "columns": [
                        { data: '0', name: 'ga:adContent'},
                        { data: '1', name: 'ga:source'},
                        { data: '2', name: 'ga:users'},
                        { data: '3', name: 'ga:bounceRate'},
                        { data: '4', name: 'ga:sessions'},
                        { data: '5', name: 'ga:avgSessionDuration'},
                        { data: '6', name: 'ga:adsenseRevenue'},
                        { data: '7', name: 'ga:adsenseAdsClicks'},
                        { data: '8', name: 'ga:adsensePageImpressions'},
                        { data: '9', name: 'ga:adsenseCTR'},
                        { data: '10', name: 'ga:adsenseECPM'},
                    ],
                    "order": [[ 2, "asc" ]],
                    drawCallback:function(settings)
                    {
                        let theadTag = $('table#datatable_site_data').find('thead')[0];
                        if($(theadTag).next().prop("tagName") == 'TFOOT') $(theadTag).next().remove();
                        let resTotal = settings.json.total;
                        let index = 0;
                        $.each(resTotal, function(key, data)
                        {
                            index ++;
                            $('th#site_total_' + index).text(data);
                        });
                    }
                });

                $(".dataTables_filter input")
                    .unbind() // Unbind previous default bindings
                    .bind("keydown", function(e) { // Bind our desired behavior
                        // If the length is 3 or more characters, or the user pressed ENTER, search
                        if(e.keyCode == 13 && this.value != "") {
                            // Call the API search function
                            site_dtable.search(this.value).draw();
                        }
                        return true;
                    });

                $(".dataTables_filter input").on('input', function(e)
                {
                    if(this.value == "") {
                        site_dtable.search("").draw();
                    }
                    return true;
                });




                $('#site_show_modal').modal({backdrop:'static', keyboard:false, show:true});
            })




            $('#selcurrency').on('change', function(evt)
            {
                $('#adsenserev').text(`AdSense Revenue(${$('#selcurrency').val()})`);
                $('#adsensecpm').text(`AdSense eCPM(${$('#selcurrency').val()})`);
                $('#site_adsenserev').text(`AdSense Revenue(${$('#selcurrency').val()})`);
                $('#site_adsensecpm').text(`AdSense eCPM(${$('#selcurrency').val()})`);
                cb(start, end);
            });

            $('#selviewids').on('change', function(evt)
            {
                let cur_view_id = $('#selviewids').val();
                $.ajax({
                    url: "{{ route('dashboard.changeviewid') }}",
                    type : "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data : {
                        cur_view_id: cur_view_id
                    },
                    success : function(res) {
                        location.href = "{{ route('reports.index') }}";
                    },
                    error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                    }
                });
            });

            cb(start, end);
        });

        // Start flot.init.js
        new Chart(document.getElementById("line-chart1"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    foreach ($ana_users as $row){
                        echo "\"".date("M d",strtotime($row[0]))."\",";
                    }
                    reset($ana_users);
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                            foreach ($ana_users as $row){
                                echo $row[2].",";
                            }
                        ?>
                    ],
                    label: "Users",
                    borderColor: "#3e95cd",
                    fill: false
                }
                ]
            },
            options: {
                title: {
                    display: true,
                }
            }
        });

        new Chart(document.getElementById("line-chart2"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    reset($ana_users);
                    foreach ($ana_users as $row){
                        echo "\"".date("M d",strtotime($row[0]))."\",";
                    }
                    reset($ana_users);
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                            foreach ($ana_users as $row){
                                echo $row[3].",";
                            }
                        ?>
                    ],
                    label: "Sessions",
                    borderColor: "#3e95cd",
                    fill: false
                }
                ]
            },
            options: {
                title: {
                display: true,
                }
            }
        });

        new Chart(document.getElementById("line-chart3"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    reset($ana_users);
                    foreach ($ana_users as $row){
                        echo "\"".date("M d",strtotime($row[0]))."\",";
                    }
                    reset($ana_users);
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                            foreach ($ana_users as $row){
                                echo round($row[4],2).",";
                            }
                        ?>
                    ],
                    label: "Bounce Rate(%)",
                    borderColor: "#3e95cd",
                    fill: false
                }
                ]
            },
            options: {
                title: {
                display: true,
                }
            }
        });

        new Chart(document.getElementById("line-chart4"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    reset($ana_users);
                    foreach ($ana_users as $row){
                        echo "\"".date("M d",strtotime($row[0]))."\",";
                    }
                    reset($ana_users);
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                            foreach ($ana_users as $row){
                                echo round($row[5]).",";
                            }
                        ?>
                    ],
                    label: "Session Duration(Second)",
                    borderColor: "#3e95cd",
                    fill: false
                }
                ]
            },
            options: {
                title: {
                display: true,
                }
            }
        });


    // End

    // Start chartjs.init.js
        !function($) {
            "use strict";

            var ChartJs = function() {};

            ChartJs.prototype.respChart = function(selector,type,data, options) {
                // get selector by context
                var ctx = selector.get(0).getContext("2d");
                // pointing parent container to make chart js inherit its width
                var container = $(selector).parent();

                // enable resizing matter
                $(window).resize( generateChart );

                // this function produce the responsive Chart JS
                function generateChart(){
                    // make chart width fit with its container
                    var ww = selector.attr('width', $(container).width() );
                    switch(type){
                        case 'Line':
                            new Chart(ctx, {type: 'line', data: data, options: options});
                            break;
                        case 'Doughnut':
                            new Chart(ctx, {type: 'doughnut', data: data, options: options});
                            break;
                        case 'Pie':
                            new Chart(ctx, {type: 'pie', data: data, options: options});
                            break;
                        case 'Bar':
                            new Chart(ctx, {type: 'bar', data: data, options: options});
                            break;
                        case 'Radar':
                            new Chart(ctx, {type: 'radar', data: data, options: options});
                            break;
                        case 'PolarArea':
                            new Chart(ctx, {data: data, type: 'polarArea', options: options});
                            break;
                    }
                    // Initiate new chart or Redraw

                }
                // run function - render chart at first load
                generateChart();
            },
            //init
            ChartJs.prototype.init = function() {
                //barchart
                var barChart = {
                    labels: [
                        <?php
                            reset($return_users);
                            foreach($return_users as $key=>$row){
                                echo "\"".substr(date('F', mktime(0, 0, 0, $row[0], 10)),0,3)."\",";
                            }
                            reset($return_users);
                        ?>
                        ],
                    datasets: [
                        {
                            label: "returning users(%)",
                            backgroundColor: "#67a8e4",
                            borderColor: "#67a8e4",
                            borderWidth: 1,
                            hoverBackgroundColor: "#67a8e4",
                            hoverBorderColor: "#67a8e4",
                            data: [
                            @php
                                foreach($return_users as $key=>$row){
                                    if(!isset($row[1]) || empty($row[1])) $row[1] = 0;
                                    if(!isset($row[2]) || empty($row[2])) $row[2] = 0;
                                    $divVal = $row[1] == 0 ? 1 : $row[1];
                                    $val = round(($row[1] - $row[2]) / $divVal * 100, 2);
                                    echo $val.",";
                                }
                            @endphp
                            ]
                        }
                    ]
                };
                this.respChart($("#bar"),'Bar',barChart);

            },
            $.ChartJs = new ChartJs, $.ChartJs.Constructor = ChartJs

        }(window.jQuery),

        //initializing
        function($) {
            "use strict";
            $.ChartJs.init()
        }(window.jQuery);
    // End

    // Start jvectormap.init.js
        !function($) {
            "use strict";

            var VectorMap = function() {};

            VectorMap.prototype.init = function() {
                //various examples
                var country_info = {
                    <?php
                        foreach($users_country as $key=>$row){
                            echo $row[1].":".$row[2].",";
                        }
                    ?>
                };

                $('#world-map-users').vectorMap({
                    map : 'world_mill_en',
                    scaleColors : ['#03a9f4', '#03a9f4'],
                    normalizeFunction : 'polynomial',
                    hoverOpacity : 0.7,
                    hoverColor : false,
                    regionStyle : {
                        initial : {
                            fill : '#c9cfd4'
                        }
                    },
                    markerStyle: {
                        initial: {
                            r: 9,
                            'fill': '#03a9f4',
                            'fill-opacity': 0.9,
                            'stroke': '#fff',
                            'stroke-width' : 7,
                            'stroke-opacity': 0.4
                        },

                        hover: {
                            'stroke': '#fff',
                            'fill-opacity': 1,
                            'stroke-width': 1.5
                        }
                    },
                    series: {
                        regions: [{
                            values: country_info,
                            scale: ['#C8EEFF', '#0071A4'],
                            normalizeFunction: 'polynomial'
                        }]
                    },
                    onRegionTipShow: function(e, el, code){
                        let sessionCnt = country_info[code];
                        if(typeof sessionCnt === 'undefined') sessionCnt = 0;
                        el.html(el.html()+ `&nbsp;<img src=\"${baseUrl}/assets/admin/images/flag/` + code.toString().toLowerCase() + ".png\" style='width: 20px; height: 14px;'/> " + " (Sessions: " + sessionCnt + ")");
                    },

                    backgroundColor : 'transparent',
                });

            },
            //init
            $.VectorMap = new VectorMap, $.VectorMap.Constructor = VectorMap
        }(window.jQuery),

        //initializing
        function($) {
            "use strict";
            $.VectorMap.init()
        }(window.jQuery);
    // End

    // Start morris.init.js
        !function ($) {
            "use strict";

            var Dashboard = function () {
            };

            //creates Donut chart
            Dashboard.prototype.createDonutChart = function (element, data, colors) {
                Morris.Donut({
                    element: element,
                    data: data,
                    resize: true,
                    colors: colors,
                });
            },

            Dashboard.prototype.init = function () {
                //creating donut chart
                var $donutData = [
                    {label: "Tablet", value: <?php echo $topdevices[2][1]; ?> },
                    {label: "Mobile", value: <?php echo $topdevices[1][1]; ?> },
                    {label: "Desktop", value: <?php echo $topdevices[0][1]; ?> }
                ];
                this.createDonutChart('morris-donut-example', $donutData, ['#f0f1f4', '#67a8e4', '#337ab7']);
            },
            //init
            $.Dashboard = new Dashboard
            $.Dashboard.Constructor = Dashboard
        }(window.jQuery),

        //initializing
        function ($) {
            "use strict";
            $.Dashboard.init();
        }(window.jQuery);
    // End

    // Start chartist.init.js
        new Chart(document.getElementById("bar-chart-horizontal"), {
            type: 'horizontalBar',
            data: {
            labels: [
                <?php
                    $i = 0;
                    reset($users_country);
                    foreach($users_country as $key=>$row){
                        echo "\"".$row[0]."\",";
                        $i++;
                        if($i>5) break;
                    }
                ?>
                ],
            datasets: [
                {
                label: "Sessions",
                backgroundColor: "#67a8e4",
                data: [
                <?php
                    $i = 0;
                    reset($users_country);
                    foreach($users_country as $key=>$row){
                        echo $row[2].",";
                        $i++;
                        if($i>5) break;
                    }
                ?>
                ]
                }
            ]
            },
            options: {
            legend: { display: false },
            title: {
                display: true,
            }
            }
        });


        //table.order([ 0, 'desc' ]).draw();
    </script>
@endpush
