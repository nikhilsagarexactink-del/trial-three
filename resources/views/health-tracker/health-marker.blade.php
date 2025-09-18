@extends('layouts.app')
@section('head')
<title>Health Tracker | Health Marker</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php 
    $userType = userType(); 
    $userData = getUser();
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb ">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard', ['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.healthTracker', ['user_type'=>$userType])}}">Health Tracker</a></li>
                    <li class="breadcrumb-item active">Health Marker</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Health Marker
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.healthTracker.saveHealthMarker')}}">
            @csrf
            <input type="hidden" id="uploadImageUrl" value="{{route('common.saveImage')}}">
            <input type="hidden" id="mediaFor" value="health-tracker">
            <input type="hidden" id="uploadType" value="multiple">
            <input type="hidden" class="form-control" name="type" value="health-markers" value="{{!empty($detail) ? $detail->type : ''}}">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        @if($setting['weight_status']=='enabled')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Weight{{!empty($setting['weight']) ? (($setting['weight']=='POUNDS_LBS') ? '(POUNDS/LBS)' : '(KILOGRAMS/.KG)') : ''}}</label>
                                <input type="text" class="form-control" placeholder="Weight" name="weight" value="{{!empty($detail) ? $detail->weight : ''}}">
                            </div>
                        </div>
                        @endif
                        @if($setting['body_fat_status']=='enabled')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Body Fat(%)</label>
                                <input type="text" class="form-control" placeholder="Body Fat" name="body_fat" value="{{!empty($detail) ? $detail->body_fat : ''}}">
                            </div>
                        </div>
                        @endif
                        @if($setting['bmi_status']=='enabled')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>BMI(%)</label>
                                <input type="text" class="form-control" placeholder="BMI" name="bmi" value="{{!empty($detail) ? $detail->bmi : ''}}">
                            </div>
                        </div>
                        @endif
                        @if($setting['body_water_status']=='enabled')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Body Water(%)</label>
                                <input type="text" class="form-control" placeholder="Body Water" name="body_water" value="{{!empty($detail) ? $detail->body_water : ''}}">
                            </div>
                        </div>
                        @endif
                        @if($setting['skeletal_muscle_status']=='enabled')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Skeletal Muscle(%)</label>
                                <input type="text" class="form-control" placeholder="Skeletal Muscle" name="skeletal_muscle" value="{{!empty($detail) ? $detail->skeletal_muscle : ''}}">
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                @if($setting['health_marker_images_status']=='enabled')
                <div class="col-md-12">
                    <div class="image-upload">
                        <div class="form-group">
                            <label class="d-block">Upload Images</label>
                            <input type="file" class="upload-image-field" onchange="uploadImage(this)" name="uploadImages" id="imgFieldId">
                            <a href="javascript:void(0)" class="btn btn-secondary" id="uploadBtn">
                                <img class="" src="{{ url('assets/images/file-upload.svg') }}">File upload <span id="uploadBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></a>
                        </div>
                    </div>
                    <ul class="uploaded-image-list img-upload-list" id="imageList">
                        @if(!empty($detail->images))
                        @foreach($detail->images as $image)
                        <li>
                            <div>
                                @if(!empty($image->media) && !empty($image->media->base_url))
                                <img style="height:50px;width:50px;" src="{{$image->media->base_url}}" data-lity data-lity-target="{{$image->media->base_url}}" alt="">
                                <a href="javascript:void(0);" class="remove-icon" id="remove" onclick="crossClick($(this))"><i class="iconmoon-close" aria-hidden="true"></i>X</a>
                                @else
                                <img style="height:50px;width:50px;" src="{{ url('assets/images/default-image.png') }}" alt="">
                                <a href="javascript:void(0);" class="remove-icon" id="remove" onclick="crossClick($(this))"><i class="iconmoon-close" aria-hidden="true"></i>X</a>
                                @endif
                                <input type="hidden" name="images[]" value="{{$image->media_id}}" class="images shopImageList">
                            </div>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                @endif
            </div>
            @if($setting['weight_status']=='enabled' || $setting['body_fat_status']=='enabled' || $setting['bmi_status']=='enabled' || $setting['body_water_status']=='enabled' || $setting['body_water_status']=='enabled' || $setting['health_marker_images_status']=='enabled')
            <div class="btn_row text-center">
                <button type="submit" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn" onClick="saveHealthMarker()">Submit<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.healthTracker', ['user_type'=>$userType])}}">Cancel</a>
            </div>
            @else
            <div class="alert alert-danger" role="alert">
                No fields enabled.<a href="{{route('user.healthTracker.healthSetting', ['user_type'=>$userType])}}"> Click here to update the settings</a>
            </div>
            @endif
        </form>
    </section>
    @if(!empty($rewardDetail) && !empty($rewardDetail->reward_game) && $rewardDetail->is_gamification == 1)
        <x-game-modal :rewardDetail="$rewardDetail" :module="'health-tracker'" />
    @endif

