@extends('admin.layout')
@section('content')
@include('admin.partials.top-bar')
<div class="page-content-wrapper ">
    <div class="container">
        <div class="row">
            <div class="col-12">
                    Currency:
                    <select id="selcurrency" class="minimal m-b-10 col-md-1 list-inline">
                        @foreach ($currencies as $val)
                            @if($val == $curcurrency)
                                <option value="{{ $val }}" selected>{{ $val }}</option>
                            @else
                                <option value="{{ $val }}">{{ $val }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if(sizeof(session('permissions')) > 0 && session('permissions')['currency_setting'] == 1)
                    <button id="currency_setting" data-toggle="popover" onclick="showCurrencySetting(this)" class="btn btn-secondary waves-effect waves-light btn-sm list-inline"><i class="mdi mdi-settings"></i></button>
                    @endif
                    <button id="site_report" data-toggle="popover" onclick="showSiteReport(this)" class="btn btn-secondary waves-effect waves-light btn-sm list-inline ml-2"><i class="mdi mdi-buffer"></i> {{ __('globals.sheet.site_report') }}</button>
                    <div class="m-b-10 list-inline float-right" id="reportrange" style="border-bottom: 1px solid;border-bottom-color: #aeaeae;cursor: pointer;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
            </div>

            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        <h4 class="mt-0 header-title list-inline"><label id="sheet_title" data-id="-1">Sheet & Analysis</label>
                            <a class="mt-0 list-inline float-right" id="btn_back" href="{{ route('sheet.index') }}"><i class="mdi mdi-arrow-left"></i> Back</a>
                        </h4>
                        <div class="row">
                            <select class="minimal m-b-10 col-md-4" id="selcampaigns">
                            </select>
                        </div>
                        <table id="datatable_sheet_data" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th id="th_daily">{{ __('globals.sheet.daily_delivery') }}({{ $curcurrency }})</th>
                                    <th id="th_spent">{{ __('globals.sheet.spent') }}({{ $curcurrency }})</th>
                                    <th id="th_received">{{ __('globals.sheet.has_received') }}({{ $curcurrency }})</th>
                                    <th id="th_received_max">{{ __('globals.sheet.received_max') }}({{ $curcurrency }})</th>
                                    <th>{{ __('globals.sheet.roi_min') }}(%)</th>
                                    <th>{{ __('globals.sheet.roi_max') }}(%)</th>
                                    <th id="th_profit_min">{{ __('globals.sheet.profit') }} Min({{ $curcurrency }})</th>
                                    <th id="th_profit_max">{{ __('globals.sheet.profit') }} Max({{ $curcurrency }})</th>
                                    <th>{{ __('globals.sheet.clicks') }}</th>
                                    <th id="th_bid_actual">{{ __('globals.sheet.bid_actual') }}({{ $curcurrency }})</th>
                                    <th id="th_bid_strategy">{{ __('globals.sheet.bid_strategy') }}({{ $curcurrency }})</th>
                                    <th id="th_bid_max">{{ __('globals.sheet.bid_max') }}({{ $curcurrency }})</th>
                                    <th id="th_margin">{{ __('globals.sheet.margin') }}(%)</th>
                                    <th id="th_start_date" width="8%">{{ __('globals.sheet.start_date') }}</th>
                                    <th id="th_status"></th>
                                </tr>
                            </thead>
                            <tbody id="datatable_body">
                            </tbody>
                            <tfoot id="datatable_foot">
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="modal_title" aria-hidden="true" id="site_report_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="modal_title">{{ __('globals.sheet.site_summery_data') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table id="datatable_site_data" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('globals.sheet.site_id') }}</th>
                        <th>{{ __('globals.sheet.site_name') }}</th>
                        <th>{{ __('globals.ads.viewable_impressions') }}</th>
                        <th>{{ __('globals.ads.vctr') }}</th>
                        <th>{{ __('globals.ads.clicks') }}</th>
                        <th id="th_actual_cpc">{{ __('globals.ads.actual_cpc') }}({{ $curcurrency }})</th>
                        <th id="th_vcpm">{{ __('globals.ads.vcpm') }}({{ $curcurrency }})</th>
                        <th>{{ __('globals.ads.conversion_rate') }}</th>
                        <th>{{ __('globals.ads.conversions') }}</th>
                        <th id="th_cpa">{{ __('globals.ads.cpa') }}({{ $curcurrency }})</th>
                        <th id="th_spent">{{ __('globals.ads.spent') }}({{ $curcurrency }})</th>
                        <th>{{ __('globals.sheet.block_level') }}</th>
                    </tr>
                    </thead>
                    <tbody id="modal_site_tbody">
                    </tbody>
                    <tfoot id="modal_site_tfoot">
                    </tfoot>
                </table>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Switchery css -->
    <link href="{{ asset('assets/admin/plugins/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Datarangepicker css -->
    <link href="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        .modal-lg
        {
            max-width: 80% !important;
        }

        .select2-container {
            border-radius: 2px;
            position: absolute;
            top: 85px;
            left: 580px;
            height: 40px;
            z-index: 1;
        }
        .select2-selection {
            background-color: #fff;
            border: 1px solid #aaa;
            border-radius: 4px;
            height: 38px !important;
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

        .select2-selection__rendered,
        .select2-selection__arrow {
            margin-top: 4px;
        }

        #selcurrency {
            border:none;
            background-color: #fafafa;
            color: #292b2c;
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
            .select2-container
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
    <!-- Swtichery Library js -->
    <script src="{{ asset('assets/admin/plugins/switchery/switchery.min.js') }}"></script>

    <!-- Select2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>

    <!-- Sweetalert2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

    <script>
        let start_date, end_date, currency;
        toastr.options.progressBar = true;
        toastr.options.closeButton = true;
        toastr.options.closeDuration = 300;
        toastr.options.timeOut = 1000; // How long the toast will display without user interaction

        $(function() {

            var start = new Date("{{ $rep_start_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
            start = moment(start);
            var end = new Date("{{ $rep_end_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
            end = moment(end);
            var dtable;

            function cb(cstart, cend) {
               $('#reportrange span').html(cstart.format('MMMM D, YYYY') + '~' + cend.format('MMMM D, YYYY'));

               $('#selcampaigns').hide();

                // Grab the datatables input box and alter how it is bound to events
                start_date = cstart.format('YYYY-MM-DD');
                end_date = cend.format('YYYY-MM-DD');


                if($('#sheet_title').attr('data-id') == "-1")
                {
                    sheetView(cstart, cend);
                } else
                {
                    var margin = $('#selcampaigns option:selected').attr('margin');
                    var cmbid = $('#selcampaigns').val();
                    siteView(cmbid, margin);
                }

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
                $('#th_actual_cpc').text(`{{ __('globals.ads.actual_cpc') }}(${$('#selcurrency').val()})`);
                $('#th_vcpm').text(`{{ __('globals.ads.vcpm') }}(${$('#selcurrency').val()})`);
                $('#th_cpa').text(`{{ __('globals.ads.cpa') }}(${$('#selcurrency').val()})`);
                $('#th_spent').text(`{{ __('globals.ads.spent') }}(${$('#selcurrency').val()})`);



                $('#th_spent').text(`{{ __('globals.sheet.spent') }}(${$('#selcurrency').val()})`);
                $('#th_received').text(`{{ __('globals.sheet.has_received') }}(${$('#selcurrency').val()})`);
                $('#th_received_max').text(`{{ __('globals.sheet.received_max') }}(${$('#selcurrency').val()})`);
                $('#th_profit_min').text(`{{ __('globals.sheet.profit') }} Min(${$('#selcurrency').val()})`);
                $('#th_profit_max').text(`{{ __('globals.sheet.profit') }} Max(${$('#selcurrency').val()})`);
                $('#th_bid_actual').text(`{{ __('globals.sheet.bid_actual') }}(${$('#selcurrency').val()})`);
                if($('#sheet_title').attr('data-id') == "-1")
                {
                    $('#th_bid_max').text(`{{ __('globals.sheet.bid_max') }}(${$('#selcurrency').val()})`);
                } else
                {
                    $('#th_bid_max').text(`{{ __('globals.sheet.bid_amount') }}(${$('#selcurrency').val()})`);
                    $('#th_margin').text(`{{ __('globals.sheet.bid_max') }}(${$('#selcurrency').val()})`);

                }

                cb(start, end);
            });

            $('#selcampaigns').on('change', function(evt)
            {
                var margin = $('option:selected', this).attr('margin');
                var cmbid = $(this).val();
                siteView(cmbid, margin);
            });

            cb(start, end);

        });

        function sheetView(cstart, cend)
        {
            $('#reportrange span').html(cstart.format('MMMM D, YYYY') + '~' + cend.format('MMMM D, YYYY'));
            start_date = cstart.format('YYYY-MM-DD');
            end_date = cend.format('YYYY-MM-DD');
            currency = $('#selcurrency').val();


            blockUI();
            $.ajax({
                url: "{{ route('sheet.gettable') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {
                    start_date:start_date,
                    end_date:end_date,
                    currency:currency,
                },
                success : function(res) {

                    $('#datatable_sheet_data').DataTable().destroy();
                    $('#datatable_body').html(res.data);
                    $('#datatable_foot').html(res.foot);
                    $('#selcampaigns').html(res.selectlist);
                    $('#selcampaigns').select2();
                    $('#sheet_title').attr('bid-admount-limit', res.bidamountlimit);
                    $('#sheet_title').attr('bid-daily-limit', res.dailylimit);

                    dtable = $('#datatable_sheet_data').DataTable({
                        "stateSave": true,
                        "autoWidth": true,
                        "scrollY": '60vh',
                        "scrollCollapse": true,
                        "dom": 'Bfrtip',
                        "bProcessing": true,
                        "responsive": true,
                        "order":[ 2, 'desc' ],
                        "language": {
                            buttons: {
                                pageLength: {
                                    _: "{{ __('globals.datatables.show') }} %d {{ __('globals.datatables.rows') }}",
                                }
                            },
                            paginate: {
                                previous: "<i class='mdi mdi-chevron-left'>",
                                next: "<i class='mdi mdi-chevron-right'>"
                            },
                            info: "{{ __('globals.datatables.showing') }} _START_ {{ __('globals.datatables.to') }} _END_ {{ __('globals.datatables.of') }} _TOTAL_ {{ __('globals.datatables.entries') }}",
                            search: "{{ __('globals.datatables.search') }}:",
                            lengthMenu: "{{ __('globals.datatables.show') }} _MENU_ {{ __('globals.datatables.entries') }}",
                            zeroRecords: "{{ __('globals.datatables.zero_records') }}",
                        },
                        "fixedHeader": {
                            header: true,
                            footer: true
                        },
                        "lengthMenu": [
                            [ 10, 50, 100, 500, 1000],
                            [ '10', '50', '100', '500', '1000' ]
                        ],
                        "buttons": [
                            'pageLength',
                            {
                                text: '{{ __('globals.datatables.all_pause') }}',
                                action: function ( e, dt, node, config ) {
                                    swal({
                                        title: '{{ __('globals.msg.are_you_sure') }}',
                                        text: "{{ __('globals.msg.all_cmp_pause') }}",
                                        type: 'warning',
                                        showCancelButton: true,
                                        confirmButtonClass: 'btn btn-success',
                                        cancelButtonClass: 'btn btn-danger m-l-10',
                                        confirmButtonText: 'Yes, change them!'
                                    }).then(function () {
                                        updateCampaign("all_pause", "", "", function(res)
                                        {
                                            //$('#selcurrency').trigger('change');
                                        });
                                    });
                                }
                            },
                            {
                                "extend": 'collection',
                                "text": '{{ __('globals.datatables.export') }}',
                                "buttons": [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                                "fade": true
                            },
                            @if(sizeof(session('permissions')) > 0 && session('permissions')['column_visibility'] == 1)
                            {
                                "extend": 'colvis',
                                "text": '{{ __('globals.datatables.colvis') }}'
                            }
                            @endif
                        ],
                    });

                    dtable.column( 3 ).visible( false, false ); //Has Rec
                    dtable.column( 5 ).visible( false, false ); //Roi Min
                    dtable.column( 7 ).visible( false, false ); //Profit Min
                    //dtable.column( 9 ).visible( false, false ); //Clicks
                    @if(sizeof(session('permissions')) > 0 && session('permissions')['column_visibility'] == 0)
                    $('.select2-container').css('left', '369px');
                    @endif


                    dtable.columns.adjust().draw( false );
                    $.unblockUI();
                },
                error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                }
            });
        }

        function goSiteData(id)
        {
            $('#selcampaigns').val(id).trigger('change');
        }

        function siteView(id, margin)
        {
            hidePopover();
            blockUI();
            currency = $('#selcurrency').val();

            $.ajax({
                    url: "{{ route('sheet.getsite') }}",
                    type : "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data : {
                        campaign_id:id,
                        start_date:start_date,
                        end_date:end_date,
                        currency:currency,
                        margin:margin,
                    },
                    success : function(res) {
                        let currencyStr = "R$";
                        if(currency == "USD")
                            currencyStr = "$";

                        $('#sheet_title').text(res.cmpname + `(Campaign: ${id}, Spent:${currencyStr} ${res.cmpspent}, CPC:${currencyStr} ${res.cmpbidamount}, Margin: ${margin}%)`);
                        $('#sheet_title').attr('bid-admount', res.cmpbidamount);
                        $('#sheet_title').attr('bid-admount-limit', res.cmpbidamountlimit);

                        $('#sheet_title').attr('data-id', id);
                        $('#datatable_sheet_data').DataTable().destroy();
                        $('#th_bid_max').text(`BID {{ __('globals.common.amount') }}(${$('#selcurrency').val()})`);
                        $('#th_margin').text(`BID MAX(${$('#selcurrency').val()})`);
                        $('#th_start_date').remove();
                        $('#th_daily').remove();
                        $('#th_bid_strategy').remove();

                        $('#datatable_body').html(res.data);
                        $('#datatable_foot').html(res.foot);


                        $('#selcampaigns').val(id);


                        dtable = $('#datatable_sheet_data').DataTable({
                            stateSave: true,
                            "autoWidth": false,
                            "scrollY": '60vh',
                            "scrollX": true,
                            "scrollCollapse": true,
                            "dom": 'Bfrtip',
                            "order":[ 1, 'desc' ],
                            "language": {
                                buttons: {
                                    pageLength: {
                                        _: "{{ __('globals.datatables.show') }} %d {{ __('globals.datatables.rows') }}",
                                    }
                                },
                                paginate: {
                                    previous: "<i class='mdi mdi-chevron-left'>",
                                    next: "<i class='mdi mdi-chevron-right'>"
                                },
                                info: "{{ __('globals.datatables.showing') }} _START_ {{ __('globals.datatables.to') }} _END_ {{ __('globals.datatables.of') }} _TOTAL_ {{ __('globals.datatables.entries') }}",
                                search: "{{ __('globals.datatables.search') }}:",
                                lengthMenu: "{{ __('globals.datatables.show') }} _MENU_ {{ __('globals.datatables.entries') }}",
                                zeroRecords: "{{ __('globals.datatables.zero_records') }}",
                            },
                            "lengthMenu": [
                                [ 10, 50, 100, 500, 1000],
                                [ '10', '50', '100', '500', '1000' ]
                            ],
                            "buttons": [
                                'pageLength',
                                {
                                    text: '{{ __('globals.datatables.auto_change') }}',
                                    action: function ( e, dt, node, config ) {
                                        swal({
                                            title: 'Are you sure?',
                                            text: "You won't be able to revert this!",
                                            type: 'warning',
                                            showCancelButton: true,
                                            confirmButtonClass: 'btn btn-success',
                                            cancelButtonClass: 'btn btn-info',
                                            confirmButtonText: 'Max>Amount',
                                            cancelButtonText: 'Max&ltAmount',
                                            cancelButtonColor: '#d33',
                                            showCloseButton: true,
                                        }).then(function () {
                                            var margin = $('#selcampaigns option:selected').attr('margin');
                                            updateCampaign("auto", "method_1", margin, function(res)
                                            {
                                                console.log(res.status);
                                                if(res.status == true)
                                                {
                                                    $('#selcurrency').trigger('change');
                                                    toastr.success("The operation is success.", "Success!");
                                                }
                                                else
                                                {
                                                    toastr.warning("There is no data applied with automatic change.", "Information!");
                                                    $.unblockUI();
                                                }
                                            });
                                        },function(dismiss) {
                                            var margin = $('#selcampaigns option:selected').attr('margin');
                                            if (dismiss === 'cancel') {
                                                updateCampaign("auto", "method_2", margin, function(res)
                                                {
                                                    if(res.status == true)
                                                    {
                                                        $('#selcurrency').trigger('change');
                                                        toastr.success("The operation is success.", "Success!");
                                                    } else
                                                    {
                                                        toastr.warning("There is no data applied with automatic change.", "Information!");
                                                        $.unblockUI();
                                                    }
                                                });
                                            }
                                        });
                                    }
                                },
                                {
                                    text: '{{ __('globals.datatables.bid_reset') }}',
                                    action: function ( e, dt, node, config ) {
                                        swal({
                                            title: '{{ __('globals.msg.are_you_sure') }}',
                                            text: "Resets the bid amount for all sites in the current campaign to the default value.",
                                            type: 'warning',
                                            showCancelButton: true,
                                            confirmButtonClass: 'btn btn-success',
                                            cancelButtonClass: 'btn btn-danger m-l-10',
                                            confirmButtonText: 'Yes, reset them!'
                                        }).then(function () {
                                            updateCampaign("reset", "", "", function(res)
                                            {
                                                $('#selcurrency').trigger('change');
                                            });
                                        });
                                    }
                                },
                                {
                                    "extend": 'collection',
                                    "text": '{{ __('globals.datatables.export') }}',
                                    "buttons": [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                                    "fade": true
                                },
                                @if(sizeof(session('permissions')) > 0 && session('permissions')['column_visibility'] == 1)
                                {
                                    "extend": 'colvis',
                                    "text": '{{ __('globals.datatables.colvis') }}'
                                }
                                @endif
                            ],
                        });

                        dtable.column( 2 ).visible( false, false ); //Has Rec
                        dtable.column( 4 ).visible( false, false ); //Roi Min
                        dtable.column( 6 ).visible( false, false ); //Profit Min

                        dtable.columns.adjust().draw( false );
                        @if(sizeof(session('permissions')) > 0 && session('permissions')['column_visibility'] == 1)
                        $('.select2-container').css('left', '830px');
                        $('.select2-container').css('width', '400px');
                        @else
                        $('.select2-container').css('left', '667px');
                        @endif
                        $.unblockUI();
                    },
                    error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                    }
                });
        }

        function showMarginPopover(obj)
        {
            let cmp_id = $(obj).attr('id');
            let cur_date = $(obj).attr('date-last');
            let cur_val = parseFloat($(obj).text());
            $('[data-toggle="popover"]').popover('dispose');

            var contentHtml = `
                <div data-toggle='popover_div'>
                    <div for="" class="control-label popupcelleditor-label mb-2 header-title">Margin Percent </div>
                    <label class="radio">
                        <span style="vertical-align: top">Value: </span>
                        <input type="number" min="0" max="400" require id="margin_${cmp_id}" value="${cur_val}" style="text-align: right;">
                        <span class="add-on">%</span>
                    </label>
                </div>
                <div class="form-actions float-right mb-1">
                    <button name="save" class="btn btn-secondary" date-last="${cur_date}" data-id="${cmp_id}" onclick="saveMargin(this)">
                    OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                    <button data-novalidate="" class="btn btn-secondary" type="submit" onclick="hidePopover()">
                    Cancel</button>
                </div>`;

                $(obj).popover({
                    animation: false,
                    html: true,
                    sanitize: false,
                    placement: 'left',
                    trigger: 'manual',
                    content: contentHtml,
                });

                $(obj).popover('show');
        }

        function showDailyPopover(obj)
        {
            let cmp_id = $(obj).attr('cmp-id');
            let cur_val = parseFloat($(obj).attr('data-value'));
            $('[data-toggle="popover"]').popover('dispose');
            let currency = $('#selcurrency').val();
            let currencyStr = 'R$';

            if(currency == "USD") currencyStr = '$';

            var contentHtml = `
                <div data-toggle='popover_div'>
                    <div for="" class="control-label popupcelleditor-label mb-2 header-title">Daily Delivery</div>
                    <label class="radio">
                        <span style="vertical-align: top">Value: </span>
                        <input type="number" min="0" max="10000" require id="daily_${cmp_id}" value="${cur_val}" style="text-align: right;">
                        <span class="add-on"> ${currencyStr}</span>
                    </label>
                </div>
                <div class="form-actions float-right mb-1">
                    <button name="save" class="btn btn-secondary" data-id="${cmp_id}" onclick="saveDaily(this)">
                    OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                    <button data-novalidate="" class="btn btn-secondary" type="submit" onclick="hidePopover()">
                    Cancel</button>
                </div>`;

                $(obj).popover({
                    animation: false,
                    html: true,
                    sanitize: false,
                    placement: 'left',
                    trigger: 'manual',
                    content: contentHtml,
                });

                $(obj).popover('show');
        }

        function showStrategyPopover(obj)
        {
            let cmp_id = $(obj).attr('cmp-id');
            let cur_val = parseFloat($(obj).attr('data-value'));
            $('[data-toggle="popover"]').popover('dispose');
            let currency = $('#selcurrency').val();
            let currencyStr = 'R$';

            if(currency == "USD") currencyStr = '$';

            var contentHtml = `
                <div data-toggle='popover_div'>
                    <div for="" class="control-label popupcelleditor-label mb-2 header-title">Bid Strategy</div>
                    <label class="radio">
                        <span style="vertical-align: top">Value: </span>
                        <input type="number" min="0" max="10" step="0.001" require id="strategy_${cmp_id}" value="${cur_val}" style="text-align: right;">
                        <span class="add-on"> ${currencyStr}</span>
                    </label>
                </div>
                <div class="form-actions float-right mb-1">
                    <button name="save" class="btn btn-secondary" data-id="${cmp_id}" onclick="saveStrategy(this)">
                    OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                    <button data-novalidate="" class="btn btn-secondary" type="submit" onclick="hidePopover()">
                    Cancel</button>
                </div>`;

                $(obj).popover({
                    animation: false,
                    html: true,
                    sanitize: false,
                    placement: 'left',
                    trigger: 'manual',
                    content: contentHtml,
                });

                $(obj).popover('show');
        }

        function saveDaily(obj)
        {
            var cmp_id = $(obj).attr('data-id');
            var dailyVal = parseFloat($(`#daily_${cmp_id}`).val());
            var dailyMin  = parseFloat($('#sheet_title').attr('bid-daily-limit'));
            console.log(cmp_id);
            console.log(dailyVal);
            console.log(dailyMin);

            var dailyMax = 10000;

            if(dailyVal < dailyMin || dailyVal > dailyMax || isNaN(dailyVal))
            {
                $('#daily_' + cmp_id).focus();
                toastr.warning(`Error while saving update: Daily Delivery value is invalid.`, 'Warning!');
                return false;
            }

            let currency = $('#selcurrency').val();

            hidePopover();
            blockUI();
            $.ajax({
                url: "{{ route('sheet.setcmpdaily') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {
                    value:dailyVal,
                    cmp_id:cmp_id,
                    currency:currency
                },
                success : function(res) {

                    let currencyStr = 'R$';

                    if(currency == "USD") currencyStr = '$';
                    $('#bid_daily_' + cmp_id).attr('data-value', res.daily_cap);
                    $('#bid_daily_' + cmp_id).text(currencyStr + ' ' + res.f_daily_cap);

                    $.unblockUI();
                    toastr.success("The operation is success.", "Success!");
                },
                error: function (request, status, error) {
                    toastr.error("Data loading error!", "Error");
                    $.unblockUI();
                }
            });

        }

        function saveStrategy(obj)
        {
            var cmp_id = $(obj).attr('data-id');
            var strategyVal = parseFloat($(`#strategy_${cmp_id}`).val());
            var strategyMin  = parseFloat($('#sheet_title').attr('bid-admount-limit'));
            var strategyMax = 10;

            if(strategyVal < strategyMin || strategyVal > strategyMax || isNaN(strategyVal))
            {
                $('#strategy_' + cmp_id).focus();
                toastr.warning(`Error while saving update: Bid Strategy value is invalid.`, 'Warning!');
                return false;
            }

            let currency = $('#selcurrency').val();

            hidePopover();
            blockUI();
            $.ajax({
                url: "{{ route('sheet.setcmpstrategy') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {
                    value:strategyVal,
                    cmp_id:cmp_id,
                    currency:currency
                },
                success : function(res) {

                    let currencyStr = 'R$';

                    if(currency == "USD") currencyStr = '$';
                    $('#bid_strategy_' + cmp_id).attr('data-value', res.bid_strategy);
                    $('#bid_strategy_' + cmp_id).html(`${currencyStr} ${res.bid_strategy}<br> (${res.bid_type})`);

                    $.unblockUI();
                    toastr.success("The operation is success.", "Success!");
                },
                error: function (request, status, error) {
                    toastr.error("Data loading error!", "Error");
                    $.unblockUI();
                }
            });
        }

        function saveMargin(obj)
        {
            let cur_date = $(obj).attr('date-last');
            let cmp_id = $(obj).attr('data-id');
            let margin_value = parseFloat($('#margin_' + cmp_id).val());


            if(margin_value < 0 || margin_value > 100 || isNaN(margin_value))
            {
                $('#boost_' + cmp_id).focus();
                toastr.warning(`Error while saving update: Margin value has to be between 0% ~ 100%`, 'Warning!');
                return false;
            }

            hidePopover();
            blockUI();
            $.ajax({
                url: "{{ route('sheet.setcmpmargin') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {
                    cur_date:cur_date,
                    value:margin_value,
                    cmp_id:cmp_id,
                },
                success : function(res) {

                    let currency = $('#selcurrency').val();
                    let currencyStr = 'R$';

                    if(currency == "USD") currencyStr = '$';

                    let pre_margin_val = parseFloat($('td#' + cmp_id).text());
                    let pre_bid_max = parseFloat($('td#' + cmp_id).prev().text().replace(`${currencyStr} `,''));
                    let cur_bid_max = pre_bid_max / ((100-pre_margin_val) / 100) * ((100 - margin_value) / 100);
                    cur_bid_max = Math.round((cur_bid_max + Number.EPSILON) * 1000) / 1000;
                    $(`#selcampaigns option[value=${cmp_id}]`).attr('margin', margin_value);

                    $("td#" + cmp_id).text(margin_value + ' %');
                    $("td#" + cmp_id).prev().text(currencyStr + ' ' + cur_bid_max);
                    $.unblockUI();
                    toastr.success("The operation is success.", "Success!");
                },
                error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                }
            });
        }


        function showPopover(obj)
        {
            let bid_amount = $('#sheet_title').attr('bid-admount');
            let currency = $('#selcurrency').val();
            let site_id = $(obj).attr('id');
            let boost = $(obj).attr('boost');
            let site_name = $(obj).attr('data-id');

            $('[data-toggle="popover"]').popover('dispose');

            var contentHtml = `
                <div data-toggle='popover_div'>
                    <div for="" class="control-label popupcelleditor-label mb-2 header-title">Bid Amount: </div>
                    <label class="radio">
                        <input type="radio" value="noValue" name="cpc_boost_radio" checked="checked" data-id="${site_id}" onclick="radioStatus(this)">
                        <span class="radio-label">Campaign Bid Amount (${bid_amount}${currency})</span>
                    </label>
                    <label class="radio">
                        <input type="radio" value="custom" name="cpc_boost_radio" data-id="${site_id}" onclick="radioStatus(this)">
                        <span style="vertical-align: top">Custom Boost</span>
                        <input type="number" min="-99" max="100" disabled="disabled" class="col-md-4" id="boost_${site_id}" style="text-align:right;">
                        <span class="add-on">%</span>
                    </label>
                </div>
                <div class="form-actions float-right mb-1">
                    <button name="save" class="btn btn-secondary" boost="${boost}" data-id="${site_id}" site-id="${site_name}" onclick="saveBidAmount(this)">
                    OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                    <button data-novalidate="" class="btn btn-secondary" type="submit" onclick="hidePopover()">
                    Cancel</button>
                </div>`;

            if(parseInt(boost) != 0)
            {
                contentHtml = `
                <div data-toggle='popover_div'>
                    <div for="" class="control-label popupcelleditor-label mb-2 header-title">Bid Amount: </div>
                    <label class="radio">
                        <input type="radio" value="noValue" name="cpc_boost_radio" data-id="${site_id}" onclick="radioStatus(this)">
                        <span class="radio-label">Campaign Bid Amount (${bid_amount}${currency})</span>
                    </label>
                    <label class="radio">
                        <input type="radio" value="custom" name="cpc_boost_radio" checked="checked" data-id="${site_id}" onclick="radioStatus(this)">
                        <span style="vertical-align: top">Custom Boost</span>
                        <input type="number" min="-99" max="100" value="${boost}" class="col-md-4" id="boost_${site_id}" style="text-align:right;">
                        <span class="add-on">%</span>
                    </label>
                </div>
                <div class="form-actions float-right mb-1">
                    <button name="save" class="btn btn-secondary" boost="${boost}" data-id="${site_id}" site-id="${site_name}" onclick="saveBidAmount(this)">
                    OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                    <button data-novalidate="" class="btn btn-secondary" type="submit" onclick="hidePopover()">
                    Cancel</button>
                </div>`;
            }

            $(obj).popover({
                animation: false,
                html: true,
                sanitize: false,
                placement: 'left',
                trigger: 'manual',
                content: contentHtml,
            });

            $(obj).popover('show');
        }

        function radioStatus(obj)
        {
            let curVal = $(obj).val();
            let site_id = $(obj).attr('data-id');
            if(curVal == 'noValue')
                $('#boost_' + site_id).attr('disabled', true);
            else if(curVal == 'custom')
                $('#boost_' + site_id).attr('disabled', false);
        };

        function radioCurrencyStatus(obj)
        {
            let curVal = $(obj).val();
            if(curVal == 'auto')
            {
                $('#currency_min').attr('disabled', true);
                $('#currency_max').attr('disabled', true);
            }
            else if(curVal == 'manual')
            {
                $('#currency_min').attr('disabled', false);
                $('#currency_max').attr('disabled', false);
            }
        }

        function siteActivate(obj)
        {
            let status = $(obj).attr('status');
            let siteid = $(obj).attr('site-id');
            let value = 1;  //1:play, 0:pause
            if(status == "play")
                value = 1;
            else if(status == "pause")
                value = 0;

            blockUI();
            $.ajax({
                url: "{{ route('sheet.sitechangestatus') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {
                    siteid:siteid,
                    status:value
                },
                success : function(res) {
                    $.unblockUI();
                    if(value == 0)
                    {
                        $(obj).attr('class', 'btn btn-success waves-effect waves-light btn-sm');
                        $(obj).attr('status', 'play');
                        $(obj).html('<i class="mdi mdi-play"></i>');
                    }
                    else if(value == 1)
                    {
                        $(obj).attr('class', 'btn btn-danger waves-effect waves-light btn-sm');
                        $(obj).attr('status', 'pause');
                        $(obj).html('<i class="mdi mdi-pause"></i>');
                    }
                    toastr.success("The operation is success.", "Success!");
                },
                error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                }
            });
        }

        function setSiteBlock(obj)
        {
            let status = $(obj).attr('status');
            let site_name = $(obj).attr('data-id');

            let send_status = status == 'unblock' ? 'block' : 'unblock';

            updateCampaign("blocking", site_name, send_status, function(res)
            {
                if(status == 'unblock')
                {
                    $(obj).attr('status', 'block');
                    $(obj).attr('class', 'btn btn-success waves-effect waves-light btn-sm');
                    $(obj).html('<i class="mdi mdi-reload"></i>');
                } else
                {
                    $(obj).attr('status', 'unblock');
                    $(obj).attr('class', 'btn btn-danger waves-effect waves-light btn-sm');
                    $(obj).html('<i class="mdi mdi-block-helper"></i>');
                }
            });
        }

        function bidDecrease(obj)
        {
            let site_name = $(obj).attr('data-id');
            let site_id = $(obj).attr('site-id');
            let cur_boost = $('a#' + site_id).attr('boost');
            let dec_boost_pro = 100 + parseInt(cur_boost) - 10;

            let init_boost = parseFloat($('#sheet_title').attr('bid-admount'));
            let boost_limit = parseFloat($('#sheet_title').attr('bid-admount-limit'));

            let cur_boost_val = parseFloat(init_boost) * dec_boost_pro / 100;
            cur_boost_val = Math.round((cur_boost_val + Number.EPSILON) * 1000) / 1000;


            if(dec_boost_pro - 100 < -99 || dec_boost_pro - 100> 100)
            {
                toastr.warning(`Error while saving update: Cpc value has to be between -99% ~ 100%`);
                return false;
            }


            if(cur_boost_val < boost_limit)
            {
                toastr.warning(`Error while saving update: Cpc value has to be greater than ${boost_limit} ${currency}`);
                return false;
            }

            let currency = $('#selcurrency').val();
            let currencyStr = 'R$';

            if(currency == "USD") currencyStr = '$';

            updateCampaign("boost", site_name , dec_boost_pro/100, function(res)
            {
                if(dec_boost_pro == 100)
                {
                     $('a#' + site_id).attr('boost', 0);
                     $('a#' + site_id).text(`${currencyStr} ${cur_boost_val}(Default)`);

                } else {
                      $('a#' + site_id).attr('boost', dec_boost_pro - 100);
                      $('a#' + site_id).text(`${currencyStr} ${cur_boost_val}(${dec_boost_pro - 100}%)`);
                }
            });
        }

        function bidIncrease(obj)
        {
            let site_name = $(obj).attr('data-id');
            let site_id = $(obj).attr('site-id');
            let cur_boost = $('a#' + site_id).attr('boost');
            let inc_boost_pro = 100 + parseInt(cur_boost) + 10;

            let init_boost = parseFloat($('#sheet_title').attr('bid-admount'));
            let boost_limit = parseFloat($('#sheet_title').attr('bid-admount-limit'));

            let cur_boost_val = parseFloat(init_boost) * inc_boost_pro / 100;

            cur_boost_val = Math.round((cur_boost_val + Number.EPSILON) * 1000) / 1000;


            if(inc_boost_pro - 100 < -99 || inc_boost_pro - 100> 100)
            {
                toastr.warning(`Error while saving update: Cpc value has to be between -99% ~ 100%`);
                return false;
            }


            if(cur_boost_val < boost_limit)
            {
                toastr.warning(`Error while saving update: Cpc value has to be greater than ${boost_limit} ${currency}`);
                return false;
            }

            let currency = $('#selcurrency').val();
            let currencyStr = 'R$';

            if(currency == "USD") currencyStr = '$';

            updateCampaign("boost", site_name , inc_boost_pro/100, function(res)
            {
                if(inc_boost_pro == 100)
                {
                     $('a#' + site_id).attr('boost', 0);
                     $('a#' + site_id).text(`${currencyStr} ${cur_boost_val}(Default)`);

                } else {
                      $('a#' + site_id).attr('boost', inc_boost_pro - 100);
                      $('a#' + site_id).text(`${currencyStr} ${cur_boost_val}(${inc_boost_pro - 100}%)`);
                }
                if(parseFloat(cur_boost_val) > parseFloat(boost_limit))
                {
                    $('#btn_dec_' + site_id).removeAttr('disabled');
                    $('#btn_dec_' + site_id).attr('onclick', "bidDecrease(this)");
                }
            });

        }

        function showSiteReport(obj) {
            hidePopover();
            $('#modal_site_tbody').hide();
            $('#modal_site_tbody').empty();
            $('#modal_site_tfoot').empty();
            $('#site_report_modal').modal({backdrop:'static', keyboard:false, show:true});
            blockUI();
            $.post("{{ route('sheet.summery_report') }}", { currency: $('#selcurrency').val() },
                function (resp,textStatus, jqXHR) {
                    $.unblockUI();
                    if(resp.status == 200)
                    {
                        $('#datatable_site_data').DataTable().destroy();

                        $('#modal_site_tbody').html(resp.content_html);
                        $('#modal_site_tfoot').html(resp.total_html);
                        let elems = $('[data-plugin="switchery"]');
                        for (var i = 0; i < elems.length; i++) {
                            let init = new Switchery(elems[i], {size:'small'});
                        }

                        $('[data-plugin="switchery"]').on('change', function(evt)
                        {
                            let status = this.checked;
                            let site = $(this).attr('data-id');
                            blockUI();
                            $.post("{{ route('sheet.sitechangeaccountlevel') }}", {site: site, value: status},
                                function (resp,textStatus, jqXHR) {
                                    $.unblockUI();
                                    if(resp.status === 200)
                                        toastr.success("{{ __('globals.msg.operation_success') }}", "{{ __('globals.msg.well_done') }}");
                                    else
                                        toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                                }
                            ).fail(function(res) {
                                $.unblockUI();
                                toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                            });
                        });


                        let m_table = $('#datatable_site_data').DataTable({
                            "processing": true,
                            "scrollY": '40vh',
                            "scrollX": true,
                            "lengthMenu": [
                                [ 10, 50, 100, 500, 1000],
                                [ '10', '50', '100', '500', '1000' ]
                            ],
                            dom: 'Bfrtip',
                            "buttons": [
                                'pageLength',
                                'copy', 'csv', 'excel', 'pdf', 'print',
                            ],
                            "order": [[ 3, "desc" ]],
                            "initComplete": function(settings, json) {
                                $('#modal_site_tbody').show();
                                setTimeout(function() { m_table.search('').draw(); }, 50);
                            }
                        });



                    } else {
                        $('#site_report_modal').modal('toggle');
                        toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                    }

                }
            ).fail(function(res) {
                $.unblockUI();
                toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
            });

        }


        function showCurrencySetting(obj)
        {
            blockUI();
            hidePopover();
            $.ajax({
                url: "{{ route('sheet.getcurrency') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success : function(res) {
                    let type = parseInt(res.type);
                    let maxVal = res.maxval;
                    let minVal = res.minval;
                    var contentHtml = `
                        <div data-toggle='popover_div'>
                            <div for="" class="control-label popupcelleditor-label mb-2 header-title">Currecy Setting(USD:BRL) </div>
                            <label class="radio">
                                <input type="radio" value="auto" name="currency_rate_radio" checked="checked" onclick="radioCurrencyStatus(this)">
                                <span class="radio-label">Auto Currency</span>
                            </label>
                            <div style="margin-bottom:5px;">
                                <span style="vertical-align: top; margin-right:2px;">Min Value: {{ session('currency_BRL') }}</span>
                            </div>
                            <div style="margin-bottom:10px;">
                                <span style="vertical-align: top">Max Value: {{ session('currency_max_BRL') }}</span>
                            </div>
                            <label class="radio">
                                <input type="radio" value="manual" name="currency_rate_radio" onclick="radioCurrencyStatus(this)">
                                <span style="vertical-align: top">Manual Currency</span>
                            </label>
                            <div style="margin-bottom:5px;">
                                <span style="vertical-align: top; margin-right:2px;">Min Value: </span>
                                <input type="number" min="0" max="100" step="any" style="text-align:right;" value="${minVal}" disabled="disabled" id="currency_min" class="col-md-8">
                            </div>
                            <div style="margin-bottom:10px;">
                                <span style="vertical-align: top">Max Value: </span>
                                <input type="number" min="0" max="100" step="any" style="text-align:right;" value="${maxVal}" disabled="disabled" id="currency_max" class="col-md-8">
                            </div>
                        </div>
                        <div class="form-actions float-right mb-1">
                            <button name="save" class="btn btn-secondary" onclick="saveCurrencyData()">
                            OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                            <button data-novalidate="" class="btn btn-secondary" onclick="hidePopover()">
                            Cancel</button>
                        </div>`;

                    if(type == 1)
                    {
                        contentHtml = `
                        <div data-toggle='popover_div'>
                            <div for="" class="control-label popupcelleditor-label mb-2 header-title">Currecy Setting(USD:BRL) </div>
                            <label class="radio">
                                <input type="radio" value="auto" name="currency_rate_radio" onclick="radioCurrencyStatus(this)">
                                <span class="radio-label">Auto Currency</span>
                            </label>
                            <div style="margin-bottom:5px;">
                                <span style="vertical-align: top; margin-right:2px;">Min Value: {{ session('currency_BRL') }}</span>
                            </div>
                            <div style="margin-bottom:10px;">
                                <span style="vertical-align: top">Max Value: {{ session('currency_max_BRL') }}</span>
                            </div>
                            <label class="radio">
                                <input type="radio" value="manual" name="currency_rate_radio" checked="checked"  onclick="radioCurrencyStatus(this)">
                                <span style="vertical-align: top">Manual Currency</span>
                            </label>
                            <div style="margin-bottom:5px;">
                                <span style="vertical-align: top; margin-right:2px;">Min Value: </span>
                                <input type="number" min="0" max="100" step="any" style="text-align:right;" value="${minVal}"  id="currency_min" class="col-md-8">
                            </div>
                            <div style="margin-bottom:10px;">
                                <span style="vertical-align: top">Max Value: </span>
                                <input type="number" min="0" max="100" step="any" style="text-align:right;" value="${maxVal}"  id="currency_max" class="col-md-8">
                            </div>
                        </div>
                        <div class="form-actions float-right mb-1">
                            <button name="save" class="btn btn-secondary" onclick="saveCurrencyData()">
                            OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                            <button data-novalidate="" class="btn btn-secondary" onclick="hidePopover()">
                            Cancel</button>
                        </div>`;
                    }
                    $.unblockUI();
                    $(obj).popover({
                        animation: false,
                        html: true,
                        sanitize: false,
                        placement: 'bottom',
                        trigger: 'manual',
                        content: contentHtml,
                    });

                    $(obj).popover('show');
                },
            });
        }

        function saveBidAmount(obj)
        {
            let site_name = $(obj).attr('site-id');
            let site_id = $(obj).attr('data-id');
            let cur_boost = $(obj).attr('boost');

            let boost_val = $('#sheet_title').attr('bid-admount');
            let boost_limit = $('#sheet_title').attr('bid-admount-limit');


            let boost_send_val = 1;

            if(parseInt(cur_boost) === 0 && $('#boost_' + site_id).attr('disabled') == 'disabled')
            {
                $('[data-toggle="popover"]').popover('dispose');
                return false;
            }


            if($('#boost_' + site_id).attr('disabled') != 'disabled')
            {
                if($('#boost_' + site_id).val() == "" || parseInt($('#boost_' + site_id).val()) === 0)
                {
                    $('#boost_' + site_id).focus();
                    return false;
                }
                boost_val = (100 + parseInt($('#boost_' + site_id).val()))/100*boost_val;
                boost_send_val = (100 + parseInt($('#boost_' + site_id).val())) / 100;
                boost_val = Math.round((boost_val + Number.EPSILON) * 1000) / 1000;

            }

            if(parseInt($('#boost_' + site_id).val()) < -99 || parseInt($('#boost_' + site_id).val()) > 100)
            {
                $('#boost_' + site_id).focus();
                toastr.warning(`Error while saving update: Cpc value has to be between -99% ~ 100%`);
                return false;
            }


            if(parseFloat(boost_val) < parseFloat(boost_limit))
            {
                $('#boost_' + site_id).focus();
                toastr.warning(`Error while saving update: Cpc value has to be greater than ${boost_limit} ${currency}`);
                return false;
            }


            cur_pro = $('#boost_' + site_id).val();
            $('[data-toggle="popover"]').popover('dispose');
            let currency = $('#selcurrency').val();
            let currencyStr = 'R$';

            if(currency == "USD") currencyStr = '$';

            updateCampaign("boost", site_name , boost_send_val, function(res)
            {
                if(boost_send_val === 1)
                {
                    $('a#' + site_id).text(`${currencyStr} ${boost_val}(Default)`);
                    $('a#' + site_id).attr('boost', 0);
                    $(obj).attr('boost', 0);
                } else
                {
                    $('a#' + site_id).text(`${currencyStr} ${boost_val}(${cur_pro}%)`);
                    $('a#' + site_id).attr('boost', cur_pro);
                    $(obj).attr('boost', cur_pro);
                }
                if(parseFloat(boost_val) > parseFloat(boost_limit))
                {
                    $('#btn_dec_' + site_id).removeAttr('disabled');
                    $('#btn_dec_' + site_id).attr('onclick', "bidDecrease(this)");
                }
            });
        }

        function saveCurrencyData()
        {
            let type = 0;   //type = auto
            if($('input[name="currency_rate_radio"]:checked').val() == "manual") //type = manual
                type = 1;
            let minVal = parseFloat($('#currency_min').val());
            let maxVal = parseFloat($('#currency_max').val());
            if(type == 1)
            {
                if(minVal < 0 || maxVal < 0 || isNaN(minVal) || isNaN(maxVal))
                {
                    toastr.warning(`Error while saving update: Currency values has not null.`, 'Warning!');
                    $('#currency_min').focus();
                    return false;
                }

                if(minVal > 100 || maxVal > 100)
                {
                    toastr.warning(`Error while saving update: Currency values have to less than 100.`, 'Warning!');
                    $('#currency_min').focus();
                    return false;
                }
            }

            hidePopover();
            blockUI();
            $.ajax({
                url: "{{ route('sheet.setcurrency') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {
                    type:type,
                    min_value:minVal,
                    max_value:maxVal,
                },
                success : function(res) {
                    $.unblockUI();
                    $('#selcurrency').trigger('change');
                    //toastr.success("The operation is success.", "Success!");
                }
            });
        }

        function setCmpPause(obj)
        {
            let status = $(obj).attr('status');
            let cmpid = $(obj).attr('cmp-id');
            let value = 1;  //1:play, 0:pause
            if(status == "play")
                value = 1;
            else if(status == "pause")
                value = 0;
            updateCampaign("cmpstatus", cmpid, value, function(res)
            {
                if(value == 0)
                {
                    $(obj).attr('class', 'btn btn-success waves-effect waves-light btn-sm');
                    $(obj).attr('status', 'play');
                    $(obj).html('<i class="mdi mdi-play"></i>');
                }
                else if(value == 1)
                {
                    $(obj).attr('class', 'btn btn-danger waves-effect waves-light btn-sm');
                    $(obj).attr('status', 'pause');
                    $(obj).html('<i class="mdi mdi-pause"></i>');
                }
            });
        }

        function updateCampaign(type, site_id, value, callback)
        {
            blockUI();
            let cmp_id = $('#sheet_title').attr('data-id');
            $.ajax({
                url: "{{ route('sheet.updatecampagin') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {
                    type:type,
                    value:value,
                    site_id:site_id,
                    cmp_id:cmp_id,
                },
                success : function(res) {
                    if (typeof callback == "function")
                    {
                        callback(res);
                        if(type != "auto" && type != "reset")
                        {
                            $.unblockUI();
                            toastr.success("The operation is success.", "Success!");
                        }
                    }
                },
                error : function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                }
            });
        }

        function hidePopover()
        {
            $('[data-toggle="popover"]').popover('dispose');
        }
    </script>
@endpush
