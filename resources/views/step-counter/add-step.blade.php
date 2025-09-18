@extends('layouts.app')
@section('head')
    <title>Step Counter | Add Input</title>
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
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.stepCounter', ['user_type' => $userType]) }}">Step Counter</a></li>
                        <li class="breadcrumb-item active">Add Input</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                   Step Counter
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            <form id="addStepForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.stepCounter.saveUserGoalLog') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="mb-2"><span id="takeInDate">How many steps Did you Take today
                                            ?</span></label>
                                    <div class="water-input">
                                        <input type="text" class="form-control" placeholder="" name="step_value">
                                        <span><strong>steps</strong></span>
                                    </div>
                                    <span id="step_value-error" class="help-block error-help-block text-danger"></span>

                                </div>
                                <input type="hidden" class="form-control" id="selectedDateField" name="date">
                                <a href="javascript:void(0)" id="inputDatepickerLink" onClick="inputDate()">Input another
                                    date</a>
                            </div>
                            <div class="col-md-12" id="inputDateField" style="display:none">
                                <div class="form-group">
                                    <label>Date</label>
                                    <div id="datepicker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addStepBtn"
                        onClick="addStep()">Submit<span id="addStepBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.stepCounter', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>

        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    {{-- {{!! JsValidator::formRequest('App\Http\Requests\StepCounterGoalRequest','#addStepForm') !!}} --}}
    <script>
        /**
         * Add Step.
         * @request form fields
         * @response object.
         */
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('addStepForm').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        });

        function addStep() {
            var formData = $("#addStepForm").serializeArray();
            $('#addStepBtn').prop('disabled', true);
            $('#addStepBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.stepCounter.saveUserGoalLog') }}",
                data: formData,
                success: function(response) {
                    $('#addStepBtn').prop('disabled', false);
                    $('#addStepBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addStepForm')[0].reset();
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('user.stepCounter', ['user_type' => $userType]) }}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addStepBtn').prop('disabled', false);
                    $('#addStepBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Please try again..');
                    }
                },
            });
        };

        function inputDate() {
            $("#inputDateField").show();
            $("#inputDatepickerLink").hide();
        }

        $(function() {
            var todayDate = new Date();
            todayDate = moment(todayDate).format('YYYY-MM-DD')
            console.log('TOday date', todayDate);
            $("#datepicker").datepicker({
                "dateFormat": "yy-mm-dd",
                "setDate": new Date(),
                onSelect: function(dateString, txtDate) {
                    if (dateString == todayDate) {
                        $("#takeInDate").text('How many steps did you take today ?');
                    } else if (dateString > todayDate) {
                        $("#takeInDate").text('How many steps will you take ' + moment(dateString)
                            .format('MMMM Do') + ' ?');
                    } else {
                        $("#takeInDate").text('How many steps did you take ' + moment(dateString)
                            .format('MMMM Do') + ' ?');
                    }
                    $("#selectedDateField").val(moment(dateString).format('YYYY-MM-DD'));
                    $("#inputDateField").hide();
                    $("#inputDatepickerLink").show();
                }
            });

            $("#selectedDateField").val(moment($("#datepicker").datepicker("getDate")).format('YYYY-MM-DD'));
        });
    </script>
@endsection
