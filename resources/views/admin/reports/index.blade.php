@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')
<div class="page-content-wrapper ">
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
                    <div class="m-b-10 col-md-2 list-inline float-right" id="reportrange" style="border-bottom: 1px solid;border-bottom-color: #aeaeae;cursor: pointer;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>    
            </div>
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        <h4 class="mt-0 header-title">AdSense Campaign Data</h4>
                        <table id="datatable_data" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Souce</th>
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
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('assets/admin/css/main.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/plugins/morris/morris.min.js') }}"></script>
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

    <!-- Datatable init js -->
    <script src="{{ asset('assets/admin/pages/datatables.init.js') }}"></script>

    <!-- Date Range Picker Js -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Myscript -->
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>

    <script>
    
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
                $('#datatable_data').DataTable().destroy();
                dtable = $('#datatable_data').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "scrollY": '60vh',
                    "scrollCollapse": true,
                    "dom": 'Bfrtip',
                    "lengthMenu": [
                        [ 10, 50, 100, 500, 1000],
                        [ '10', '50', '100', '500', '1000' ]
                    ],
                    "buttons": [
                        'pageLength', 
                        {
                            "extend": 'collection',
                            "text": 'Export',
                            "buttons": [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                            "fade": true
                        },
                        'colvis',
                    ],
                    "ajax": {
                        url: "{{ url('admin/getanalysisjson') }}",
                        data: { 'start_date':start_date, 'end_date':end_date, 'currency': currency }
                        },
                    "columns": [
                        { data: '0', name: 'ga:adContent'},
                        { data: '1', name: 'ga:source'},
                        { data: '2', name: 'ga:adsenseRevenue'},
                        { data: '3', name: 'ga:adsenseAdsClicks'},
                        { data: '4', name: 'ga:adsensePageImpressions'},
                        { data: '5', name: 'ga:adsenseCTR'},
                        { data: '6', name: 'ga:adsenseECPM'},
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
                    return;
                });

                $(".dataTables_filter input").on('input', function(e)
                {
                    if(this.value == "") {
                        dtable.search("").draw();
                    }
                    return;
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

            
            $('#selcurrency').on('change', function(evt)
            {
                $('#adsenserev').text(`AdSense Revenue(${$('#selcurrency').val()})`);
                $('#adsensecpm').text(`AdSense eCPM(${$('#selcurrency').val()})`);
                cb(start, end);
            });

            cb(start, end);

        });

        
        //table.order([ 0, 'desc' ]).draw();
    </script>
@endpush
