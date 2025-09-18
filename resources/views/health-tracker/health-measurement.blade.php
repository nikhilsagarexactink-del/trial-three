@extends('layouts.app')
@section('head')
    <title>Health Tracker | Health Measurment</title>
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
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.healthTracker', ['user_type' => $userType]) }}">Health Tracker</a></li>
                        <li class="breadcrumb-item active">Health Measurements</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Health Measurements
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.healthTracker.saveHealthMeasurement') }}">
                @csrf
                <input type="hidden" id="uploadImageUrl" value="{{ route('common.saveImage') }}">
                <input type="hidden" id="mediaFor" value="health-tracker">
                <input type="hidden" id="uploadType" value="multiple">
                <input type="hidden" class="form-control" name="type" value="health-measurements"
                    value="{{ !empty($detail) ? $detail->type : '' }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            @if ($setting['height_status'] == 'enabled')
                                <div class="row">
                                    <div class="col-md-3 col-lg-12">
                                        <div class="form-group height-range">
                                            <label>Height (<span id="height_slider-value"></span>)</label>
                                            <div id="height_slider"></div>
                                            <input type="hidden" name="height" id="heightField"
                                                value="{{ !empty($detail) ? $detail->height : 0 }}">

                                        </div>
                                    </div>
                                </div>
                            @endif
                            <br /> <br /> <br /> <br />
                            @if ($setting['neck_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Neck</label>
                                        <input type="text" class="form-control" placeholder="Neck" name="neck"
                                            value="{{ !empty($detail) ? $detail->neck : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['shoulder_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Shoulder</label>
                                        <input type="text" class="form-control" placeholder="Shoulder" name="shoulder"
                                            value="{{ !empty($detail) ? $detail->shoulder : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['chest_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Chest</label>
                                        <input type="text" class="form-control" placeholder="Chest" name="chest"
                                            value="{{ !empty($detail) ? $detail->chest : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['waist_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Waist</label>
                                        <input type="text" class="form-control" placeholder="Waist" name="waist"
                                            value="{{ !empty($detail) ? $detail->waist : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['abdomen_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Abdomen</label>
                                        <input type="text" class="form-control" placeholder="Abdomen" name="abdomen"
                                            value="{{ !empty($detail) ? $detail->abdomen : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['hip_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Hip</label>
                                        <input type="text" class="form-control" placeholder="Hip" name="hip"
                                            value="{{ !empty($detail) ? $detail->hip : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['bicep_left_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Bicep Left</label>
                                        <input type="text" class="form-control" placeholder="Bicep Left"
                                            name="bicep_left" value="{{ !empty($detail) ? $detail->bicep_left : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['bicep_right_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Bicep Right</label>
                                        <input type="text" class="form-control" placeholder="Bicep Right"
                                            name="bicep_right" value="{{ !empty($detail) ? $detail->bicep_right : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['thigh_left_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Thigh Left</label>
                                        <input type="text" class="form-control" placeholder="Thigh Left"
                                            name="thigh_left" value="{{ !empty($detail) ? $detail->thigh_left : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['thigh_right_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Thigh Right</label>
                                        <input type="text" class="form-control" placeholder="Thigh Right"
                                            name="thigh_right" value="{{ !empty($detail) ? $detail->thigh_right : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['calf_left_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Calf Left</label>
                                        <input type="text" class="form-control" placeholder="Calf Left"
                                            name="calf_left" value="{{ !empty($detail) ? $detail->calf_left : '' }}">
                                    </div>
                                </div>
                            @endif
                            @if ($setting['calf_right_status'] == 'enabled')
                                <div class="col-md-3 col-lg-4">
                                    <div class="form-group">
                                        <label>Calf Right</label>
                                        <input type="text" class="form-control" placeholder="Calf Right"
                                            name="calf_right" value="{{ !empty($detail) ? $detail->calf_right : '' }}">
                                    </div>
                                </div>
                            @endif

                            @if ($setting['health_measurement_images_status'] == 'enabled')
                                <div class="col-md-12">
                                    <div class="image-upload">
                                        <div class="form-group">
                                            <label class="d-block">Upload Images</label>
                                            <input type="file" class="upload-image-field" onchange="uploadImage(this)"
                                                name="uploadImages" id="imgFieldId">
                                            <a href="javascript:void(0)" class="btn btn-secondary" id="uploadBtn">
                                                <img class="" src="{{ url('assets/images/file-upload.svg') }}">File
                                                upload <span id="uploadBtnLoader" class="spinner-border spinner-border-sm"
                                                    style="display: none;"></span></a>
                                        </div>
                                    </div>
                                    <ul class="uploaded-image-list img-upload-list" id="imageList">
                                        @if (!empty($detail->images))
                                            @foreach ($detail->images as $image)
                                                <li>
                                                    <div>
                                                        @if (!empty($image->media) && !empty($image->media->base_url))
                                                            <img style="height:50px;width:50px;"
                                                                src="{{ $image->media->base_url }}" data-lity
                                                                data-lity-target="{{ $image->media->base_url }}"
                                                                alt="">
                                                            <a href="javascript:void(0);" class="remove-icon"
                                                                id="remove" onclick="crossClick($(this))"><i
                                                                    class="iconmoon-close" aria-hidden="true"></i>X</a>
                                                        @else
                                                            <img style="height:50px;width:50px;"
                                                                src="{{ url('assets/images/default-image.png') }}"
                                                                alt="">
                                                            <a href="javascript:void(0);" class="remove-icon"
                                                                id="remove" onclick="crossClick($(this))"><i
                                                                    class="iconmoon-close" aria-hidden="true"></i>X</a>
                                                        @endif
                                                        <input type="hidden" name="images[]"
                                                            value="{{ $image->media_id }}" class="images shopImageList">
                                                    </div>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                        @if (
                            $setting['height_status'] == 'enabled' ||
                                $setting['neck_status'] == 'enabled' ||
                                $setting['shoulder_status'] == 'enabled' ||
                                $setting['chest_status'] == 'enabled' ||
                                $setting['waist_status'] == 'enabled' ||
                                $setting['abdomen_status'] == 'enabled' ||
                                $setting['hip_status'] == 'enabled' ||
                                $setting['bicep_left_status'] == 'enabled' ||
                                $setting['bicep_right_status'] == 'enabled' ||
                                $setting['thigh_left_status'] == 'enabled' ||
                                $setting['thigh_right_status'] == 'enabled' ||
                                $setting['calf_left_status'] == 'enabled' ||
                                $setting['calf_right_status'] == 'enabled' ||
                                $setting['health_measurement_images_status'] == 'enabled')
                            <div class="btn_row text-center">
                                <button type="submit" class="btn btn-secondary ripple-effect-dark btn-120"
                                    id="addBtn" onClick="saveHealthMeasurement()">Submit<span id="addBtnLoader"
                                        class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                                    href="{{ route('user.healthTracker', ['user_type' => $userType]) }}">Cancel</a>
                            </div>
                        @else
                            <div class="alert alert-danger" role="alert">
                                No fields enabled.<a
                                    href="{{ route('user.healthTracker.healthSetting', ['user_type' => $userType]) }}">
                                    Click here to update the settings</a>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">

                        @if ($userData->gender == 'male')
                            @include('health-tracker.male-body-skeleton', ['data' => $measurementData])
                        @elseif($userData->gender == 'female')
                            @include('health-tracker.female-body-skeleton', ['data' => $measurementData])
                             @else
                            <div class="health-measurements-right">
                                <p>Please select your Gender to provide you with proper guidance</p>
                                    <a class="btn btn-secondary ripple-effect-dark btn-120"
                                        href="{{ route('user.profileSetting', ['user_type' => $userType]) }}">
                                        Update Gender Now
                                    </a>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </section>
        @if(!empty($rewardDetail) && !empty($rewardDetail->reward_game) && $rewardDetail->is_gamification == 1)
            <x-game-modal :rewardDetail="$rewardDetail" :module="'health-tracker'" />
        @endif
    </div>
    <!-- Main Content Start -->

@endsection
<link rel="stylesheet" type="text/css" href="{{ url('assets/css/style.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ url('assets/css/media.css') }}" />
@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\HealthMeasurementRequest', '#addForm') !!}
    <script src="{{ url('assets/js/setting.js') }}"></script>
    <script>
        /**
         * Add Health Tracker.
         * @request form fields
         * @response object.
         */
        const rewardData = @json($rewardDetail ?? null);
        var slider = document.getElementById('height_slider');
        var heightSliderValue = document.getElementById('height_slider-value');
        noUiSlider.create(slider, {
            connect: "lower",
            range: {
                min: 12,
                max: 8 * 12
            },
            start: 12,
            step: 1,
            format: {
                to: function(value) {
                    var totalInches = Math.round(+value);
                    var feet = Math.floor(totalInches / 12);
                    var inches = totalInches % 12;
                    var feetString = (feet == 0 ? "" : feet + "ft ");
                    var inchString = (inches == 0 ? "" : inches + "in ");
                    var combinedString = (feetString + inchString).trim();
                    return combinedString;
                },
                from: function(value) {
                    return value.replace(' in', '');
                }
            },
            tooltips: false,
            pips: {
                mode: 'values',
                values: [12, 24, 36, 48, 60, 72, 84, 96],
                density: 3,
                stepped: true,
                format: {
                    to: function(value) {
                        var totalInches = Math.round(+value);
                        var feet = Math.floor(totalInches / 12);
                        var inches = totalInches % 12;
                        return feet + " ft";
                    }
                }
            }
        });

        slider.noUiSlider.on('update', function(values, handle) {
            heightSliderValue.innerHTML = values[handle];
            let heightArr = values[handle].split("ft");
            let ft = heightArr.length ? parseInt(heightArr[0]) : 1;
            let inch = (heightArr.length > 1) ? heightArr[1] : 1;
            inch = (inch) ? inch.split("in") : [];
            inch = (inch.length) ? parseInt(inch[0].trim()) : 0;
            let height = (ft * 12) + inch;
            $("#heightField").val(height);
        });
        let heightValue = "{{ !empty($detail->height) ? $detail->height : 12 }}";
        slider.noUiSlider.set([heightValue]);

        function saveHealthMeasurement() {
            var formData = $("#addForm").serializeArray();
            if ($('#addForm').valid()) {
                $('#addBtn').prop('disabled', true);
                $('#addBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.healthTracker.saveHealthMeasurement') }}",
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
                                    window.location.href =
                                        "{{ route('user.healthTracker', ['user_type' => $userType]) }}";
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
            extension = extension.toLowerCase();
            if (imageLength < 10) {
                if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension == 'svg' || extension ==
                    'mpeg') {
                    var fileObj = document.getElementById("imgFieldId").files[0];
                    $('#uploadBtn').prop('disabled', true);
                    $('#uploadBtnLoader').show();
                    var formData = new FormData();
                    formData.append('file', fileObj);
                    formData.append('mediaFor', 'health-tracker');
                    formData.append('_token', "{{ csrf_token() }}");
                    $.ajax({
                        type: "POST",
                        url: "{{ route('common.saveMultipartMedia') }}",
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
                                                    <img style="height:50px;width:50px;" src="` + data.fileInfo.base_url +
                                    `" data-lity data-lity-target="` + data.fileInfo.base_url + `" alt="">
                                                    <a href="javascript:void(0);" class="remove-icon" id="remove" onclick="crossClick($(this))"><i class="iconmoon-close" aria-hidden="true"></i>X</a>
                                                    <input type="hidden" name="images[]" value="` + data.id + `" class="images">
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
            }else{
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
