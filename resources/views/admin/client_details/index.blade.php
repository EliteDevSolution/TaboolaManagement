@extends('admin.layout')
@section('content')
@include('admin.partials.top-bar')

<div class="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        <h4 class="ml-3 mt-0 mb-5 header-title list-inline">
                            <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
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
                        <table id="datatable_user_data" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>{{ __('globals.wizard.name') }}</th>
                                <th>{{ __('globals.wizard.email') }}</th>
                                <th>{{ __('globals.wizard.business_name') }}</th>
                                <th>{{ __('globals.wizard.address') }}</th>
                                <th>{{ __('globals.wizard.phone') }}</th>
                                <th>{{ __('globals.wizard.bank_name') }}</th>
                                <th>{{ __('globals.wizard.bank_proxy') }}</th>
                                <th>{{ __('globals.wizard.bank_confirm') }}</th>
                                <th>{{ __('globals.wizard.cpf_cnpj') }}</th>
                                <th>{{ __('globals.wizard.ip_address') }}</th>
                                <th>{{ __('globals.wizard.doc_version') }}</th>
                                <th>{{ __('globals.wizard.date_time') }}</th>
                                <th>{{ __('globals.wizard.status') }}</th>
                                <th>{{ __('globals.wizard.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->business_name }}</td>
                                <td>{{ $row->address }}</td>
                                <td>{{ $row->phone_number }}</td>
                                <td>{{ $row->bank_name }}</td>
                                <td>{{ $row->bank_proxy_name }}</td>
                                <td>{{ $row->bank_account_confirm }}</td>
                                <td>{{ $row->bank_cpf_cnpj }}</td>
                                <td>{{ $row->ip_address }}</td>
                                <td>{{ $row->doc_version }}</td>
                                <td>{{ $row->accept_date_time }}</td>
                                <td>
                                    @if($row->accept_status == 1)
                                        <span class="badge badge-success">{{ __('globals.wizard.accept') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('globals.wizard.reject') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('client_details.edit',$row->id) }}" title="Edit">
                                        <button class="btn-primary"><i class="mdi mdi-lead-pencil"></i></button>
                                    </a>
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
    <link href="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #datatable_user_data
        {

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
    <!-- Datatable init js -->
    <script src="{{ asset('assets/admin/pages/datatables.init.js') }}"></script>

    <script>
    $(document).ready(function(){
        $('#datatable_user_data').DataTable(
        {
            "stateSave": true,
            "scrollY": '60vh',
            "scrollX": true,
            "scrollCollapse": true,
            "dom": 'Bfrtip',
            "bProcessing": true,
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
            "autoWidth": false,
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
            "initComplete": function(settings, json) {
                //$('#datatable_user_data').show();
            }
        });
    });
    </script>
@endpush