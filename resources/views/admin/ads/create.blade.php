@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')

<div class="page-content-wrapper ">

    <div class="container">

        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        <div class="form">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"> {{ __('globals.ads.cmp_id') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    {!! Form::select('campaign_id', $cmplist, old('campaign_id', $cmpid), array('id'=>'campaign_id', 'class'=> 'custom-select col-md-3')) !!}
                                    @if ($errors->has('campaign_id'))
                                        <div class="form-control-feedback" >{{ $errors->first('campaign_id') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('url'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label"> {{ __('globals.ads.url') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <button class="btn btn-primary waves-effect waves-light mb-1" type="button" id="btn_add_url"><i class="ion-plus"></i> {{ __('globals.ads.add') }}</button>
                                    <div class="url-content">
                                        <div class="alert alert-dismissible fade show" role="alert">
                                            <input name="url[]" class="form-control" type="text" placeholder="{{ __('globals.ads.input_url') }}" onkeypress="addUrl(event, this)">
                                            <button type="button" class="multi-input close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    </div>
                                    @if ($errors->has('url'))
                                        <div class="form-control-feedback" >{{ $errors->first('url') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('title'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.ads.title') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <button class="btn btn-primary waves-effect waves-light mb-1" type="button" id="btn_add_title"><i class="ion-plus"></i> {{ __('globals.ads.add') }}</button>
                                    <div class="title-content">
                                        <div class="alert alert-dismissible fade show" role="alert">
                                            <input name="title[]" class="form-control" type="text" placeholder="{{ __('globals.ads.input_title') }}" onkeypress="addTitle(event, this)">
                                            <button type="button" class="multi-input close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    </div>
                                    @if ($errors->has('title'))
                                        <div class="form-control-feedback" >{{ $errors->first('title') }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group row
                            @if($errors->has('file'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">{{ __('globals.ads.thumbnail') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <div class="m-b-30">
                                        <form action="{{ route('ads.ajax_multi_img_upload') }}" class="dropzone" id="my-awesome-dropzone">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                    @if ($errors->has('file'))
                                        <div class="form-control-feedback text-danger" >{{ $errors->first('file') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <div class="button-items">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit" id="btn_save">{{ __('globals.ads.save') }}</button>
                                        <button class="btn btn-secondary waves-effect" type="button" onclick="history.back(1);">{{ __('globals.ads.cancel') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->
</div>

@endsection

@push('css')
    <!-- Select2 css -->
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Alertify css -->
    <link href="{{ asset('assets/admin/plugins/alertify/css/alertify.css') }}" rel="stylesheet" type="text/css">
    <!-- Dropify css -->
    <link href="{{ asset('assets/admin/plugins/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Dropzone css -->
    <link href="{{ asset('assets/admin/plugins/dropzone/dist/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <!-- Toastr css -->
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .multi-input {
            position: absolute !important;
        }
        .alert
        {
            padding: unset !important;
            margin-bottom: -1px !important;
        }
        .close
        {
            top: -0.4rem !important;
            font-size: 1.6rem !important;
            right: -0.5rem !important;
        }
        .icon-remove
        {
            left: -35px;
            top: 4px;
            font-weight: 800;
            font-size: 20px;
        }
        .custom-control
        {
            margin-top: 8px;
        }
        .select2-container {
            border-radius: 2px;
            height: 40px;
        }
        .select2-selection {
            height: 38px !important;
            padding-left: 2px;
        }
        .select2-selection__rendered,
        .select2-selection__arrow {
            margin-top: 4px;
        }
    </style>
@endpush

@push('scripts')
    <!-- Select2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>
    <!-- Alertify Library js -->
    <script src="{{ asset('assets/admin/plugins/alertify/js/alertify.js') }}"></script>
    <!-- Dropify Library js -->
    <script src="{{ asset('assets/admin/plugins/dropify/dropify.min.js') }}"></script>
    <!-- Dropzone Library js -->
    <script src="{{ asset('assets/admin/plugins/dropzone/dist/dropzone.js') }}"></script>
    <!-- Toastr Library js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>

<script>
    $(document).ready(function(){
        //Toastr init//
        var uploadFileList = [];
        toastr.options.progressBar = true;
        toastr.options.closeButton = true;
        toastr.options.closeDuration = 300;
        toastr.options.timeOut = 1000; // How long the toast will display without user interaction
        var myDropZone;

        // "myAwesomeDropzone" is the camelized version of the HTML element's ID
        Dropzone.options.myAwesomeDropzone = {
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 2.5, // MB
            maxFiles: 10,
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            init: function () {
                myDropZone = this;
            },
            maxfilesexceeded: function(file) {
                this.removeFile(file);
            },
            success: function(file, res)
            {
                let filename = file.name;
                let url = res.url;
                uploadFileList.push({filename: filename, url: url});
            },
            accept: function(file, done) {
                var i;
                var filelist = myDropZone.getAcceptedFiles();
                if(filelist.length > 0) {
                    for(i = 0; i < filelist.length; i++) {
                        if(filelist[i].name === file.name
                            && filelist[i].size === file.size
                            && filelist[i].lastModifiedDate.toString() === file.lastModifiedDate.toString())
                        {
                            this.removeFile(file);
                        }
                    }
                }
                if (file.type.indexOf('image') === 0) {
                    done();
                }
            }
        };

        $('#btn_save').click(function()
        {
            let cmp_id = $('#campaign_id').val();
            let url_list = $('.url-content').find('input');
            let title_list = $('.title-content').find('input');
            let fileLst = myDropZone.getAcceptedFiles();
            let attachUrl = [];

            if(url_list.length === 0 || url_list.first().val() === '' || !/^(ftp|http|https):\/\/[^ "]+$/.test(url_list.first().val()))
            {
                url_list.first().focus();
                toastr.warning('{{ __('globals.msg.input_correct_url') }}', '{{ __('globals.msg.oh_snap') }}');
                return false;
            } else if(title_list.length === 0 || title_list.first().val() === '')
            {
                title_list.first().focus();
                toastr.warning('{{ __('globals.msg.input_require') }}', '{{ __('globals.msg.oh_snap') }}');
                return false;
            } else if(fileLst.length === 0)
            {
                toastr.warning('{{ __('globals.msg.files_require') }}', '{{ __('globals.msg.oh_snap') }}');
                return false;
            }

            for(index = 0; index < fileLst.length; index++)
            {
                for(jIndex = 0; jIndex < uploadFileList.length; jIndex++)
                {
                    if(fileLst[index].name == uploadFileList[jIndex].filename)
                    {
                        attachUrl.push(uploadFileList[jIndex].url);
                        break;
                    }
                }
            }

            let urlLst = [], titleLst = [];
            url_list.each(function(index) {
                if($(this).val() != '')
                    urlLst.push($(this).val())
            });

            title_list.each(function(index) {
                if($(this).val() != '')
                    titleLst.push($(this).val())
            });

            alertify
                .okBtn("{{ __('globals.ads.ok') }}")
                .confirm("{{ __('globals.msg.are_you_sure') }}",
                    function(){
                        blockUI();
                        $.post("{{ route('ads.ajax_save_ads') }}", {image_list: attachUrl, url_list: urlLst, title_list: titleLst, cmp_id: cmp_id },
                            function (resp,textStatus, jqXHR) {
                                $.unblockUI();
                                toastr.success("{{ __('globals.msg.save_success') }}", "{{ __('globals.msg.well_done') }}");
                                setTimeout(function(){ location.href = "{{ url('admin/ads') }}" +  `/${cmp_id}/edit`; }, 1500);
                            });
                    },
                    function(){

                    }
                );
        });


        //$('#img_dropzone').dropzone({ url: "{{ route('ads.ajax_multi_img_upload') }}" });
        $('#campaign_id').select2();

        $('#btn_add_url').click(function(evt){
            let url_list = $('.url-content').find('input');
            let lastChild = url_list.last();
            if($(lastChild).val() === '' || url_list.length > 9 || (!/^(ftp|http|https):\/\/[^ "]+$/.test($(lastChild).val()) && url_list.length > 0))
            {
                toastr.warning('{{ __('globals.msg.input_correct_url') }}', '{{ __('globals.msg.oh_snap') }}');
                $(lastChild).focus();
                return;
            }

            for(index = 0; index < url_list.length - 1; index++)
            {
                if(lastChild.val() == $(url_list[index]).val())
                {
                    toastr.warning('{{ __('globals.msg.already_exist') }}', '{{ __('globals.msg.oh_snap') }}');
                    $(lastChild).focus();
                    return;
                }
            }

            $('.url-content').append(`<div class="alert alert-dismissible fade show" role="alert">
                                            <input name="url[]" class="form-control" type="text" placeholder="{{ __('globals.ads.input_url') }}" onkeypress="addUrl(event, this)">
                                            <button type="button" class="multi-input close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>`);
        });

        $('#btn_add_title').click(function(evt){
            let title_list = $('.title-content').find('input');
            let lastChild = title_list.last();
            if($(lastChild).val() === '' || title_list.length > 9)
            {
                toastr.warning('{{ __('globals.msg.input_require') }}', '{{ __('globals.msg.oh_snap') }}');
                $(lastChild).focus();
                return;
            }

            for(index = 0; index < title_list.length - 1; index++)
            {
                if(lastChild.val() == $(title_list[index]).val())
                {
                    toastr.warning('{{ __('globals.msg.already_exist') }}', '{{ __('globals.msg.oh_snap') }}');
                    $(lastChild).focus();
                    return;
                }
            }

            $('.title-content').append(`<div class="alert alert-dismissible fade show" role="alert">
                                            <input name="title[]" class="form-control" type="text" placeholder="{{ __('globals.ads.input_title') }}" onkeypress="addTitle(event, this)">
                                            <button type="button" class="multi-input close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>`);

        });
    });

    let addUrl = (event, obj) => {
        if (event.key === 'Enter' || event.keyCode === 13) {
            $('#btn_add_url').trigger('click');
            setTimeout(function(){
            }, 100)
        }
    }

    let addTitle = (event, obj) => {
        if (event.key === 'Enter' || event.keyCode === 13) {
            $('#btn_add_title').trigger('click');
            setTimeout(function () {
                $('.title-content').find('input').last().focus();
            }, 100)
        }
    }


</script>
@endpush
