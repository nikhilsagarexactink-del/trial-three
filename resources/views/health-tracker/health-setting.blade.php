@extends('layouts.app')
@section('head')
<title>Health Tracker | Health Setting</title>
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
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard', ['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.healthTracker', ['user_type'=>$userType])}}">Health Tracker</a></li>
                    <li class="breadcrumb-item active">Health Settings</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Health Settings
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.healthTracker.saveHealthSetting')}}">
            @csrf
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="d-block">Weight<span class="text-danger">*</span></label>
                                <ul class="health-checkbox">
                                    <li>
                                        <input type="radio" name="weight" value="POUNDS_LBS" {{(!empty($detail) && $detail->weight=='POUNDS_LBS')  ? "checked" : ''}}> <span>POUNDS/LBS</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="weight" value="KILOGRAM/KG" {{(!empty($detail) && $detail->weight=='KILOGRAM/KG')  ? "checked" : ''}}> <span>KILOGRAMS/KG</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Height<span class="text-danger">*</span></label>
                                <ul class="health-checkbox">
                                    <li>
                                        <input type="radio" name="height" value="INCHES" {{(!empty($detail) && $detail->height=='INCHES')  ? "checked" : ''}}> <span>INCHES</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="height" value="MILLIMETERS" {{(!empty($detail) && $detail->height=='MILLIMETERS')  ? "checked" : ''}}> <span>MILLIMETERS</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>HOW OFTEN WOULD YOU LIKE TO LOG YOUR HEALTH MARKERS ?<span class="text-danger">*</span></label>
                                <ul class="health-checkbox">
                                    <li>
                                        <input type="radio" name="log_marker" value="DAILY" {{(!empty($detail) && $detail->log_marker=='DAILY')  ? "checked" : ''}}> <span>DAILY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_marker" value="WEEKLY" {{(!empty($detail) && $detail->log_marker=='WEEKLY')  ? "checked" : ''}}> <span>WEEKLY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_marker" value="EVERY_OTHER_WEEK" {{(!empty($detail) && $detail->log_marker=='EVERY_OTHER_WEEK')  ? "checked" : ''}}> <span>EVERY OTHER WEEK</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_marker" value="MONTHLY" {{(!empty($detail) && $detail->log_marker=='MONTHLY')  ? "checked" : ''}}> <span>MONTHLY</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>HOW OFTEN WOULD YOU LIKE TO LOG YOUR HEALTH MEASUREMENT ?<span class="text-danger">*</span></label>
                                <ul class="health-checkbox">
                                    <li>
                                        <input type="radio" name="log_measurement" value="DAILY" {{(!empty($detail) && $detail->log_measurement=='DAILY')  ? "checked" : ''}}> <span>DAILY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_measurement" value="WEEKLY" {{(!empty($detail) && $detail->log_measurement=='WEEKLY')  ? "checked" : ''}}> <span>WEEKLY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_measurement" value="EVERY_OTHER_WEEK" {{(!empty($detail) && $detail->log_measurement=='EVERY_OTHER_WEEK')  ? "checked" : ''}}> <span>EVERY OTHER WEEK</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_measurement" value="MONTHLY" {{(!empty($detail) && $detail->log_measurement=='MONTHLY')  ? "checked" : ''}}> <span>MONTHLY</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>WHAT DAY OF THE WEEK IS BEST TO LOG YOUR RESULTS ?<span class="text-danger">*</span></label>
                                <ul class="health-checkbox">
                                    <li>
                                        <input type="radio" name="log_day" value="MONDAY" {{(!empty($detail) && $detail->log_day=='MONDAY')  ? "checked" : ''}}> <span>MONDAY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_day" value="TUESDAY" {{(!empty($detail) && $detail->log_day=='TUESDAY')  ? "checked" : ''}}> <span>TUESDAY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_day" value="WEDNESDAY" {{(!empty($detail) && $detail->log_day=='WEDNESDAY')  ? "checked" : ''}}> <span>WEDNESDAY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_day" value="THURSDAY" {{(!empty($detail) && $detail->log_day=='THURSDAY')  ? "checked" : ''}}> <span>THURSDAY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_day" value="FRIDAY" {{(!empty($detail) && $detail->log_day=='FRIDAY')  ? "checked" : ''}}> <span>FRIDAY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_day" value="SATURDAY" {{(!empty($detail) && $detail->log_day=='SATURDAY')  ? "checked" : ''}}> <span>SATURDAY</span>
                                    </li>
                                    <li>
                                        <input type="radio" name="log_day" value="SUNDAY" {{(!empty($detail) && $detail->log_day=='SUNDAY')  ? "checked" : ''}}> <span>SUNDAY</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <h4>Marker Fields</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                        <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->weight_status=='enabled')  ? "checked" : ''}} role="switch" name="weight_status" id="weight_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="weight_status">Weight Status</span>
                                </label>
                            </div>
                            <!-- <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->weight_status=='enabled')  ? "checked" : ''}} role="switch" name="weight_status" id="weight_status">
                                    <label class="form-check-label" for="weight_status">Weight Status</label>
                                </div>
                            </div> -->
                        </div>
                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->body_fat_status=='enabled')  ? "checked" : ''}} role="switch" name="body_fat_status" id="body_fat_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="body_fat_status">Body Fat Status</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->bmi_status=='enabled')  ? "checked" : ''}} role="switch" name="bmi_status" id="bmi_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="bmi_status">BMI Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->body_water_status=='enabled')  ? "checked" : ''}} role="switch" name="body_water_status" id="body_water_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="body_water_status">Body Water Status</span>
                               </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->skeletal_muscle_status=='enabled')  ? "checked" : ''}} role="switch" name="skeletal_muscle_status" id="skeletal_muscle_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="skeletal_muscle_status">Skeletal Muscle Status</span>
                             </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->health_marker_images_status=='enabled')  ? "checked" : ''}} role="switch" name="health_marker_images_status" id="health_marker_images_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="health_marker_images_status">Images</span>
                             </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <h4>Measurement Fields</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->height_status=='enabled')  ? "checked" : ''}} role="switch" name="height_status" id="height_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="height_status">Height Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->neck_status=='enabled')  ? "checked" : ''}} role="switch" name="neck_status" id="neck_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="neck_status">Neck Status</span>
                               </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->shoulder_status=='enabled')  ? "checked" : ''}} role="switch" name="shoulder_status" id="shoulder_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="shoulder_status">Shoulder Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->chest_status=='enabled')  ? "checked" : ''}} role="switch" name="chest_status" id="chest_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="chest_status">Chest Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->waist_status=='enabled')  ? "checked" : ''}} role="switch" name="waist_status" id="waist_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="waist_status">Waist Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->abdomen_status=='enabled')  ? "checked" : ''}} role="switch" name="abdomen_status" id="abdomen_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="abdomen_status">Abdomen Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->hip_status=='enabled')  ? "checked" : ''}} role="switch" name="hip_status" id="hip_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="hip_status">Hip Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->bicep_left_status=='enabled')  ? "checked" : ''}} role="switch" name="bicep_left_status" id="bicep_left_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="bicep_left_status">Bicep Left Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->bicep_right_status=='enabled')  ? "checked" : ''}} role="switch" name="bicep_right_status" id="bicep_right_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="bicep_right_status">Bicep Right Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->thigh_left_status=='enabled')  ? "checked" : ''}} role="switch" name="thigh_left_status" id="thigh_left_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="thigh_left_status">Thigh Left Status</span>
                               </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->thigh_right_status=='enabled')  ? "checked" : ''}} role="switch" name="thigh_right_status" id="thigh_right_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="thigh_right_status">Thigh Right Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->calf_left_status=='enabled')  ? "checked" : ''}} role="switch" name="calf_left_status" id="calf_left_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="calf_left_status">Calf Left Status</span>
                               </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->calf_right_status=='enabled')  ? "checked" : ''}} role="switch" name="calf_right_status" id="calf_right_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="calf_right_status">Calf Right Status</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{(!empty($detail) && $detail->health_measurement_images_status=='enabled')  ? "checked" : ''}} role="switch" name="health_measurement_images_status" id="health_measurement_images_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="health_measurement_images_status">Images</span>
                             </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <h4>Reminder Notification</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Notification Type</label>
                            <select class="form-control" name="notification_type">
                                <option value="">Select</option>
                                @if($notificationTypes->count() > 0)
                                    @foreach($notificationTypes as $notificationType)
                                        <option value="{{ $notificationType->id }}" {{!empty($reminderData) && $reminderData['master_notification_type_id'] == $notificationType->id ? 'selected' : ''}}>{{ $notificationType->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        <input type="hidden" name="module_id" {{!empty($moduleData) ? 'value=' . $moduleData['id'] : ''}}>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="reminder_time">Reminder Time</label>
                        <input type="time" id="reminder_time" name="reminder_time" class="form-control"  value="{{ !empty($reminderData) && isset($reminderData['reminder_time']) ? $reminderData['reminder_time'] : '' }}">
                    </div>
                    </div>
                </div>
            </div>

            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn" onClick="saveHealthSetting()">Submit<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.healthTracker', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\HealthSettingRequest','#addForm') !!}

<script>
    /**
     * Add Health Setting.
     * @request form fields
     * @response object.
     */
    function saveHealthSetting() {
        var formData = $("#addForm").serializeArray();
        if ($('#addForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.healthTracker.saveHealthSetting')}}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.href = "{{route('user.healthTracker', ['user_type'=>$userType])}}";
                        }, 500)
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
                    } else {
                        _toast.error('Please try again.');
                    }
                },
            });
        }
    };
</script>
@endsection