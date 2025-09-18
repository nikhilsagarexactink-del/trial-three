@extends('layouts.app')
@section('head')
<title>Training Video | Update</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php
    $id = request()->route('id');
    $userType = userType();
    $selectedSkills = [];
    $selectedAgeRanges = [];
    $selectedCategories = [];
    $selectedUserTypes = [];
    foreach($result->skillLevels as $skillLevel){
        array_push($selectedSkills,$skillLevel->skill_level_id);
    }
    foreach($result->ageRanges as $ageRange){
        array_push($selectedAgeRanges,$ageRange->age_range_id);
    }
    foreach($result->categories as $category){
        array_push($selectedCategories,$category->category_id);
    }
    
    if(!empty($result->user_types)){
        $selectedUserTypes = explode(", ",$result->user_types);
    }
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.manageTrainingVideo', ['user_type'=>$userType])}}">Trainng Video</a></li>
                    <li class="breadcrumb-item active">Update</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Training Video
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false" action="{{route('common.updateTrainingVideo',['id'=>$id])}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" id="uploadImageUrl" value="{{route('common.saveImage')}}">
                    <input type="hidden" id="mediaFor" value="training-videos">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Title<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Title" name="title" value="{{$result->title}}">
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
                                    <select id="Categories" name="categories[]" class="js-states form-control" multiple>
                                        @foreach($categories as $category)
                                            @if($category->status =='active')
                                            <option value="{{$category->id}}" {{in_array($category->id, $selectedCategories) ? 'selected' : ''}}>{{$category->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <!-- <select class="form-control" name="training_video_category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        @if($category->status =='active')
                                        <option value="{{$category->id}}" {{$result->training_video_category_id==$category->id ? 'selected' : ''}}>{{$category->name}}</option>
                                        @endif
                                        @endforeach
                                    </select> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group multi-select">
                                <label>Skill Level<span class="text-danger">*</span></label>
                                <select id="SkillLevel" name="skill_levels[]" class="js-states form-control" multiple>
                                    @foreach($skillLevels as $skillLevel)
                                    @if($skillLevel->status =='active')
                                    <option value="{{$skillLevel->id}}" {{in_array($skillLevel->id, $selectedSkills) ? 'selected' : ''}}>{{$skillLevel->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <span id="skill_levels-error" class="help-block error-help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group multi-select">
                                <label>Age Range<span class="text-danger">*</span></label>
                                <select id="AgeRange" name="age_ranges[]" class="js-states form-control" multiple>
                                    @foreach($ageRanges as $ageRange)
                                    @if($ageRange->status =='active')
                                    <option value="{{$ageRange->id}}" {{in_array($ageRange->id, $selectedAgeRanges) ? 'selected' : ''}}>{{$ageRange->min_age_range}} - {{$ageRange->max_age_range}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <span id="age_ranges-error" class="help-block error-help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date<span class="text-danger">*</span></label>
                                <input type="text" readOnly id="datepicker" class="form-control" placeholder="Date" name="date" value="{{ !empty($result->date) ? date('m-d-Y', strtotime($result->date)) : '' }}">
                            </div>
                        </div>                       
                        <div class="col-md-3">
                            <div class="form-group custom-form-check-head mt-4">
                                <div class=" custom-form-check">
                                    <label class="form-check">
                                        <input type="checkbox" value="1" name="is_featured" {{$result->is_featured==1 ? 'checked' : ''}}> <span>Featured</span>
                                        <div class="checkbox__checkmark"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group multi-select">
                                <label for="">User Type(s)<span class="text-danger"></span></label>
                                <select class="js-example-basic-multiple form-control" id="userTypes" name="user_types[]" multiple="multiple">
                                    <option value="parent" {{in_array('Parent', $selectedUserTypes) ? 'selected' : ''}}>Parent</option>
                                    <option value="athlete" {{in_array('athlete', $selectedUserTypes) ? 'selected' : ''}}>Athlete</option>
                                </select>
                                <span id="user_types-error" class="help-block error-help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description<span class="text-danger">*</span></label>
                                <textarea class="form-control text-editor" placeholder="Description" name="description" rows="6" cols="30">{{$result->description}}</textarea>
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
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.manageTrainingVideo', ['user_type'=>$userType])}}">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\TrainingVideoRequest','#updateForm') !!}

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
            var url = "{{route('common.updateTrainingVideo', ['id'=>'%recordId%'])}}";
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
                            window.location.href = "{{route('user.manageTrainingVideo', ['user_type'=>$userType])}}";
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

    $(function() {
        $("#userTypes").select2({
            placeholder: "Select user types"
        });
        $("#datepicker").datepicker({
          dateFormat: 'mm-dd-yy'
        });
    });
</script>
@endsection