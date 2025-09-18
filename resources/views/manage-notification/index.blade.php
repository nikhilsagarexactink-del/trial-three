@extends('layouts.app')
@section('head')
<title>Notification | Manage</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php $userType = userType(); @endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <!-- <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manage Notification</li>
                </ol>
            </nav> -->
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Manage Notification
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updateNotification" class="form-head" method="POST" novalidate autocomplete="false">
            <div class="row">
                @if(count($modules['fitness-profile']['permission']) > 0)
                    <div class="col-md-12">
                        <h5>Fitness Notification</h5>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Notification Type</label>
                                        <select class="form-control" name="fitness_profile[notification_type]">
                                            <option value="">Select</option>
                                            @if($notificationTypes->count() > 0)
                                                @foreach($notificationTypes as $notificationType)
                                                    <option value="{{ $notificationType->id }}" {{!empty($modules['fitness-profile']['notification_setting']) && $modules['fitness-profile']['notification_setting']['master_notification_type_id'] == $notificationType->id ? 'selected' : ''}}>{{ $notificationType->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    <input type="hidden" name="fitness_profile[module_id]" {{!empty($modules['fitness-profile']['permission']) ? 'value=' . $modules['fitness-profile']['permission']['id'] : ''}}>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="reminder_time">Reminder Time</label>
                                    <input type="time" id="reminder_time" name="fitness_profile[reminder_time]" class="form-control"  value="{{ !empty($modules['fitness-profile']['notification_setting']) && isset($modules['fitness-profile']['notification_setting']['reminder_time']) ? $modules['fitness-profile']['notification_setting']['reminder_time'] : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(count($modules['health-tracker']['permission']) > 0)
                    <div class="col-md-12">
                        <h5>Health Tracker Notification</h5>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Notification Type</label>
                                        <select class="form-control" name="health_tracker[notification_type]">
                                            <option value="">Select</option>
                                            @if($notificationTypes->count() > 0)
                                                @foreach($notificationTypes as $notificationType)
                                                    <option value="{{ $notificationType->id }}" {{!empty($modules['health-tracker']['notification_setting']) && $modules['health-tracker']['notification_setting']['master_notification_type_id'] == $notificationType->id ? 'selected' : ''}}>{{ $notificationType->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    <input type="hidden" name="health_tracker[module_id]" {{!empty($modules['health-tracker']['permission']) ? 'value=' . $modules['health-tracker']['permission']['id'] : ''}}>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="reminder_time">Reminder Time</label>
                                    <input type="time" id="reminder_time" name="health_tracker[reminder_time]" class="form-control"  value="{{ !empty($modules['health-tracker']['notification_setting']) && isset($modules['health-tracker']['notification_setting']['reminder_time']) ? $modules['health-tracker']['notification_setting']['reminder_time'] : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(count($modules['health-tracker']['permission']) > 0 || count($modules['fitness-profile']['permission']) > 0)
                    <div class="btn_row text-center">
                        <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn" onClick="updateNotificationSetting()">Update<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                        <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.dashboard', ['user_type'=>$userType])}}">Cancel</a>
                    </div>
                @endif
                @if(count($modules['health-tracker']['permission']) == 0 && count($modules['fitness-profile']['permission']) == 0)
                    <div class="alert alert-danger" role="alert"> You don't have permission to access notification-related modules.</div>
                @endif
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')

<script>
    
    function updateNotificationSetting() {
        var formData = $("#updateNotification").serializeArray();
        if ($('#updateNotification').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.updateNotificationSetting')}}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                    } else {
                        _toast.error(response.message);
                    }
                },
                error: function(err) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                },

            });
        }
    };
</script>
@endsection