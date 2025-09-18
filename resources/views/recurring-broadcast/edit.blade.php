@extends('layouts.app')
@section('head')
<title>Broadcast | Update</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php
$id = request()->route('id');
$userType = userType();
$sendType = !empty($result->send_type) ? explode(',', $result->send_type) : [];
$userStatus = !empty($result->users_status) ? explode(',', $result->users_status) : [];
$userTypes = !empty($result->user_types) ? explode(',', $result->user_types) : [];

@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.recurringBroadcasts', ['user_type'=>$userType])}}">Recurring Broadcast</a></li>
                    <li class="breadcrumb-item active">Update Recurring Broadcast</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Recurring Broadcast
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false" action="{{route('common.updateRecurringBroadcast',['id'=>$id])}}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="form-group">
                            <label>Title<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Title" name="title" value="{{$result->title}}">
                            <span id="title-error" class="help-block error-help-block text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label>Message<span class="text-danger">*</span></label>
                            <textarea class="form-control text-editor" placeholder="Message" name="message">{{$result->message}}</textarea>
                            <span id="message-error" class="help-block error-help-block text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="form-group custom-form-check-head">
                            <label>Send Type<span class="text-danger">*</span></label>
                            <div class="custom-form-check">
                                <label class="form-check">
                                    <input type="checkbox" value="alert" name="send_type[]" {{in_array("alert", $sendType) ? "checked" : ""}}> <span>SMS Alert</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                                <label class="form-check">
                                    <input type="checkbox" value="email" name="send_type[]" {{in_array("email", $sendType) ? "checked" : ""}}> <span>Email</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                            </div>
                            <span id="send_type-error" class="help-block error-help-block text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="trigger_type">Event Trigger</label>
                            <div class="select-arrow">
                                <select class="selectpicker select-custom form-control" id="trigger_type" name="trigger_event" onchange="showFieldsBasedOnTrigger()">
                                    <option value="sign_up" {{ $result->trigger_event == 'sign_up' ? 'selected' : '' }}>Sign up</option>
                                    <option value="day_after_sign_up" {{ $result->trigger_event == 'day_after_sign_up' ? 'selected' : '' }}>Day After Sign Up</option>
                                    <option value="last_login" {{ $result->trigger_event == 'last_login' ? 'selected' : '' }}>Last Login</option>
                                    <option value="hasnt_logged_in" {{ $result->trigger_event == 'hasnt_logged_in' ? 'selected' : '' }}>Hasn’t Logged In</option>
                                    <option value="anniversary" {{ $result->trigger_event == 'anniversary' ? 'selected' : '' }}>Anniversary</option>
                                </select>
                            </div>
                        </div>
                        <div id="userSignupFields" class="trigger-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_day">From Day</label>
                                        <input type="number" id="from_day" name="from_day" class="form-control" placeholder="-7" value="{{$result->from_day}}" max="0"/>
                                        <span id="from_day-error" class="help-block error-help-block text-danger pt-5"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_day">To Day</label>
                                        <input type="number" id="to_day" name="to_day" value="{{$result->to_day}}" class="form-control" placeholder="0"  min="-1000" max="0"/>
                                        <span id="to_day-error" class="help-block error-help-block text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="hasntLoggedInFields" class="form-group trigger-fields" style="display: none;">
                            <label for="has_not_logged_in_days">Hasn’t Logged In (days):</label>
                            <div class="select-arrow">
                                <select id="has_not_logged_in_days" name="has_not_logged_in_days" class="form-control">
                                    <option value="3"   {{ $result->has_not_logged_in_days == 3 ? 'selected' : '' }}>3 Days</option>
                                    <option value="7"   {{ $result->has_not_logged_in_days == 7 ? 'selected' : '' }}>7 Days</option>
                                    <option value="10"  {{ $result->has_not_logged_in_days == 10 ? 'selected' : '' }}>10 Days</option>
                                    <option value="14"  {{ $result->has_not_logged_in_days == 14 ? 'selected' : '' }}>14 Days</option>
                                    <option value="30"  {{ $result->has_not_logged_in_days == 30 ? 'selected' : '' }}>30 Days</option>
                                    <option value="60"  {{ $result->has_not_logged_in_days == 60 ? 'selected' : '' }}>60 Days</option>
                                    <option value="90"  {{ $result->has_not_logged_in_days == 90 ? 'selected' : '' }}>90 Days</option>
                                    <option value="180" {{ $result->has_not_logged_in_days == 180 ? 'selected' : '' }}>6 Months</option>
                                    <option value="365" {{ $result->has_not_logged_in_days == 365 ? 'selected' : '' }}>1 Year</option>
                                </select>
                            </div>
                        </div>
                        <div id="anniversaryFields" class="form-group trigger-fields" style="display: none;">
                            <label for="anniversary_months">Anniversary:</label>
                            <div class="select-arrow">
                                <select id="anniversary_months" name="anniversary_months" class="form-control">
                                    <option value="1"  {{ $result->anniversary_months == 1 ? 'selected' : '' }}>1 Month</option>
                                    <option value="3"  {{ $result->anniversary_months == 3 ? 'selected' : '' }}>3 Months</option>
                                    <option value="6"  {{ $result->anniversary_months == 6 ? 'selected' : '' }}>6 Months</option>
                                    <option value="12" {{ $result->anniversary_months == 12 ? 'selected' : '' }}>1 Year</option>
                                    <option value="24" {{ $result->anniversary_months == 24 ? 'selected' : '' }}>2 Years</option>
                                    <option value="36" {{ $result->anniversary_months == 36 ? 'selected' : '' }}>3 Years</option>
                                    <option value="48" {{ $result->anniversary_months == 48 ? 'selected' : '' }}>4 Years</option>
                                    <option value="60" {{ $result->anniversary_months == 60 ? 'selected' : '' }}>5 Years</option>
                                    <option value="72" {{ $result->anniversary_months == 72 ? 'selected' : '' }}>6 Years</option>
                                    <option value="84" {{ $result->anniversary_months == 84 ? 'selected' : '' }}>7 Years</option>
                                    <option value="96" {{ $result->anniversary_months == 96 ? 'selected' : '' }}>8 Years</option>
                                    <option value="108" {{ $result->anniversary_months == 108 ? 'selected' : '' }}>9 Years</option>
                                    <option value="120" {{ $result->anniversary_months == 120 ? 'selected' : '' }}>10 Years</option>
                                    <option value="132" {{ $result->anniversary_months == 132 ? 'selected' : '' }}>11 Years</option>
                                    <option value="144" {{ $result->anniversary_months == 144 ? 'selected' : '' }}>12 Years</option>
                                    <option value="156" {{ $result->anniversary_months == 156 ? 'selected' : '' }}>13 Years</option>
                                    <option value="168" {{ $result->anniversary_months == 168 ? 'selected' : '' }}>14 Years</option>
                                    <option value="180" {{ $result->anniversary_months == 180 ? 'selected' : '' }}>15 Years</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 custom-form-check-head">
                            <label class="mt-3">Send By Role</label>
                            <div class="custom-form-check mb-2">                               
                                <label class="form-check">
                                    <input type="checkbox" value="admin" name="user_types[]" {{in_array("admin", $userTypes) ? "checked" : ""}}> <span>Admin</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                                <label class="form-check">
                                    <input type="checkbox" value="content-creator" name="user_types[]" {{in_array("content-creator", $userTypes) ? "checked" : ""}}> <span>Content Creator</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                            </div>
                            <div class="custom-form-check mb-2">                                 
                                <label class="form-check">
                                    <input type="checkbox" value="parent" name="user_types[]" {{in_array("parent", $userTypes) ? "checked" : ""}}> <span>Parent</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                                <label class="form-check">
                                    <input type="checkbox" value="athlete" name="user_types[]" {{in_array("athlete", $userTypes) ? "checked" : ""}}> <span>Athlete</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                            </div>
                            <div class="custom-form-check mb-2">                               
                                <label class="form-check">
                                    <input type="checkbox" value="coach" name="user_types[]" {{in_array("coach", $userTypes) ? "checked" : ""}}> <span>Coach</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                            </div>
                        </div>
                        <div id="mailSend" class="col-md-6">
                            <div class="form-group">
                                <label for="send_time">Send Time:</label>
                                <input type="time" value="{{$result->send_time}}" id="send_time" name="send_time" class="form-control">
                                <span id="send_time-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
            <!-- @verbatim
                <div class="col-md-6 " style="background: beige; border-radius: 4px;">
                    <strong>Available Tokens:</strong><br>
                    [[user_name]], [[first_name]], [[last_name]], [[email]], [[reset_password_link]], [[signup_date]], [[renewal_date]], [[next_billing_date]], [[next_billing_amount]], [[last_billing_date]], [[last_billing_amount]], [[last_login_date]], [[next_workout_date]], [[last_workout_date]]
                </div>
            @endverbatim -->
            <div class="col-md-6 " style="background: beige; border-radius: 4px;">
                <strong>Available Tokens:</strong><br>
                @if(!empty($tokens) && count($tokens) > 0)
                    @foreach($tokens as $token)
                        [[{{$token->token_key}}]],
                    @endforeach
                @endif
            </div>
            <div class="mt-4 btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn" onClick="updateBroadcast()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.recurringBroadcasts', ['user_type'=>$userType])}}">Cancel</a>
            </div> 
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection

