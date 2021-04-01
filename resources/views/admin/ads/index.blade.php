@extends('admin.layout')
@section('content')
    @include('admin.partials.top-bar')
    <div class="page-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="m-b-10 list-inline float-right" id="dateranger" style="border-bottom: 1px solid;border-bottom-color: #aeaeae;cursor: pointer;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <h4 class="ml-3 mt-0 header-title list-inline"><label data-id="-1">{{ __('globals.common.top_adverstings') }}</label>
                                <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                            </h4>
                            <div class="row">
                                {!! Form::select('selcampaigns', $cmplist, old('selcampaigns', $cmpid), array('id'=>'selcampaigns', 'class'=> 'custom-select minimal m-b-10 col-3')) !!}
                            </div>
                            <h4 class="mt-0 header-title">
                                <a href="{{ route('ads.create') }}">
                                    <button class="btn btn-success waves-effect waves-light" style="margin-left: 15px;">
                                        <i class="ion-plus"></i> @lang('globals.ads.new_ads')
                                    </button>
                                </a>
                            </h4>
                            @if(session()->has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <strong>{{ __('globals.msg.well_done') }}</strong> {{ session('success') }}
                                </div>
                            @endif
                            @if(session()->has('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <strong>{{ __('globals.msg.oh_snap') }}</strong> {{ session('error') }}
                                </div>
                            @endif
                            <table id="datatable_cmp_data" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('globals.ads.id')</th>
                                    <th class="th-media">@lang('globals.ads.top_media')</th>
                                    <th class="th-title">@lang('globals.ads.top_item')</th>
                                    <th>@lang('globals.campaigns.campaing')</th>
                                    <th>@lang('globals.ads.viewable_impressions')</th>
                                    <th>@lang('globals.ads.vctr')</th>
                                    <th>@lang('globals.ads.clicks')</th>
                                    <th>@lang('globals.ads.actual_cpc')</th>
                                    <th>@lang('globals.ads.vcpm')</th>
                                    <th>@lang('globals.ads.conversion_rate')</th>
                                    <th>@lang('globals.ads.conversions')</th>
                                    <th>@lang('globals.ads.cpa')</th>
                                    <th class="th-spent">@lang('globals.ads.spent')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total_spent = 0;
                                    $total_viewable = 0;
                                    $total_vctr = 0;
                                    $total_clicks = 0;
                                    $total_act_cpc = 0;
                                    $total_conversion_rate = 0;
                                    $total_conversions = 0;
                                    $total_cpa = 0;
                                    $total_vcpm = 0;
                                    $total_cnt = 0;
                                @endphp

                                @foreach($result as $row)
                                    @php
                                        $total_cnt ++;
                                        $total_spent += $row['spent'];
                                        $total_viewable += $row['visible_impressions'];
                                        $total_clicks += $row['clicks'];
                                        $total_vctr += $row['vctr'];
                                        $total_act_cpc += $row['cpc'];
                                        $total_conversion_rate += $row['cvr'];
                                        $total_conversions += $row['conversions_value'];
                                        $total_cpa += $row['cpa'];
                                        $total_vcpm += $row['vcpm'];
                                    @endphp

                                    <tr row-id="{{ $row['item'] }}">
                                        <td>
                                            @if(array_key_exists($row['item'], $ads_status) && $ads_status[$row['item']] === 0)
                                                <input type="checkbox" data-id="{{ $row['item'] }}" data-plugin="switchery"/>
                                            @else
                                                <input type="checkbox" data-id="{{ $row['item'] }}" data-plugin="switchery" checked/>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $row['item'] }}
                                        </td>
                                        <td class="td-img" id="td_img_{{ $row['item'] }}" data-title="{{ $row['item_name'] }}" data-id="{{ $row['item'] }}" data-img-url="{{ $row['thumbnail_url'] }}">
                                            <img class="thumb-img" data-src="{{ $row['thumbnail_url'] }}" width="150" id="img_{{ $row['item'] }}" />
                                        </td>
                                        <td width="300" class="td-title" id="td_title_{{ $row['item'] }}" data-toggle="popover">
                                            <a href="{{ $row['url'] }}" target="_blank">{{ $row['item_name'] }}</a>
                                            <div>
                                                @if($row['learning_display_status'] === 'LEARNING_COMPLETE')
                                                    <span class="text-success">{{ $row['learning_display_status'] }}</span>
                                                @else
                                                    <span class="text-warning">{{ $row['learning_display_status'] }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="td-campaign">
                                            {{ $row['campaign_name'] }}
                                        </td>
                                        <td>
                                            {{ $row['visible_impressions'] }}
                                        </td>
                                        <td>
                                            {{ $row['vctr'] }}%
                                        </td>
                                        <td>
                                            {{ $row['clicks'] }}
                                        </td>
                                        <td>
                                            R${{ number_format($row['cpc'], 3, '.', ',') }}
                                        </td>
                                        <td>
                                            R${{ number_format($row['vcpm'], 3, '.', ',') }}
                                        </td>
                                        <td>
                                            {{ number_format($row['cvr']) }}%
                                        </td>
                                        <td>
                                            {{ $row['conversions_value'] }}
                                        </td>
                                        <td>
                                            R${{ number_format($row['cpa'], 3, '.', ',') }}
                                        </td>
                                        <td class="td-spent">
                                            R${{ number_format($row['spent'], 2, '.', ',') }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                @php if($total_cnt == 0) $total_cnt = 1; @endphp
                                <tfoot class="db-table-footer">
                                    <tr>
                                        <td class="td-total">-</td>
                                        <td class="td-total">-</td>
                                        <td class="td-total">-</td>
                                        <td class="td-total">-</td>
                                        <td class="td-total">{{ __('globals.common.total') }}</td>
                                        <td>{{ number_format($total_viewable) }}</td>
                                        <td>{{ number_format($total_vctr/$total_cnt, 2, '.', ',') }}%</td>
                                        <td>{{ number_format($total_clicks) }}</td>
                                        <td>R${{ number_format($total_act_cpc/$total_cnt, 3, '.', ',') }}</td>
                                        <td>R${{ number_format($total_vcpm/$total_cnt, 2, '.', ',') }}</td>
                                        <td>{{ number_format($total_conversion_rate/$total_cnt, 2, '.', ',') }}%</td>
                                        <td>{{ number_format($total_conversions) }}</td>
                                        <td>R${{ number_format($total_cpa/$total_cnt, 3, '.', ',') }}</td>
                                        <td>R${{ number_format($total_spent, 2, '.', ',') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div><!-- container -->
    </div>

    <!-- sample modal content -->
    <div id="img_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0">{{ __('globals.ads.thumbnail') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <h4 id="item_title"></h4>
                    <input type="file" class="dropify" id="dropify-img" data-max-file-size="2.5M" disabled="disabled" data-height="400" accept="image/*"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">{{ __('globals.campaigns.close') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
        #datatable_cmp_data
        {
            display: none;
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
    <!-- Image LazyLoading Library Js -->
    <script src="{{ asset('assets/admin/plugins/lazyload/lazyload.js') }}"></script>

    <script>
        var cmp_table;
        let start_date, end_date;

        $(document).ready(function(){

            //Toastr init//
            toastr.options.progressBar = true;
            toastr.options.closeButton = true;

            $(function() {

                var start = new Date("{{ $rep_start_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
                start = moment(start);
                var end = new Date("{{ $rep_end_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
                end = moment(end);
                $('#dateranger span').html(start.format('MMMM D, YYYY') + '~' + end.format('MMMM D, YYYY'));

                function cb(cstart, cend) {
                    $('#dateranger span').html(cstart.format('MMMM D, YYYY') + '~' + cend.format('MMMM D, YYYY'));

                    // Grab the datatables input box and alter how it is bound to events
                    start_date = cstart.format('YYYY-MM-DD');
                    end_date = cend.format('YYYY-MM-DD');
                    start = cstart;
                    end = cend;
                    $.post("{{ route('ads.ajax_setsessiondate') }}", { start_date: start_date, end_date: end_date },
                        function (resp, textStatus, jqXHR) {
                            $.unblockUI();
                            setTimeout(function(){ location.reload(); }, 100);
                        });
                }
                $('#dateranger').daterangepicker({
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
            });

            $('.td-img').click(function(){

                $('#item_title').html($(this).attr('data-title'));
                $('#item_title').attr('data-id', $(this).attr('data-id'));

                var imagenUrl = $(this).attr('data-img-url');
                var drEvent = $('#dropify-img').dropify({ defaultFile: imagenUrl });
                drEvent = drEvent.data('dropify');
                drEvent.resetPreview();
                drEvent.clearElement();
                drEvent.settings.defaultFile = imagenUrl;
                drEvent.destroy();
                drEvent.init();
                $('.dropify-wrapper').height(400);
                $('#img_modal').modal();
            });

            $('#selcampaigns').select2();

            $('#selcampaigns').change(function(evt){
                let cmpid = $(this).val();
                $.post("{{ route('ads.ajax_setsessioncmpid') }}", { cmpid: cmpid },
                    function (resp, textStatus, jqXHR) {
                        $.unblockUI();
                        setTimeout(function(){ location.reload(); }, 100);
                    });
            });

            let elems = $('[data-plugin="switchery"]');
            for (var i = 0; i < elems.length; i++) {
                let init = new Switchery(elems[i], {size:'small'});
            }

            $('[data-plugin="switchery"]').on('change', function(evt)
            {
                let status = this.checked;
                let rowId = $(this).attr('data-id');
                updateAds('is_active', rowId, status);
            });

            let cmp_table = $('#datatable_cmp_data').DataTable({
                "stateSave": true,
                "scrollY": '60vh',
                "scrollCollapse": true,
                "scrollX": true,
                "dom": 'Bfrtip',
                "bProcessing": true,
                "order":[ 13, 'desc' ],
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
                "drawCallback": function() {
                    $("img.thumb-img").lazyload();
                },
                "initComplete": function(settings, json) {
                    $('#datatable_cmp_data').show();
                }
            });

            cmp_table.search('').draw();

        });

        let updateAds = (type, id, value) =>  {
            blockUI();
            {
                $.post("{{ route('ads.ajax_update', $cmpid) }}", {type: type, id: id, value: value},
                    function (resp,textStatus, jqXHR) {
                        $.unblockUI();
                        toastr.success("{{ __('globals.msg.operation_success') }}", "{{ __('globals.msg.well_done') }}");
                    }
                ).fail(function(res) {
                    $.unblockUI();
                    toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                });
            }
        }
    </script>
@endpush