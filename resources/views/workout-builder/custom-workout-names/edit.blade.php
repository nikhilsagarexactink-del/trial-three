@extends('layouts.app')
@section('head')
<title>Custom Workout Name | Update</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php 
$userType = userType(); 
$id = request()->route('id');
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
    <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.customWorkoutNamesIndex', ['user_type' => $userType]) }}">Custom Workout Names</a></li>
                    <li class="breadcrumb-item active">Update</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update
            </h2>
            <!-- Page Title End -->
        </div>
    </div>
    <section class="content white-bg">
    <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false"  action="{{ route('common.updateCustomWorkoutName', ['id' => $id]) }}">
         @csrf
            <div class="row">
               <div class="col-md-6">
                      <div class="form-group">
                                    <label for="title" class="form-label">Title<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" id="tile" type="text" name="title" value="{{ $result->title ?? '' }}" placeholder="Title" >
                                    <span id="title-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label>Select Day</label>
                                    <select id="day" name="day" class="form-control">
                                        <option value="">Select Day</option>
                                        <option value="SUNDAY" {{ $result->day ==  'SUNDAY' ? 'selected' : '' }} >SUNDAY</option>
                                        <option value="MONDAY" {{ $result->day ==  'MONDAY' ? 'selected' : '' }}>MONDAY</option>
                                        <option value="TUESDAY" {{ $result->day ==  'TUESDAY' ? 'selected' : '' }}>TUESDAY</option>
                                        <option value="WEDNESDAY" {{ $result->day ==  'WEDNESDAY' ? 'selected' : '' }}>WEDNESDAY</option>
                                        <option value="THURSDAY" {{ $result->day ==  'THURSDAY' ? 'selected' : '' }}>THURSDAY</option>
                                        <option value="FRIDAY" {{ $result->day ==  'FRIDAY' ? 'selected' : '' }}>FRIDAY</option>
                                        <option value="SATURDAY" {{ $result->day ==  'SATURDAY' ? 'selected' : '' }}>SATURDAY</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reminder_time">Reminder Time</label>
                                    <input type="time" id="reminder_time" name="reminder_time"  value="{{ $result->reminder_time ?? '' }}"   class="form-control">
                                </div>
                            </div>
                            <div class="btn_row text-center">
                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120 updateBtn"onClick="updateWorkouts()">
                                    Update <span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span>
                                </button>
                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{ route('user.customWorkoutNamesIndex', ['user_type' => $userType]) }}">Cancel</a>
                            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection
@section('js')
{!! JsValidator::formRequest('App\Http\Requests\CustomWorkoutNameRequest','#updateForm') !!}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('updateForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    function updateWorkouts() {
        var formData = $("#updateForm").serializeArray();
        if ($('#updateForm').valid()) {
            $('#updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{ route('common.updateCustomWorkoutName', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', "{{ $id }}");
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
                            window.location.href = "{{ route('user.customWorkoutNamesIndex', ['user_type' => $userType]) }}";
                        }, 500);
                    } else {
                        _toast.error('Something went wrong. Please try again');
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
                        _toast.error('Custom workout name not updated.');
                    }
                },
            });
        } else {
            console.log("Form is not valid");
        }
    }
</script>
@endsection
