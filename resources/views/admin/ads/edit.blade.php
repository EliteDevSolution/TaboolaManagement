@extends('admin.layout')
@section('content')
@include('admin.partials.top-bar')
    <div class="page-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    {{ __('globals.ads.status') }}:
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
                            <h4 class="ml-3 mt-0 header-title list-inline"><label data-id="-1">{{ __('globals.common.edit_adversting') }}</label>
                                <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                            </h4>
                            <h4 class="mt-0 header-title"><a href="{{ route('ads.create') }}">
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
                            <div class="row ml-1 mb-1">
                                <div class="col-md-10">
                                    <textarea class="form-control" id="mass_url" rows="3" placeholder="@lang('globals.ads.mass_url_placeholder')"></textarea>
                                </div>
                                <div class="col-md-2">
                                    <button class="form-control btn btn-secondary waves-effect waves-light row mb-sm-1" id="btn_mass_clear"><i class="ion-trash-a"></i> @lang('globals.ads.clear')</button>
                                    <button class="form-control btn btn-secondary waves-effect waves-light row" id="btn_mass_add"><i class="ion-plus"></i> @lang('globals.ads.add')</button>
                                </div>
                            </div>

                            <table id="datatable_cmp_data" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('globals.ads.item_id')</th>
                                        <th>@lang('globals.ads.thumbnail')</th>
                                        <th>@lang('globals.ads.title')</th>
                                        <th>@lang('globals.ads.url')</th>
                                        <th>@lang('globals.ads.status')</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result as $row)
                                    <tr row-id="{{ $row['id'] }}">
                                        <td>
                                            <input type="checkbox" data-id="{{ $row['id'] }}" data-plugin="switchery" @if($row['is_active']) checked @endif />
                                        </td>
                                        <td>
                                            {{ $row['id'] }}
                                        </td>
                                        <td width="150" class="td-img" id="td_img_{{ $row['id'] }}" data-title="{{ $row['title'] }}" data-id="{{ $row['id'] }}" data-img-url="{{ $row['thumbnail_url'] }}">
                                            <img class="thumb-img" src="{{ $row['thumbnail_url'] }}" width="150" id="img_{{ $row['id'] }}" />
                                        </td>
                                        <td class="td-title" id="td_title_{{ $row['id'] }}" data-toggle="popover">
                                            {{ $row['title'] }}
                                        </td>
                                        <td class="td-url" id="td_url_{{ $row['id'] }}" data-toggle="popover">
                                            <a href="{{ $row['url'] }}" target="_blank">{{ $row['url'] }}</a>
                                        </td>
                                        <td>
                                            @if($row['status'] == 'RUNNING')
                                                <span class="badge badge-success">{{ $row['status'] }}</span>
                                            @elseif($row['status'] == 'PENDING_APPROVAL')
                                                <span class="badge badge-warning">{{ $row['status'] }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $row['status'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button remove-id='{{ $row['id'] }}' onclick="removeAds(this, {{ $row['id'] }})" class='btn btn-secondary waves-effect waves-light btn-sm btn_ad_edit' href="{{ route('ads.destroy', $row['id']) }}" title="{{ __('globals.ads.remove_capaign_ads') }}"><i class='ion-trash-a text-danger'></i></button>
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
                    <input type="file" class="dropify" id="dropify-img" data-max-file-size="2.5M" data-height="400" accept="image/*"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">{{ __('globals.campaigns.close') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="btn_save">{{ __('globals.ads.save') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection
@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Alertify css -->
    <link href="{{ asset('assets/admin/plugins/alertify/css/alertify.css') }}" rel="stylesheet" type="text/css">
    <!-- Switchery css -->
    <link href="{{ asset('assets/admin/plugins/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Toastr css -->
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Dropify css -->
    <link href="{{ asset('assets/admin/plugins/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />

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

        .td-url:hover
        {
            background-color: #2196f34d;
            text-decoration-line: underline;
        }
        .td-img:hover
        {
            background-color: #2196f34d;
        }
        .td-title:hover
        {
            background-color: #2196f34d;
            text-decoration-line: underline;
        }

        .alert
        {
            margin-left: 15px;
        }
        table.dataTable tbody td {
            vertical-align: middle !important;
        }

        .popover{
            min-width: 20%; /* Max Width of the popover (depending on the container!) */
        }
        .thumb-img{
            border-radius: 5%;
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

    <script>
        var cmp_table;
        $(document).ready(function(){
            //Toastr init//
            toastr.options.progressBar = true;
            toastr.options.closeButton = true;

            $('.td-img').click(function(){

                hidePopover();

                $('#item_title').html($(this).attr('data-title'));
                $('#item_title').attr('data-id', $(this).attr('data-id'));

                var imagenUrl = $(this).attr('data-img-url');
                var drEvent = $('#dropify-img').dropify(
                    {
                        defaultFile: imagenUrl,
                    });
                drEvent = drEvent.data('dropify');
                drEvent.resetPreview();
                drEvent.clearElement();
                drEvent.settings.defaultFile = imagenUrl;
                drEvent.destroy();
                drEvent.init();
                $('.dropify-wrapper').height(400);

                $('#img_modal').modal({
                    backdrop:'static',keyboard:false, show:true
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



            $('#btn_mass_clear').click(function(){
                $('#mass_url').val('');
                $('#mass_url').focus();
            });



            $('#btn_save').click(function(){
                let rowId = $('#item_title').attr('data-id');
                let realImgPath = $('#dropify-img').val();
                let file = document.getElementById('dropify-img').files[0];
                if(realImgPath == "")
                {
                    $('#img_modal').modal('toggle');
                } else {
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('id', rowId);
                    formData.append('type', 'thumbnail_url');
                    formData.append('value', '');
                    updateAds('thumbnail_url', rowId, formData);
                }
            });

            $('.td-title').click(function () {

                $('[data-toggle="popover"]').popover('dispose');
                let curText = $(this).text().trim();
                let rowId = $(this).parent().attr('row-id');

                var contentHtml = `
                <div data-toggle='popover_div col-md-12'>
                    <div for="" class="control-label popupcelleditor-label mb-2 header-title">{{ __('globals.ads.title') }} </div>
                    <input class="form-control mb-2" type="text" require id="title_${rowId}" value="${curText}">
                </div>
                <div class="form-actions float-right mb-1">
                    <button name="save" class="btn btn-secondary" onclick="updateTitle(${rowId})">
                    {{ __('globals.ads.ok') }} <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                    <button data-novalidate="" class="btn btn-secondary" type="submit" onclick="hidePopover()">
                    {{ __('globals.ads.cancel') }}</button>
                </div>`;

                $(this).popover({
                    animation: false,
                    html: true,
                    sanitize: false,
                    placement: 'bottom',
                    trigger: 'manual',
                    content: contentHtml,
                });

                $(this).popover('show');

            });

            $('.td-url').click(function () {

                $('[data-toggle="popover"]').popover('dispose');
                let curText = $(this).text().trim();
                let rowId = $(this).parent().attr('row-id');

                var contentHtml = `
                <div data-toggle='popover_div col-md-12'>
                    <div for="" class="control-label popupcelleditor-label mb-2 header-title">{{ __('globals.ads.url') }} </div>
                    <input class="form-control mb-2" type="text" require id="url_${rowId}" value="${curText}">
                </div>
                <div class="form-actions float-right mb-1">
                    <button name="save" class="btn btn-secondary" onclick="updateUrl(${rowId})">
                    {{ __('globals.ads.ok') }} <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                    <button data-novalidate="" class="btn btn-secondary" type="submit" onclick="hidePopover()">
                    {{ __('globals.ads.cancel') }}</button>
                </div>`;

                $(this).popover({
                    animation: false,
                    html: true,
                    sanitize: false,
                    placement: 'bottom',
                    trigger: 'manual',
                    content: contentHtml,
                });

                $(this).popover('show');

            });

            $('#btn_mass_add').click(function(){
                if($('#mass_url').val() == '')
                {
                    $('#mass_url').focus();
                    return;
                }
                alertify
                    .okBtn("{{ __('globals.ads.ok') }}")
                    .confirm("{{ __('globals.msg.are_you_sure') }}",
                        function(){
                            blockUI();
                            $.post("{{ route('ads.mass_add', $cmpid) }}", {data: $('#mass_url').val() },
                                function (resp,textStatus, jqXHR) {
                                    $.unblockUI();
                                    toastr.success("{{ __('globals.msg.save_success') }}", "{{ __('globals.msg.well_done') }}");
                                    setTimeout(function(){ location.reload(); }, 1500);
                            }).fail(function(res) {
                                $.unblockUI();
                                toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                            });
                        },
                        function(){
                            $('#mass_url').focus();
                        }
                    );
            });

            cmp_table = $('#datatable_cmp_data').DataTable({
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

            let curStatus = localStorage.getItem('cur_ads_status');
            if(curStatus == '' || curStatus == null)
            {
                curStatus = 'ALL';
            }
            $('#sel_status').val(curStatus);


            $('#sel_status').change(function(evt)
            {
                let curStatus = $(this).val();
                localStorage.setItem('cur_ads_status', curStatus);
                if ( cmp_table.column().search() !== curStatus ) {
                    if(curStatus === 'ALL')
                    {
                        cmp_table
                            .column(5)
                            .search( '' )
                            .draw();
                    } else
                    {
                        cmp_table
                            .column(5)
                            .search( curStatus )
                            .draw();
                    }
                }
            })

        });

        let removeAds = (obj, id) => {
            var $this = $(obj).parents('tr');
            alertify
            .okBtn("{{ __('globals.ads.ok') }}")
            .confirm("{{ __('globals.msg.are_you_sure') }}",
                function(){
                    blockUI();
                    $.post("{{ route('ads.remove', $cmpid) }}", {id: id },
                        function (resp,textStatus, jqXHR) {
                            $.unblockUI();
                            toastr.success("{{ __('globals.msg.remove_success') }}", "{{ __('globals.msg.well_done') }}");
                            cmp_table.row($this).remove().draw();
                        }
                    );
                },
                function(){
                    $('#mass_url').focus();
                }
            );
        }

        let hidePopover = () => {
            $('[data-toggle="popover"]').popover('dispose');
        }


        let updateTitle = (id) => {
            if($(`#title_${id}`).val().trim() == '')
            {
                $(`#title_${id}`).focus();
                return false;
            }
            updateAds('title', id, $(`#title_${id}`).val().trim());
        }

        let updateUrl = (id) => {
            if($(`#url_${id}`).val().trim() == '' || !isUrlValid($(`#url_${id}`).val().trim()))
            {
                $(`#url_${id}`).focus();
                return false;
            }
            updateAds('url', id, $(`#url_${id}`).val().trim());
        }

        let updateAds = (type, id, value) =>  {
            hidePopover();
            blockUI();
            if(type === 'thumbnail_url')
            {
                $.ajax({
                    url: "{{ route('ads.ajax_update', $cmpid) }}",
                    data: value,
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 800000,
                    type: 'POST',
                    dataType: 'json', // added data type
                    success: function(res) {
                        $.unblockUI();
                        let url = res.url;
                        $('#img_' + id).attr('src', url);
                        $('#td_img_' + id).attr('data-img-url', url);
                        $('#img_modal').modal('toggle');
                        toastr.success("{{ __('globals.msg.operation_success') }}", "{{ __('globals.msg.well_done') }}");
                    },
                    error: function (jqXHR, exception) {
                        $.unblockUI();
                        toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                        $('#img_modal').modal('toggle');
                    }
                });

            } else
            {
                $.post("{{ route('ads.ajax_update', $cmpid) }}", {type: type, id: id, value: value},
                    function (resp,textStatus, jqXHR) {
                        $.unblockUI();
                        if(type == 'title')
                        {
                            $(`#td_${type}_${id}`).text(value);
                            $('#td_img_' + id).attr('data-title', value);
                        }
                        if(type == 'url') $(`#td_${type}_${id}`).html(`<a href="${value}" target="_blank">${value}</a>`);
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