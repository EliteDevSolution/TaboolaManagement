@extends('admin.layout')
@section('content')
    @include('admin.partials.top-bar')

    <div class="page-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <h4 class="ml-3 mt-0 header-title list-inline">
                                <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                            </h4>
                            <div class="row">
                                {!! Form::select('selusers', $users, old('selusers', $sel_user), array('id'=>'selusers', 'class'=> 'custom-select minimal m-b-10 col-3')) !!}
                            </div>
                            <h4 class="mt-0 ml-3 header-title">
                                <button id="btn_add_deposit" class="btn btn-primary waves-effect waves-light" type="button">
                                    <i class="ion-plus"></i> {{ __('globals.finance.add_deposit') }}
                                </button>
                            </h4>
                            <table id="datatable_deposit_data" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ __('globals.finance.id') }}</th>
                                    <th>{{ __('globals.finance.users') }}</th>
                                    <th>{{ __('globals.finance.made_date') }}</th>
                                    <th>{{ __('globals.finance.amount') }} (<span id="amount_total">0.00</span>)</th>
                                    <th>{{ __('globals.finance.cnpj') }}</th>
                                    <th>{{ __('globals.finance.bussiness_name') }}</th>
                                    <th>{{ __('globals.finance.description') }}</th>
                                    <th width="5%">{{ __('globals.finance.action') }}</th>
                                </tr>
                                </thead>
                                <tbody id="datatable_tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div><!-- container -->
    </div>
    <!-- Modal Form -->
    <div id="crud_modal" class="modal fade" role="dialog" aria-labelledby="curdModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="crud_form" name="crud_form">
                    <div class="modal-header">
                        <h5 class="modal-title mt-0" id="modal_title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('globals.finance.users') }}:</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    {!! Form::select('selusers_modal', $user_list, old('selusers_modal'), array('id'=>'selusers_modal', 'class'=> 'custom-select minimal m-b-10 col-5')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row
                            @if($errors->has('made_date'))
                                has-danger
                            @endif">
                            <label class="col-sm-3 col-form-label">{{ __('globals.finance.made_date') }}:</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input name="made_date" class="form-control" type="text" value="{{ old('made_date') }}" id="made_date" required>
                                    <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar"></i></span>
                                </div>
                                <div class="form-control-error text-danger" id="error_made_date"></div>
                            </div>
                        </div>

                        <div class="form-group row
                            @if($errors->has('amount'))
                                has-danger
                            @endif">
                            <label class="col-sm-3 col-form-label">{{ __('globals.finance.amount') }}:</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input name="amount" class="form-control" type="number" value="{{ old('amount') }}" id="amount" required>
                                    <span class="input-group-addon bg-custom b-0">R$</span>
                                </div>
                                <div class="form-control-error text-danger" id="error_amount"></div>
                            </div>
                        </div>

                        <div class="form-group row
                            @if($errors->has('deposit_description'))
                                has-danger
                            @endif">
                            <label class="col-sm-3 col-form-label">{{ __('globals.finance.description') }}:</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="deposit_description" id="deposit_description" rows="3"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('globals.common.close') }}</button>
                        <button type="submit" class="btn btn-primary" >{{ __('globals.common.save_changes')  }}</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection
