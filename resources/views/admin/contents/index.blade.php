@extends('admin.layout')
@section('content')
    @include('admin.partials.top-bar')
    <div class="page-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    {{ __('globals.content_page.site_list') }}:
                    <select id="selsitelist" class="minimal m-b-10 col-md-auto list-inline header-select">
                        @foreach ($view_ids as $key => $val)
                            @if($val == $cur_view_id)
                                <option value="{{ $val }}" data-url="{{ 'https://'.$view_id_urls[$key] }}" selected>{{ 'https://'.$view_id_urls[$key] }} ({{ $val }} )</option>
                            @else
                                <option value="{{ $val }}" data-url="{{ 'https://'.$view_id_urls[$key] }}">{{ 'https://'.$view_id_urls[$key] }} ({{ $val }} )</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="m-b-10 list-inline float-right dateranger-select" id="dateranger">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <h4 class="ml-3 mt-0 header-title list-inline"><label data-id="-1">{{ $title }}</label>
                                <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                            </h4>
                            <table id="content_datatable" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('globals.content_page.image') }}</th>
                                    <th width="30%">{{ __('globals.content_page.title') }}</th>
                                    <th>{{ __('globals.content_page.page_views') }}</th>
                                    <th>{{ __('globals.content_page.reg_date') }}</th>
                                    <th>{{ __('globals.content_page.status') }}</th>
                                    <th>{{ __('globals.content_page.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($wp_posts as $row)
                                    <tr>
                                        <td>{{ $row['id'] }}</td>
                                        <td class="td-img" id="td_img_{{ $row['id'] }}" data-title="{{ $row['title'] }}" data-id="{{ $row['id'] }}" data-img-url="{{ $row['featured_img_src'] }}">
                                            <img class="thumb-img" data-src="{{ $row['featured_img_src'] }}" width="150" id="img_{{ $row['id'] }}" />
                                        </td>
                                        <td>
                                            <a href="{{ $row['link'] }}" target="_blank">{{ $row['title'] }}</a>
                                        </td>
                                        <td>
                                            {{ $row['page_views'] }}
                                        </td>
                                        <td>
                                            {{ substr($row['date'], 0, 10) }}
                                        </td>
                                        <td>
                                            @if($row['status'] === 'publish')
                                                <span class="badge badge-success">{{ $row['status'] }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ $row['status'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(sizeof(session('permissions')) > 0 && array_key_exists('utm_generator', session('permissions')->toArray()) && session('permissions')['utm_generator'] == 1)
                                            <button class="btn btn-secondary waves-effect btn-clipboard" data-url="{{ $row['link'] }}"><i class="ti-clipboard"></i></button>
                                            @endif
                                            <button class="btn btn-info waves-effect btn-clipboard-url" title="{{ $row['link'] }}" data-url="{{ $row['link'] }}"><i class="fa fa-link"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="db-table-footer">
                                    <tr>
                                        <td>-</td>
                                        <td>{{ __('globals.common.total') }}</td>
                                        <td>-</td>
                                        <td>{{ number_format($total_views) }}</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('globals.campaigns.close') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- campaign url generator modal  -->
    <div id="cmpurl_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ __('globals.content_page.cmp_generator_modal_title') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div><p><span class="text-danger">*</span> {{ __('globals.content_page.cmp_generator_info') }}</p></div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ __('globals.content_page.utm_source') }}:</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input class="form-control modal-input" type="text" id="utm_source" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ __('globals.content_page.utm_medium') }}:</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input class="form-control modal-input" type="text" id="utm_medium" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ __('globals.content_page.utm_content') }}:</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input class="form-control modal-input" type="text" id="utm_content" required>
                            </div>
                            <p>{{ __('globals.content_page.utm_content_bage') }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ __('globals.content_page.shared_url') }}:</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <textarea class="form-control" id="campaign_url" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('globals.common.close') }}</button>
                    <button type="submit" class="btn btn-primary" id="btn_copy_url"><i class="ti-clipboard"></i> {{ __('globals.content_page.copy_url')  }}</button>
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
        #content_datatable
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

        .header-select
        {
            border:none;
            background-color: #fafafa;
            color: #292b2c;
        }

        .dateranger-select
        {
            border-bottom: 1px solid;
            border-bottom-color: #aeaeae;
            cursor: pointer;"
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
        var content_datatable;
        let start_date, end_date;
        let curUrl;

        $(document).ready(function(){

            //Toastr init//
            toastr.options.progressBar = true;
            toastr.options.closeButton = true;
            toastr.options.closeDuration = 300;
            toastr.options.timeOut = 1000; // How long the toast will display without user interaction

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
                    if(start_date == "{{ session('rep_start_date') }}" && end_date == "{{ session('rep_end_date') }}") return false;
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

            @if(sizeof(session('permissions')) > 0 && array_key_exists('utm_generator', session('permissions')->toArray()) && session('permissions')['utm_generator'] == 1)
            $('.btn-clipboard').click(function(){
                $('#cmpurl_modal').modal({backdrop:'static', keyboard:false, show:true});
                curUrl = $(this).attr('data-url');
                $('#utm_source').val('');
                $('#utm_medium').val('');
                $('#utm_content').val('');
                $('#campaign_url').val('');
            });
            @endif

            $('.btn-clipboard-url').click(function(){
                let strUrl = $(this).attr('data-url');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(strUrl).select();
                document.execCommand("copy");
                $temp.remove();
                toastr.success('{{ __('globals.msg.clipboard_copy') }}', '{{ __('globals.msg.well_done') }}');
            });



            $('.td-img').click(function(){

                $('#item_title').html($(this).attr('data-title'));
                $('#item_title').attr('data-id', $(this).attr('data-id'));


                var imagenUrl = $(this).attr('data-img-url');
                if((imagenUrl).indexOf('?') > -1)
                {
                    imagenUrl = imagenUrl.split('?')[0];
                }

                console.log(imagenUrl);
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

            $('input.modal-input').keyup(() => {
                generateCmpUrl();
            });

            $('#btn_copy_url').click(() => {
                if($('#campaign_url').val() == '')
                {
                    $('#utm_source').focus();
                    return false;
                }

                $("#campaign_url").select();
                document.execCommand('copy');
                toastr.success('{{ __('globals.msg.clipboard_copy') }}', '{{ __('globals.msg.well_done') }}');
            });


            $('#selsitelist').change(function(evt){
                let viewId = $(this).val();
                let siteUrl = $('#selsitelist option:selected').attr('data-url');
                $.post("{{ route('content.ajax_setsessionsitedata') }}", { viwe_id: viewId, site_url: siteUrl },
                    function (resp, textStatus, jqXHR) {
                        setTimeout(function(){ location.reload(); }, 50);
                    });
            });

            let content_datatable = $('#content_datatable').DataTable({
                "stateSave": true,
                "scrollY": '60vh',
                "scrollCollapse": true,
                "dom": 'Bfrtip',
                "bProcessing": true,
                "order":[ 3, 'desc' ],
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
                        "extend": 'collection',
                        "text": "{{ __('globals.datatables.export') }}",
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
                "drawCallback": function() {
                    $("img.thumb-img").lazyload();
                },
                "initComplete": function(settings, json) {
                    $('#content_datatable').show();
                }
            });
            content_datatable.search('').draw();
        });


        let generateCmpUrl = () => {
            let cmpName = "{{ $user_name }}"
            let utm_source = $('#utm_source').val();
            let utm_medium = $('#utm_medium').val();
            let utm_content = $('#utm_content').val();
            let resUrl = curUrl;

            if(utm_source != '')
            {
                resUrl += `?utm_source=${utm_source}`;
                resUrl += `&utm_campaign=${cmpName}`;
            } else
            {
                resUrl = '';
            }

            if(utm_medium != '' && utm_source != '')
            {
                resUrl += `&utm_medium=${utm_medium}`;
            }

            if(utm_content != '' && utm_source != '')
            {
                resUrl += `&utm_content=${utm_content}`;
            }

            $('#campaign_url').val(resUrl);

        };

    </script>
@endpush