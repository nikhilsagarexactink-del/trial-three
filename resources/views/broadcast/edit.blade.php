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
                    <li class="breadcrumb-item"><a href="{{route('user.broadcasts', ['user_type'=>$userType])}}">Broadcast</a></li>
                    <li class="breadcrumb-item active">Update Broadcast</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Broadcast
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false" action="{{route('common.updateBroadcast',['id'=>$id])}}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="form-group">
                            <label>Title<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Title" name="title" value="{{$result->title}}">
                            <span id="title-error" class="help-block error-help-block"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label>Message<span class="text-danger">*</span></label>
                            <textarea class="form-control text-editor" placeholder="Message" name="message">{{$result->message}}</textarea>
                            <span id="message-error" class="help-block error-help-block"></span>
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
                                    <span id="send_type-error" class="help-block error-help-block"></span>
                                </label>
                                <label class="form-check">
                                    <input type="checkbox" value="email" name="send_type[]" {{in_array("email", $sendType) ? "checked" : ""}}> <span>Email</span>
                                    <div class="checkbox__checkmark"></div>
                                    <span id="send_type-error" class="help-block error-help-block"></span>
                                </label>
                                <label class="form-check">
                                    <input type="checkbox" value="dashboard_alert" name="send_type[]" {{in_array("dashboard_alert", $sendType) ? "checked" : ""}}> <span>Dashboard Alert</span>
                                    <div class="checkbox__checkmark"></div>
                                    <!-- <span id="send_type-error" class="help-block error-help-block text-danger"></span> -->
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group custom-form-check-head">
                            <label>Users</label>
                            <div class="custom-form-check">
                                <label class="form-check">
                                    <input type="checkbox" value="active" name="users_status[]" {{in_array("active", $userStatus) ? "checked" : ""}}> <span>Active</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                                <label class="form-check">
                                    <input type="checkbox" value="inactive" name="users_status[]" {{in_array("inactive", $userStatus) ? "checked" : ""}}> <span>Inactive</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                            </div>
                        </div>
                        <div class="form-group custom-form-check-head">
                            <div class="custom-form-check">
                                <label class="form-check">
                                    <input type="checkbox" value="1" name="signed_up_last_thirty_days" {{$result->signed_up_last_thirty_days==1 ? "checked" : ""}}> <span>Signed up(in last 30 days)</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                            </div>
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
                            <div class="custom-form-check pt-1">
                                <label class="form-check">
                                    <input type="checkbox" id="lastLoginBetween" value="1" name="last_login_between" {{$result->last_login_between==1 ? "checked" : ""}}> <span>Last Login Between</span>
                                    <div class="checkbox__checkmark"></div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6" id="dateRangePickerDivId" {{$result->last_login_between==0 ? "style=display:none" : ""}}>
                            <div class="form-group">
                                <input id="daterangepicker" type="text" class="form-control" placeholder="Date Range" name="last_login_date" value="{{!empty($result->last_login_from_date) ? ( date('m/d/Y', strtotime($result['last_login_from_date'])).' - '.date('m/d/Y', strtotime($result['last_login_to_date'])) ) : ''}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group custom-radio">
                    <label class="form-check">
                        <input type="radio" class="schedule-time" value="now" name="type" {{$result->type=='now' ? "checked" : ""}}> <span>Now</span>
                    </label>
                    <label class="form-check">
                        <input type="radio" class="schedule-time" value="scheduled" name="type" {{$result->type=='scheduled' ? "checked" : ""}}> <span>Scheduled</span>
                    </label>
                </div>
            </div>
            <div class="row" id="scheduledTimeDivId" {{$result->type=='now' ? "style=display:none" : ""}}>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text" id="datepicker" class="form-control" placeholder="Scheduled Date" name="scheduled_date" value="{{$result->scheduled_date}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text" id="timepicker" class="form-control" placeholder="Scheduled Time" name="scheduled_time" value="{{$result->scheduled_time}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Timezone" name="timezone" readonly="true" value="{{$timezone}}">
                    </div>
                </div>
            </div>
            <div class="btn_row text-center">
                <!-- <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn" onClick="updateBroadcast()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button> -->
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.broadcasts', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection

@section('js')
{{-- {!! JsValidator::formRequest('App\Http\Requests\BroadcastRequest','#updateForm') !!} --}}

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

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('updateForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    function updateBroadcast() {
        var formData = $("#updateForm").serializeArray();
        var startDate = $('#daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        formData.push({
            name: 'last_login_from_date',
            value: startDate
        });
        formData.push({
            name: 'last_login_to_date',
            value: endDate
        });
        if ($('#updateForm').valid()) {
            $('#updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{route('common.updateBroadcast', ['id'=>'%recordId%'])}}";
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
                            window.location.href = "{{route('user.broadcasts', ['user_type'=>$userType])}}";
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

    $(function() {
        $("#datepicker").datepicker();
        $("#timepicker").timepicker({
            defaultTime: 'value'
        });
        $("#daterangepicker").daterangepicker();
        $(".schedule-time").on('click', function() {
            $("#scheduledTimeDivId").hide();
            if (this.value == "scheduled") {
                $("#scheduledTimeDivId").show();
            }
        });
        $("#lastLoginBetween").on('click', function() {
            $("#dateRangePickerDivId").hide();
            if (this.checked) {
                $("#dateRangePickerDivId").show();
            }
        });
    });
</script>
@endsection