@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #datatable_deposit_data
        {

        }
        .select2-container[data-select2-id="3"] {
            width: 100% !important;
        }


        .select2-container[data-select2-id="1"] {
            border-radius: 2px;
            position: absolute;
            top: 103px;
            left: 468px; /*436273px*/
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

        .select2-selection__rendered,
        .select2-selection__arrow {
            margin-top: 4px;
        }

        button
        {
            cursor: pointer;
        }

        @media only screen and (max-width: 1045px) {
            .dt-buttons.btn-group
            {
                display: none;
            }
            .select2-container[data-select2-id="1"]
            {
                display: none;
            }
        }
    </style>
@endpush
@push('scripts')
    <!-- Jquery validate Library -->
    <script src="{{ asset('assets/admin/js/jquery.validate.min.js') }}"></script>
    <!-- Datapicker -->
    <script src="{{ asset('assets/admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Datatable -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons Addin Datatable -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <!-- Responsive Datatable -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ asset('assets/admin/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <!-- Toastr Alert Js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    <!-- Select2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>


    <script>
        let modalStatus = 'add';
        let editId = -1;
        let oldDate = '';
        $(document).ready(function(){
            toastr.options.progressBar = true;
            toastr.options.closeButton = true;
            toastr.options.closeDuration = 300;
            toastr.options.timeOut = 1000; // How long the toast will display without user interaction

            $('#btn_add_deposit').click((evt) => {
                modalStatus = 'add';
                editId = -1;
                $('#modal_title').text("{{ __('globals.finance.add_deposit') }}");
                $('#selusers_modal').val(1);
                $('#selusers_modal').trigger('change');
                $('#crud_modal').modal({backdrop:'static', keyboard:false, show:true});
                $('#crud_form').trigger("reset");
                $('#made_date').datepicker({ format: "yyyy-mm-dd" }).datepicker('setDate', new Date());
            });

            $('#selusers').select2();
            $('#selusers_modal').select2();

            $('#selusers').change(function(evt){
                let curUserId = $(this).val();
                $.post("{{ route('deposits.ajax_setsessionuserid') }}", { cur_userid: curUserId },
                    function (resp, textStatus, jqXHR) {
                        getLoadData();
                    });
            });


            $("#crud_form").validate({
                errorPlacement: function(error, element) {
                    //Custom position: first name
                    if (element.attr("name") == "made_date" ) {
                        $("#error_made_date").text('{{ __('globals.msg.field_require') }}');
                    }
                    //Custom position: second name
                    else if (element.attr("name") == "amount" ) {
                        $("#error_amount").text('{{ __('globals.msg.field_require') }}');
                    }
                },
                submitHandler: function (form) {
                    blockUI();
                    if(modalStatus == 'add')
                    {
                        console.log($('#deposit_description').val())
                        $.post("{{ route('deposits.save_data') }}", { user_id: $('#selusers_modal').val(), made_date: $('#made_date').val(), amount: $('#amount').val(), description: $('#deposit_description').val() },
                            function (resp, textStatus, jqXHR) {
                                if(resp.status !== 200)
                                {
                                    toastr.error("{{ __('globals.msg.already_exist') }}", "{{ __('globals.msg.oh_snap') }}");
                                } else {
                                    $('#crud_modal').modal('toggle');
                                    if(resp.real_balance < 100)
                                        $('#span_balance').attr('class', 'text-danger');
                                    else
                                        $('#span_balance').attr('class', 'text-success');
                                    $('#span_balance').text(resp.cur_balance);
                                    getLoadData();
                                }
                                $.unblockUI();
                            });
                    } else if(modalStatus == 'edit')
                    {
                        let changeFlag = true;
                        if(oldDate == $('#made_date').val()) changeFlag = false;

                        $.post("{{ route('deposits.edit_data') }}", { id: editId, user_id: $('#selusers_modal').val(), change_flag: changeFlag, made_date: $('#made_date').val(), amount: $('#amount').val(), description: $('#deposit_description').val() },
                            function (resp, textStatus, jqXHR) {
                                if(resp.status !== 200)
                                {
                                    toastr.error("{{ __('globals.msg.already_exist') }}", "{{ __('globals.msg.oh_snap') }}");
                                } else {
                                    if(resp.real_balance < 100)
                                        $('#span_balance').attr('class', 'text-danger');
                                    else
                                        $('#span_balance').attr('class', 'text-success');
                                    $('#span_balance').text(resp.cur_balance);
                                    $('#crud_modal').modal('toggle');
                                    getLoadData();
                                }
                                $.unblockUI();
                            });
                    }
                }
            });

            getLoadData();

            var SweetAlert = function () {
            };
            //init
            $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert;

        });

        let editRow = (obj, id) => {
            modalStatus = 'edit';
            editId = id;
            var $this = $(obj).parents('tr');
            $('#modal_title').text("{{ __('globals.finance.edit_deposit') }}");
            $('#amount').val($this.children('td').eq(3).text());
            $('#deposit_description').val($this.children('td').eq(4).text());
            $('#selusers_modal').val($(obj).attr('data-userid'));
            $('#selusers_modal').trigger('change');

            $('#made_date').datepicker({ format: "yyyy-mm-dd"}).datepicker('setDate', $this.children('td').eq(2).text());
            $('#crud_modal').modal({backdrop:'static', keyboard:false, show:true});
            oldDate = $('#made_date').val();
        }

        let deleteRow = (obj, id) => {
            var $this = $(obj).parents('tr');
            var _method = 'delete';
            swal({
                title: "{{ __('globals.msg.are_you_sure') }}",
                text: "{{ __('glboals.msg.you_dont_revert') }}",
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                confirmButtonText: 'Yes, delete it!'
            }).then(function () {
                $.ajax({
                    url: "{{ url('admin/deposits/remove') }}" + '/'  + id,
                    type: 'POST',
                    success: function (data) {
                        if (data.status === 200) {
                            $this.remove();
                            $('#datatable_deposit_data').DataTable().row($this).remove().draw(false);
                            if(data.real_balance < 100)
                                $('#span_balance').attr('class', 'text-danger');
                            else
                                $('#span_balance').attr('class', 'text-success');
                            $('#span_balance').text(data.cur_balance);
                            toastr.success('{{ __('globals.msg.remove_success') }}', '{{ __('globals.msg.well_done') }}');
                        } else {
                            alert('Whoops Something went wrong!!');
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
            })
        }

        let getLoadData = () => {
            blockUI();
            $('.select2-container[data-select2-id="1"]').hide();
            $('#datatable_deposit_data').DataTable().destroy();
            $.post("{{ route('deposits.get_all') }}",
                function (resp, textStatus, jqXHR) {
                    $.unblockUI();
                    let insertBody = "";
                    let total_amount = 0;
                    resp.results.forEach(ele => {
                        let cpf_cnpj = '';
                        let business_name = '';

                        let obj = ele.admin.client_details;
                        if(obj && obj !== 'null' && obj !== 'undefined')
                        {
                            cpf_cnpj = ele.admin.client_details.cnpj;
                            business_name = ele.admin.client_details.business_name;
                        }
                        insertBody += `<tr style="cursor: pointer;">`;
                        insertBody += `<td>${ele.id}</td>`;
                        insertBody += `<td title="${ele.admin.email}">${ele.admin.name}</td>`;
                        insertBody += `<td>${ele.made_date}</td>`;
                        insertBody += `<td>${ele.amount}</td>`;
                        insertBody += `<td>${cpf_cnpj}</td>`;
                        insertBody += `<td>${business_name}</td>`;
                        insertBody += `<td>${ele.description}</td>`;
                        insertBody += `<td>
                                            <button class="btn-primary" title="Edit" data-userid="${ele.user_id}" onclick="editRow(this, ${ele.id})"><i class="mdi mdi-lead-pencil"></i></button>
                                            <button class="btn-danger btn-delete-record" title="Delete" data-id="${ele.id}" onclick="deleteRow(this, ${ele.id})"><i class="mdi mdi-delete"></i></button>
                                        </td>`;
                       insertBody += '</tr>';
                       total_amount += parseFloat(ele.amount);
                    });
                    $('#amount_total').text(changeRealMoneyUnit(total_amount));

                    $('#datatable_tbody').html(insertBody);

                    $('#datatable_deposit_data').DataTable(
                    {
                        "stateSave": true,
                        "autoWidth": true,
                        "scrollY": '60vh',
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
                            $('#datatable_cmp_data').show();
                        }
                    });

                    $('.select2-container[data-select2-id="1"]').show();
                }
            ).fail(function(res){
                location.reload();
            });
        }
    </script>
@endpush