</div>
<!-- Main Content Start -->

@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\HealthMarkerRequest','#addForm') !!}

<script>
    const rewardData = @json($rewardDetail??null);
    /**
     * Add Health Tracker.
     * @request form fields
     * @response object.
     */
    function saveHealthMarker() {
        var formData = $("#addForm").serializeArray();
        if ($('#addForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.healthTracker.saveHealthMarker')}}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            if(rewardData && rewardData.is_gamification == 1 && rewardData.reward_game){
                                const userId = @json($userData->id);
                                const rewardId = rewardData.id;
                                const modalId = '#gameModal_' + userId + '_' + rewardId;
                                $(modalId).modal('show'); // updated here
                            }else{
                                window.location.reload();
                            }
                        }, 500);

                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                        _toast.error(errors.message);
                    } else {
                        _toast.error('Please try again.');
                    }
                },
            });
        }
    };

    function uploadImage() {
        var filename = $("#imgFieldId").val();
        var imageElements = document.querySelectorAll('.uploaded-image-list.img-upload-list img');
        // Get the length of the image elements
        var imageLength = imageElements.length;
        var extension = filename.replace(/^.*\./, '');
        if (imageLength < 10) {
            extension = extension.toLowerCase();
            if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension == 'svg' || extension == 'mpeg') {
                var fileObj = document.getElementById("imgFieldId").files[0];
                $('#uploadBtn').prop('disabled', true);
                $('#uploadBtnLoader').show();
                var formData = new FormData();
                formData.append('file', fileObj);
                formData.append('mediaFor', 'health-tracker');
                formData.append('_token', "{{csrf_token()}}");
                $.ajax({
                    type: "POST",
                    url: "{{route('common.saveMultipartMedia')}}",
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#uploadBtnLoader').hide();
                        $('#uploadBtn').prop('disabled', false);
                        if (response.success) {
                            let data = response.data;
                            let liHtml = ` <li class="list-inline-item">
                                                <div class="uploaded-image-list">
                                                    <img style="height:50px;width:50px;" src="`+data.fileInfo.base_url+`" data-lity data-lity-target="`+data.fileInfo.base_url+`" alt="">
                                                    <a href="javascript:void(0);" class="remove-icon" id="remove" onclick="crossClick($(this))"><i class="iconmoon-close" aria-hidden="true"></i>X</a>
                                                    <input type="hidden" name="images[]" value="`+data.id+`" class="images"> 
                                                </div>
                                            </li>`;
                            $('#imageList').append(liHtml);
                            _toast.success(response.message);
                        } else {
                            $('#imgFieldId').val('');
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#imgFieldId').val('');
                        $('#uploadBtnLoader').hide();
                        $('#uploadBtn').prop('disabled', false);
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    },
                });
            } else {
                $('#imgFieldId').val('');
                _toast.error('Only jpeg,png,jpg,svg file allowed.');
            }
        } else {
            _toast.error('Maximum 10 media file can be uploaded.');
        }
    };
    function crossClick(object) {
        $(this).click(function(e) {
            object.parent().parent().remove();
        });
    }
    $('[data-toggle="tooltip"]').tooltip();
</script>
@endsection