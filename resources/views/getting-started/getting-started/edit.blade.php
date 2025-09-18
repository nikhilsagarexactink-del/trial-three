@extends('layouts.app')
@section('head')
<title>Getting Started  | Update</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php
    $id = request()->route('id');
    $userType = userType();
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.gettingStarted.index', ['user_type'=>$userType])}}">Getting Started</a></li>
                    <li class="breadcrumb-item active">Update Getting Started</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Getting Started
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false" action="{{route('common.update',['id'=>$id])}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" id="uploadImageUrl" value="{{route('common.saveImage')}}">
                    <input type="hidden" id="mediaFor" value="getting-started">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Title<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Title" name="title" value="{{$result->title}}">
                                <span id="title-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Video URL</label>
                                <input type="text" class="form-control" placeholder="Video URL" name="video_url" value="{{$result->video_url}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provider Type</label>
                                <div class="select-arrow">
                                    <select class="form-control" name="provider_type">
                                        <option value="">Select Provider Type</option>
                                        <option value="youtube" {{$result->provider_type=='youtube' ? 'selected' : ''}}>Youtube</option>
                                        <option value="vimeo" {{$result->provider_type=='vimeo' ? 'selected' : ''}}>Vimeo</option>
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
                                        <option value="{{$category->id}}" {{$result->category_id==$category->id ? 'selected' : ''}}>{{$category->name}}</option>
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
                                <textarea class="form-control text-editor" placeholder="Description" name="description" rows="6" cols="30">{{$result->description}}</textarea>
                                <span id="description-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="image-upload">
                                <div class="form-group mb-3">
                                    <label class="d-block">Upload Images</label>
                                    <input class="file-upload-input" type="file" class="" onchange="setImage(this)" name="uploadImages" id="UploadImg">
                                    <a href="javascript:void(0)" class="btn btn-secondary"><img class="" src="{{ url('assets/images/file-upload.svg') }}">File upload</a>
                                </div>
                                <div class="uploaded-image-list">
                                    @if(!empty($result->media_id) && !empty($result->media->base_url))
                                        <img style="height:50px;width:50px;" id="imagePreview" src="{{$result->media->base_url}}">
                                    @else
                                        <img style="height:50px;width:50px;" id="imagePreview" src="{{ url('assets/images/default-image.png') }}">
                                    @endif
                                </div>
                                <input type="hidden" id="hiddenMediaFileId" name="media_id" value="{{!empty($result->media_id) ? $result->media_id : ''}}">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn" onClick="updateVideo()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
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
<!-- {!! JsValidator::formRequest('App\Http\Requests\GettingStartedRequest','#updateForm') !!} -->

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
     * Update Record.
     * @request form fields
     * @response object.
     */
    function updateVideo() {
        var formData = $("#updateForm").serializeArray();
        if ($('#updateForm').valid()) {
            $('#updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{route('common.update', ['id'=>'%recordId%'])}}";
            url = url.replace('%recordId%', "{{$result['id']}}");
            $.ajax({
                type: "PUT",
                url: url,
                data: formData,
                success: function(response) {
                    $('#updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.href = "{{route('user.manageGettingStarted.index', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Vdeo not updated.');
                    }

                },
            });
        }
    };

</script>
@endsection