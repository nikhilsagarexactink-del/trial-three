@extends('layouts.app')
@section('head')
<title>Custom Workout Name | Add</title>
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
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.customWorkoutNamesIndex', ['user_type' => $userType]) }}">Custom Workout Names</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create
            </h2>
            <!-- Page Title End -->
        </div>
    </div>
    <section class="content white-bg">
    <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{ route('common.saveCustomWorkoutName') }}">
         @csrf
            <div class="row">
               <div class="col-md-6">
                      <div class="form-group">
                                    <label for="title" class="form-label">Title<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" id="tile" type="text" name="title"
                                        placeholder="Title">
                                    <span id="title-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label>Select Day</label>
                                    <select id="day" name="day" class="form-control">
                                        <option value="">Select Day</option>
                                        <option value="SUNDAY">SUNDAY</option>
                                        <option value="MONDAY">MONDAY</option>
                                        <option value="TUESDAY">TUESDAY</option>
                                        <option value="WEDNESDAY">WEDNESDAY</option>
                                        <option value="THURSDAY">THURSDAY</option>
                                        <option value="FRIDAY">FRIDAY</option>
                                        <option value="SATURDAY">SATURDAY</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reminder_time">Reminder Time</label>
                                    <input type="time" id="reminder_time" name="reminder_time" class="form-control">
                                </div>
                            </div>
                            <div class="btn_row text-center">
                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120 addBtn"onClick="addWorkouts()">
                                    Add <span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span>
                                </button>
                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{ route('user.customWorkoutNamesIndex', ['user_type' => $userType]) }}">Cancel</a>
                            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection
@section('js')
{!! JsValidator::formRequest('App\Http\Requests\CustomWorkoutNameRequest','#addForm') !!}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('addForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    function addWorkouts() {
        var formData = $("#addForm").serializeArray();

        if ($('#addForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.saveCustomWorkoutName') }}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{ route('user.customWorkoutNamesIndex', ['user_type' => $userType]) }}";
                        }, 500);
                    } else {
                        _toast.error('Something went wrong. Please try again');
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
                        _toast.error('Custom name not created.');
                    }
                },
            });
        } else {
            console.log("Form is not valid"); // Debugging: Print form validation status
        }
    }
</script>
@endsection
