@extends('admin.layout')
@section('content')
    @include('admin.partials.top-bar')
    <div class="page-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <span class="ml-3">{{ __('globals.common.search_type') }} :</span>
                    {!! Form::select('sel_datetype', $search_type, $cur_date_type, array('id'=>'sel_datetype', 'class'=> 'minimal ml-1 m-b-10 list-inline')) !!}
                    <div class="m-b-10 list-inline float-right" id="dateranger">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <h4 class="ml-3 mt-0 header-title list-inline"><label data-id="-1">{{ __('globals.payment_history.transactions') }}</label>
                                <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                            </h4>
                            <table id="datatable_data" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ __('globals.payment_history.date') }}</th>
                                    <th>{{ __('globals.payment_history.deposits') }}</th>
                                    <th>{{ __('globals.payment_history.spent') }}</th>
                                    <th>{{ __('globals.payment_history.description') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @php
                                        $total_deposit = 0;
                                        $total_spent = 0
                                        @endphp
                                        <td>{{ $final_res['pre_date'] }}</td>
                                        <td>R$ {{ number_format($final_res['pre_total_deposit'], 2, '.', ',') }}</td>
                                        <td>R$ {{ number_format($final_res['pre_total_spent'], 2, '.', ',') }}</td>
                                        <td>{{ __('globals.payment_history.pre_balance') }} (R$ {{ number_format($final_res['pre_balance'], 2, '.', ',') }})</td>
                                    </tr>
                                    @foreach($final_res['final_res'] as $row)
                                        @php
                                            $total_deposit += $row['deposit'];
                                            $total_spent += $row['spent'];
                                        @endphp
                                    <tr>
                                        <td>{{ $row['date'] }}</td>
                                        <td>@if($row['deposit'] != 0) R$ {{ number_format($row['deposit'], 2, '.', ',') }}  @endif</td>
                                        <td> R$ {{ number_format($row['spent'], 2, '.', ',') }}</td>
                                        <td>{{ $row['description'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @php
                                    $total_deposit += $final_res['pre_total_deposit'];
                                    $total_spent += $final_res['pre_total_spent'];
                                    $total_balance = $total_deposit - $total_spent;
                                @endphp
                                <tfoot class="db-table-footer">
                                    <tr>
                                        <td>{{ __('globals.common.total') }}</td>
                                        <td>R$ {{ number_format($total_deposit, 2, '.', ',') }}</td>
                                        <td>R$ {{ number_format($total_spent, 2, '.', ',') }}</td>
                                        <td>{{ __('globals.payment_history.last_balance') }} (R$ {{ number_format($total_balance, 2, '.', ',') }})</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div><!-- container -->
    </div>

@endsection
@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Alertify css -->
    <link href="{{ asset('assets/admin/plugins/alertify/css/alertify.css') }}" rel="stylesheet" type="text/css">
    <!-- Switchery css -->
    <link href="{{ asset('assets/admin/plugins/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Toastr css -->
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Dropify css -->
    <link href="{{ asset('assets/admin/plugins/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Datarangepicker css -->
    <link href="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>

    <style>
        #datatable_data
        {

        }

        #sel_datetype {
            border:none;
            background-color: #fafafa;
            color: #292b2c;
            width: 100px;
        }

        .yearselect, .monthselect
        {
            border:none;
            background-color: #fafafa;
            color: #292b2c;
        }

        .select2-container {
            border-radius: 2px;
            position: absolute;
            top: 133px;
            left: 436px; /*436273px*/
            height: 60px;
            z-index: 1;
        }
        .select2-dropdown
        {
            top: -24px;
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

        table td
        {
            cursor: pointer !important;
        }

        .db-table-footer
        {

        }

        .th-media
        {
            max-width: 135px;
            min-width: 135px;
        }
        .th-spent
        {
            max-width: 70px;
            min-width: 70px;
        }
        .td-campaign
        {
            max-width: 250px;
            min-width: 250px;
        }
        .td-title
        {
            max-width: 250px;
            min-width: 250px;
        }

        .alert
        {
            margin-left: 15px;
        }
        table.dataTable tbody td {
            vertical-align: middle !important;
        }

        td.td-total {
            text-align: right;
            font-weight: 600;
        }

        .popover{
            min-width: 20%; /* Max Width of the popover (depending on the container!) */
        }
        .thumb-img{
            border-radius: 5%;
        }
        .td-img:hover
        {
            background-color: #2196f34d;
        }

        #dateranger
        {
            border-bottom: 1px solid;
            border-bottom-color: #aeaeae;
            cursor: pointer;
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
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Datatable extention -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <!-- Datatable Responsive -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ asset('assets/admin/pages/datatables.init.js') }}"></script>
    <!-- Alertify Library js -->
    <script src="{{ asset('assets/admin/plugins/alertify/js/alertify.js') }}"></script>
    <!-- Swtichery Library js -->
    <script src="{{ asset('assets/admin/plugins/switchery/switchery.min.js') }}"></script>
    <!-- Toastr Library js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    <!-- Dropify Library js -->
    <script src="{{ asset('assets/admin/plugins/dropify/dropify.min.js') }}"></script>
    <!-- Date Range Picker Js -->
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datarangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.min.js') }}"></script>
    <!-- Select2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>

    <script>
        var cmp_table;
        let start_date, end_date;

        $(document).ready(function(){

            //Toastr init//
            toastr.options.progressBar = true;
            toastr.options.closeButton = true;
            toastr.options.closeDuration = 300;
            toastr.options.timeOut = 1000; // How long the toast will display without user interaction

            $(function() {

                var start = new Date("{{ $fin_start_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
                start = moment(start);
                var end = new Date("{{ $fin_end_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
                end = moment(end);
                $('#dateranger span').html(start.format('MMMM D, YYYY') + '~' + end.format('MMMM D, YYYY'));

                function cb(cstart, cend) {
                    $('#dateranger span').html(cstart.format('MMMM D, YYYY') + '~' + cend.format('MMMM D, YYYY'));

                    // Grab the datatables input box and alter how it is bound to events
                    start_date = cstart.format('YYYY-MM-DD');
                    end_date = cend.format('YYYY-MM-DD');
                    start = cstart;
                    end = cend;
                    $.post("{{ route('payments.ajax_setsessiondate') }}", { start_date: start_date, end_date: end_date },
                        function (resp, textStatus, jqXHR) {
                            $.unblockUI();
                            setTimeout(function(){ location.reload(); }, 100);
                        }
                    );
                }
                $('#dateranger').daterangepicker({
                    startDate: start,
                    endDate: end,
                    showDropdowns: true,
                    linkedCalendars: false,
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
            });

            $('#sel_datetype').change(function(evt){
                let curType = $(this).val();
                $.post("{{ route('payments.ajax_setsessiondatetype') }}", { cur_type: curType },
                    function (resp, textStatus, jqXHR) {
                        $.unblockUI();
                        setTimeout(function(){ location.reload(); }, 100);
                    });
            });

            let cmp_table = $('#datatable_data').DataTable({
                "stateSave": true,
                "scrollY": '60vh',
                "scrollCollapse": true,
                "dom": 'Bfrtip',
                "bProcessing": true,
                "order":[ 0, 'asc' ],
                "language":{
                    "export":"example"
                },
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
                    @if(sizeof(session('permissions')) > 0 && session('permissions')['column_visibility'] == 1)
                        'colvis',
                    @endif
                ],
                "initComplete": function(settings, json) {
                    $('#datatable_data').show();
                }
            });

            cmp_table.search('').draw();

        });

    </script>
@endpush