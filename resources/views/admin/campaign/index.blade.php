@extends('admin.layout')
@section('content')
@include('admin.partials.top-bar')

<div class="page-content-wrapper ">
    <div class="container">
        <div class="row">
            <div class="col-12">
                {{ __('globals.campaigns.status') }}:
                <select id="sel_status" class="minimal m-b-10 list-inline">
                    <option value="ALL" selected>ALL_STATUS</option>
                    <option value="RUNNING">RUNNING</option>
                    <option value="PAUSED">PAUSED</option>
                    <option value="REJECTED">REJECTED</option>
                    <option value="PENDING_START_DATE">PENDING_START_DATE</option>
                    <option value="DEPLETED_MONTHLY">DEPLETED_MONTHLY</option>
                    <option value="DEPLETED">DEPLETED</option>
                    <option value="EXPIRED">EXPIRED</option>
                    <option value="TERMINATED">TERMINATED</option>
                    <option value="FROZEN">FROZEN</option>
                    <option value="PENDING_APPROVAL">PENDING_APPROVAL</option>
                </select>
            </div>
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        <h4 class="ml-3 mt-0 header-title list-inline"><label data-id="-1">{{ __('globals.common.campagins_adversting') }}</label>
                            <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                        </h4>
                        @if((session()->get('cur_balance') >= 100 && Auth::guard('admin')->user()->id !== 1) || Auth::guard('admin')->user()->id === 1)
                        <h4 class="mt-0 header-title"><a href="{{ route('campaigns.create') }}">
                                <button class="btn btn-success waves-effect waves-light" style="margin-left: 15px;">
                                    <i class="ion-plus"></i> @lang('globals.campaigns.new_campaign')
                                </button>
                            </a>
                        </h4>
                        @endif
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
                                @if((session()->get('cur_balance') >= 100 && Auth::guard('admin')->user()->id !== 1) || Auth::guard('admin')->user()->id === 1)
                                <th>#</th>
                                @endif
                                <th>@lang('globals.campaigns.id')</th>
                                <th width="22%">@lang('globals.campaigns.campaing')</th>
                                <th>@lang('globals.campaigns.status')</th>
                                <th width="100">@lang('globals.campaigns.spending_model')</th>
                                <th width="100">@lang('globals.campaigns.spending_limit')</th>
                                <th width="100">@lang('globals.campaigns.daily_ad_delivery')</th>
                                <th>@lang('globals.campaigns.bid_strategy')</th>
                                <th width="130">@lang('globals.campaigns.start_date')</th>
                                <th>@lang('globals.campaigns.end_date')</th>
                                <th>@lang('globals.campaigns.spent')</th>
                                <th width="170">@lang('globals.campaigns.actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($allCampaigns as $row)
                                <tr>
                                    @if((session()->get('cur_balance') >= 100 && Auth::guard('admin')->user()->id !== 1) || Auth::guard('admin')->user()->id === 1)
                                    <td><input type="checkbox" data-id="{{ $row['id'] }}" data-plugin="switchery" @if($row['is_active']) checked @endif /></td>
                                    @endif
                                    <td title="{{ $row['name'] }}">{{ $row['id'] }}</td>
                                    <td title="{{ $row['name'] }}">{{ $row['name'] }}</td>
                                    <td>
                                        @if($row['status'] === 'RUNNING')
                                            <span class="badge badge-success">{{ $row['status'] }}</span>
                                        @elseif($row['status'] == 'PENDING_APPROVAL')
                                            <span class="badge badge-warning">{{ $row['status'] }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $row['status'] }}</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-primary">{{ $row['spending_limit_model'] }}</span></td>
                                    <td>R${{ number_format($row['spending_limit']) }}</td>
                                    <td>R${{ $row['daily_cap'] }}</td>
                                    <td>R${{ $row['cpc'] }} ({{ $row['bid_strategy'] }})</td>
                                    <td>{{ $row['start_date'] ?? '-' }}</td>
                                    <td>
                                        @if(explode('-', $row['end_date'])[0] > 2100)
                                            -
                                        @else
                                            {{ $row['end_date'] }}
                                        @endif
                                    </td>
                                    <td>R${{ number_format($row['spent'], 2, '.', ',') }}</td>
                                    <td>
                                        <a edit-id='{{ $row['id'] }}' class='btn btn-secondary waves-effect waves-light btn-sm btn_cmp_edit' href="{{ route('campaigns.edit', $row['id']) }}" title="{{ __('globals.campaigns.edit_campaign_properties') }}"><i class='ion-edit text-primary'></i></a>
                                        @if((session()->get('cur_balance') >= 100 && Auth::guard('admin')->user()->id !== 1) || Auth::guard('admin')->user()->id === 1)
                                        <a edit-id='{{ $row['id'] }}' class='btn btn-secondary waves-effect waves-light btn-sm btn_ad_edit' href="{{ route('ads.edit', $row['id']) }}" title="{{ __('globals.campaigns.edit_campaign_ads') }}"><i class='mdi mdi-format-list-bulleted text-primary'></i></a>
                                        <a edit-id='{{ $row['id'] }}' class='btn btn-secondary waves-effect waves-light btn-sm btn_replicate' href="{{ route('campaigns.page_duplicate', $row['id']) }}" title="{{ __('globals.campaigns.duplicate_campaign') }}"><i class='mdi mdi-content-duplicate text-primary'></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
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
    <!-- Switchery css -->
    <link href="{{ asset('assets/admin/plugins/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Toastr css -->
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        #datatable_cmp_data{
            display: none;
        }
        #sel_status {
            border:none;
            background-color: #fafafa;
            color: #292b2c;
        }
        table td
        {
            cursor: pointer !important;
        }
        .alert
        {
            margin-left: 15px;
        }
        @media only screen and (max-width: 1045px) {
            .dt-buttons.btn-group
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
    <!-- Swtichery Library js -->
    <script src="{{ asset('assets/admin/plugins/switchery/switchery.min.js') }}"></script>
    <!-- Toastr Library js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>

    <script>
    $(document).ready(function(){
        @if((session()->get('cur_balance') >= 100 && Auth::guard('admin')->user()->id !== 1) || Auth::guard('admin')->user()->id === 1)
        let elems = $('[data-plugin="switchery"]');
        for (var i = 0; i < elems.length; i++) {
            let init = new Switchery(elems[i], {size:'small'});
        }

        $('[data-plugin="switchery"]').on('change', function(evt)
        {
            let status = this.checked;
            let cmpId = $(this).attr('data-id');
            //updateAds('is_active', rowId, status);
            blockUI();
            $.post("{{ route('campaigns.ajax_update') }}", {cmpid: cmpId, type: 'is_active',  value: status},
                function (resp,textStatus, jqXHR) {
                    $.unblockUI();
                    toastr.success("{{ __('globals.msg.operation_success') }}", "{{ __('globals.msg.well_done') }}");
                }
            ).fail(function(res) {
                $.unblockUI();
                toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
            });
        });
        @endif

        var cmp_table = $('#datatable_cmp_data').DataTable({
            "stateSave": true,
            "autoWidth": true,
            "scrollY": '60vh',
            "scrollCollapse": true,
            "dom": 'Bfrtip',
            "bProcessing": true,
            "order":[ 1, 'desc' ],
            "language":{
                "export":"example"
            },
            "responsive": true,
            "fixedHeader": true,
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
                {
                    "extend": 'colvis'
                }
                @endif
            ],
            "initComplete": function(settings, json) {
                $('#datatable_cmp_data').show();
            }
        });

        let curStatus = localStorage.getItem('cur_status');
        if(curStatus == '' || curStatus == null)
        {
            curStatus = 'ALL';
        }
        $('#sel_status').val(curStatus);


        $('#sel_status').change(function(evt)
        {
            let curStatus = $(this).val();
            localStorage.setItem('cur_status', curStatus);
            if ( cmp_table.column().search() !== curStatus ) {
                if(curStatus === 'ALL')
                {
                    cmp_table
                        .column(3)
                        .search( '' )
                        .draw();
                } else
                {
                    cmp_table
                        .column(3)
                        .search( curStatus )
                        .draw();
                }
            }
        })
    });
    </script>
@endpush