@section('js')
{{-- {!! JsValidator::formRequest('App\Http\Requests\RecurringBroadcastRequest','#updateForm') !!} --}}

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

    $(document).ready(function() {
        showFieldsBasedOnTrigger();
    });


    /**
     * Update Record.
     * @request form fields
     * @response object.
     */

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('updateForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    function updateBroadcast() {
        var formData = $("#updateForm").serializeArray();
        if ($('#updateForm').valid()) {
            $('#updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{route('common.updateRecurringBroadcast', ['id'=>'%recordId%'])}}";
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
                            window.location.href = "{{route('user.recurringBroadcasts', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
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
                        _toast.error('Broadcast not updated.');
                    }

                },
            });
        }
    };

    function showFieldsBasedOnTrigger() {
        // Hide all trigger-specific fields
        document.querySelectorAll('.trigger-fields').forEach(field => {
            field.style.display = 'none';
        });

        // Show specific fields based on selected trigger type
        const triggerType = document.getElementById('trigger_type').value;
        const sendTimeInput = document.getElementById('send_time'); // Actual input field

        if (triggerType === 'sign_up' || triggerType === 'last_login') {
            document.getElementById('userSignupFields').style.display = 'block';
        } else if (triggerType === 'hasnt_logged_in') {
            document.getElementById('hasntLoggedInFields').style.display = 'block';
        } else if (triggerType === 'anniversary') {
            document.getElementById('anniversaryFields').style.display = 'block';
        }
        if (triggerType === 'sign_up') {
            document.getElementById('mailSend').style.display = 'none';
            sendTimeInput.value = ''; // Clear the input value when hidden
        }
        else{
            document.getElementById('mailSend').style.display = 'block';
        }
    }
</script>
@endsection