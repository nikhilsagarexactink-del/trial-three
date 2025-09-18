@extends('layouts.app')
<title>Calendar Setting</title>
@section('content')
@include('layouts.sidebar')
@php 
$userType = userType();
$userData = getUser();
$placeHolder = !empty($athlete) ? 'for '.$athlete['first_name'] : '';
$user_id = !empty($athlete) ? $athlete['id'] : $userData['id'];
@endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="{{route('user.calendarIndex', ['user_type'=>$userType])}}">Calendar</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Calendar Setting</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0"> Calendar Setting {{$placeHolder}} </h2>
                <!-- Page Title End -->
            </div>
        </div>
        <section class="content white-bg">
        <form id="saveCalendarSetting" class="form-head" method="POST" novalidate autocomplete="false">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select what goes into the calendar.</label>
                            <div class="custom-form-check-head">
                                @if(!empty($modules))
                                    @foreach($modules as $module)
                                        @php 
                                            $filter = array_filter($settings, function ($value) use ($module) {
                                                return ($value['calendar_module_id'] == $module['id']) ? true : false;
                                            });
                                        @endphp
                                        <div class="custom-form-check">
                                            <label class="form-check">
                                                <input type="checkbox" 
                                                    value="{{$module['id']}}" 
                                                    class="calendar-checkbox" 
                                                    {{!empty($filter) ? 'checked' : ''}}  
                                                    name="module_name[]" 
                                                    data-module-id="{{$module['id']}}" 
                                                    title="Module Name">
                                                <div class="checkbox__checkmark"></div>
                                            </label>
                                            <div class="getstart-desc">
                                                <span class="getstart-label">{{$module['name']}}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <input type="hidden" name="user_id" value="{{$user_id}}">
                        </div>
                    </div>     
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Enable Push Notifications.</label>
                            @if(!empty($modules))
                                @foreach($modules as $module)
                                @php 
                                    $filter = array_filter($settings, function ($value) use ($module) {
                                        return ($value['calendar_module_id'] == $module['id'] && $value['is_push_notification']) ? true : false;
                                    });
                                @endphp
                                    <div class="custom-form-check-head">
                                        <div class="custom-form-check">
                                            <label class="form-check">
                                                <input type="checkbox" 
                                                    class="push-notification-checkbox" 
                                                    {{!empty($filter) ? 'checked' : ''}} 
                                                    name="is_push_notification_{{$module['id']}}" 
                                                    data-module-id="{{$module['id']}}" 
                                                    disabled>
                                                <div class="checkbox__checkmark"></div>
                                            </label>
                                            <div class="getstart-desc">
                                                        <span class="getstart-label">{{$module['name']}}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class= "form-group">
                            <label>Notification Type</label>
                            <div class=" select-arrow">
                            
                                <select class="selectpicker select-custom form-control" name="notification_type">
                                    <option value="">Select Notification Type</option>
                                    <option value="email" <?= !empty($notificationSetting) && $notificationSetting['notification_type'] === 'email' ? 'selected' : '' ?>>Email</option>
                                    <option value="text-message" <?= !empty($notificationSetting) && $notificationSetting['notification_type']  === 'text-message' ? 'selected' : '' ?>>Text Message</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>When to send the notification</label>
                            <div class=" select-arrow">
                                <select class="selectpicker select-custom form-control " name="recuring_time">  
                                    <option value="">Notification Recuring Time</option>
                                    <option value="day-before" <?= !empty($notificationSetting) && $notificationSetting['recuring_time'] === 'day-before' ? 'selected' : '' ?>>Day Before</option>
                                    <option value="day-of" <?= !empty($notificationSetting) && $notificationSetting['recuring_time'] === 'day-of' ? 'selected' : '' ?>>Day Of</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addrBtn" onClick="saveSetting()">Update<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.calendarIndex', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select all calendar and push notification checkboxes
            const calendarCheckboxes = document.querySelectorAll('.calendar-checkbox');
            const pushNotificationCheckboxes = document.querySelectorAll('.push-notification-checkbox');

            function updatePushNotificationState(moduleId) {
                // Find the related push notification checkbox
                const pushNotificationCheckbox = document.querySelector(
                    `.push-notification-checkbox[data-module-id="${moduleId}"]`
                );

                // Find the related calendar checkbox
                const calendarCheckbox = document.querySelector(
                    `.calendar-checkbox[data-module-id="${moduleId}"]`
                );

                // Enable or disable the push notification checkbox based on the calendar checkbox state
                if (pushNotificationCheckbox && calendarCheckbox) {
                    pushNotificationCheckbox.disabled = !calendarCheckbox.checked;
                    // If disabling the push notification checkbox, also uncheck it
                    if (!calendarCheckbox.checked && pushNotificationCheckbox.checked) {
                        pushNotificationCheckbox.checked = false;
                    }
                }
            }
            // Add event listeners to calendar checkboxes
            calendarCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const moduleId = this.getAttribute('data-module-id');
                    updatePushNotificationState(moduleId);
                });

                // Initial state update
                const moduleId = checkbox.getAttribute('data-module-id');
                updatePushNotificationState(moduleId);
            });
        });
        function saveSetting(){
            var formData = $("#saveCalendarSetting").serializeArray();
            if ($('#saveCalendarSetting').valid()) {
                $('#addBtn').prop('disabled', true);
                $('#addBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{route('common.saveCalendarSetting')}}",
                    data: formData,
                    success: function(response) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            setTimeout(function() {
                                window.location.href = "{{route('user.calendarIndex', ['user_type'=>$userType])}}";
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
                            var errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                        }
                    },
                });
            }
        }
    </script>
@endsection
