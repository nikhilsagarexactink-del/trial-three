@extends('layouts.app')
@section('head')
<title>Motivation Section | Add</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php $userType = userType(); @endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.motivationSection.index', ['user_type'=>$userType])}}">Motivation Section</a></li>
                    <li class="breadcrumb-item active">Create Motivation Section</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create Motivation Section
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.save')}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" id="uploadImageUrl" value="{{route('common.saveImage')}}">
                    <input type="hidden" id="mediaFor" value="motivation-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Title<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Title" name="title">
                                <span id="title-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Video URL</label>
                                <input type="text" class="form-control" placeholder="Video URL" name="video_url">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provider Type</label>
                                <div class="select-arrow">
                                    <select class="form-control" name="provider_type">
                                        <option value="">Select Provider Type</option>
                                        <option value="youtube">Youtube</option>
                                        <option value="vimeo">Vimeo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Category<span class="text-danger">*</span></label>
                                <div class="select-arrow">
                                    <select class="form-control" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        @if($category->status =='active')
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>  
                                <span id="category_id-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                                          
                      
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description<span class="text-danger">*</span></label>
                                <textarea class="form-control text-editor" placeholder="Description" name="description" rows="6" cols="30"></textarea>
                                <span id="description-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="image-upload">
                                <div class="form-group mb-3">
                                    <label class="d-block">Upload Image</label>
                                    <input type="file" class="file-upload-input" onchange="setImage(this)" name="uploadImages" id="UploadImg">
                                    <a href="javascript:void(0)" class="btn btn-secondary"><img class="" src="{{ url('assets/images/file-upload.svg') }}">File upload</a>
                                </div>
                                <div class="uploaded-image-list">
                                    <img style="height:50px;width:50px;" id="imagePreview" src="{{ url('assets/images/default-image.png') }}">
                                </div>
                                <input type="hidden" id="hiddenMediaFileId" name="media_id">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn" onClick="addVideo()">Add<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.gettingStarted.index', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
<!-- Image crop modal -->
@include('layouts.image-cropper-modal')
<!-- Image crop modal -->

@endsection

@section('js')
<script src="{{url('assets/custom/image-cropper.js')}}"></script>
<!-- {!! JsValidator::formRequest('App\Http\Requests\MotivationSectionReqeust','#addForm') !!} -->

<script>
    tinymce.init({
        theme: "modern",
        //selector: "textarea",
        mode: "specific_textareas",
        editor_selector: "text-editor",
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        },
        relative_urls: false,
        remove_script_host: true,
        convert_urls: false,
        plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern',
        toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
        height: 200,
    });
    /**
     * Add Video
     * @request form fields
     * @response object.
     */
    function addVideo() {
        var formData = $("#addForm").serializeArray();
        if ($('#addForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.motivation.save')}}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{route('user.motivationSection.index', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    $('.error-help-block').text('');
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Recipe not created.');
                    }
                },
            });
        }
    };

</script>
@endsection