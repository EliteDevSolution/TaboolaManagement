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
                    <button id="currency_setting" data-toggle="popover" onclick="showCurrencySetting(this)" class="btn btn-secondary waves-effect waves-light btn-sm list-inline"><i class="mdi mdi-settings"></i></button>

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
                                    <th id="th_daily" width="8%">Daily Delivery({{ $curcurrency }})</th>
                                    <th id="th_spent">Spent({{ $curcurrency }})</th>
                                    <th id="th_received">Has received({{ $curcurrency }})</th>
                                    <th id="th_received_max">Received Max({{ $curcurrency }})</th>
                                    <th>ROI Min(%)</th>
                                    <th>ROI Max(%)</th>
                                    <th id="th_profit_min">Profit Min({{ $curcurrency }})</th>
                                    <th id="th_profit_max">Profit Max({{ $curcurrency }})</th>
                                    <th>Clicks</th>
                                    <th id="th_bid_actual">BID Actual({{ $curcurrency }})</th>
                                    <th id="th_bid_strategy">BID Strategy({{ $curcurrency }})</th>
                                    <th id="th_bid_max">BID Max({{ $curcurrency }})</th>
                                    <th id="th_margin">Margin(%)</th>
                                    <th id="th_start_date" width="8%">Start Date</th>
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

@endsection

@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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

    <!-- Toastr Alert Js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>

    <!-- Select2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>

    <!-- Sweetalert2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

    <!-- Myscript -->
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>

    <script>
        let start_date, end_date, currency;
        toastr.options.progressBar = true;
        toastr.options.closeButton = true;

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
                $('#th_spent').text(`Spent(${$('#selcurrency').val()})`);
                $('#th_received').text(`Has received(${$('#selcurrency').val()})`);
                $('#th_received_max').text(`Received Max(${$('#selcurrency').val()})`);
                $('#th_profit_min').text(`Profit Min(${$('#selcurrency').val()})`);
                $('#th_profit_max').text(`Profit Max(${$('#selcurrency').val()})`);
                $('#th_bid_actual').text(`BID Actual(${$('#selcurrency').val()})`);
                if($('#sheet_title').attr('data-id') == "-1")
                {
                    $('#th_bid_max').text(`BID Max(${$('#selcurrency').val()})`);
                } else
                {
                    $('#th_bid_max').text(`BID Amount(${$('#selcurrency').val()})`);
                    $('#th_margin').text(`BID MAX(${$('#selcurrency').val()})`);
                    
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
                        stateSave: true,
                        "autoWidth": false,
                        "scrollY": '60vh',
                        "scrollCollapse": true,
                        "dom": 'Bfrtip',
                        "order":[ 2, 'desc' ],
                        "language":{
                            "export":"example"
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
                                text: 'All Pause',
                                action: function ( e, dt, node, config ) {
                                    swal({
                                        title: 'Are you sure?',
                                        text: "All campaign status becomes paue. You won't be able to revert this.",
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
                                "text": 'Export',
                                "buttons": [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                                "fade": true
                            },
                            {
                                "extend": 'colvis'
                            }
                        ],
                    });
                    
                    dtable.column( 3 ).visible( false, false ); //Has Rec
                    dtable.column( 5 ).visible( false, false ); //Roi Min
                    dtable.column( 7 ).visible( false, false ); //Profit Min
                    //dtable.column( 9 ).visible( false, false ); //Clicks
                    
                    
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
                        $('#datatable_body').html(res.data);
                        $('#datatable_foot').html(res.foot);
                        $('#th_bid_max').text(`BID Amount(${$('#selcurrency').val()})`);
                        $('#th_margin').text(`BID MAX(${$('#selcurrency').val()})`);
                        $('#th_start_date').remove();
                        //$('#th_status').remove();
                        $('#th_daily').remove();
                        $('#th_bid_strategy').remove();

                        
                        $('#selcampaigns').val(id);
                        

                        dtable = $('#datatable_sheet_data').DataTable({
                            stateSave: true,
                            "autoWidth": false,
                            "scrollY": '60vh',
                            "scrollCollapse": true,
                            "dom": 'Bfrtip',
                            "order":[ 1, 'desc' ],
                            "lengthMenu": [
                                [ 10, 50, 100, 500, 1000],
                                [ '10', '50', '100', '500', '1000' ]
                            ],
                            "buttons": [
                                'pageLength', 
                                {
                                    text: 'Auto Change',
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
                                                if(res.status == true)
                                                    $('#selcurrency').trigger('change');
                                                else
                                                    $.unblockUI();
                                            });
                                        },function(dismiss) {
                                            var margin = $('#selcampaigns option:selected').attr('margin');
                                            if (dismiss === 'cancel') {
                                                updateCampaign("auto", "method_2", margin, function(res)
                                                {
                                                    if(res.status == true)
                                                        $('#selcurrency').trigger('change');
                                                    else
                                                        $.unblockUI();
                                                });
                                            } 
                                        });
                                    }
                                },
                                {
                                    text: 'Bid Reset',
                                    action: function ( e, dt, node, config ) {
                                        swal({
                                            title: 'Are you sure?',
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
                                    "text": 'Export',
                                    "buttons": [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                                    "fade": true
                                },
                                'colvis',
                            ],
                        });

                        dtable.column( 2 ).visible( false, false ); //Has Rec
                        dtable.column( 4 ).visible( false, false ); //Roi Min
                        dtable.column( 6 ).visible( false, false ); //Profit Min
                        //dtable.column( 8 ).visible( false, false ); //Clicks
                        
                        dtable.columns.adjust().draw( false );

                        $('.select2-container').css('left', '655px');
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

        function  updateCampaign(type, site_id, value, callback)
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
