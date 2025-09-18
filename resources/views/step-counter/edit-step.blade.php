@extends('layouts.app')
@section('head')
    <title>Step Counter | Update Input</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $date = request()->query('date');
        $userType = userType();
    @endphp
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
                        <li class="breadcrumb-item active">Update Input</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Update Counter
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            <form id="updateStepForm" class="form-head" method="PUT" novalidate autocomplete="false"
                action="{{ route('common.stepCounter.updateUserGoalLog') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="mb-2">How many steps did you take <span id="drinkInDate">today</span>
                                        ?</label>
                                    <div class="water-input">
                                        <input type="text" class="form-control"
                                            value="{{ !empty($data['step_value']) ? $data['step_value'] : '' }}"
                                            name="step_value">
                                        <span><strong>steps</strong></span>
                                    </div>
                                    <!-- <span id="step_value-error" class="help-block error-help-block"></span> -->
                                    <input type="hidden" class="form-control" value="{{ $date }}" name="date">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateStepBtn"
                        onClick="updateStep()">Update<span id="updateStepBtnLoader"
                            class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.stepCounter', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>

        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <script>
        /**
         * Add Step.
         * @request form fields
         * @response object.
         */
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('updateStepForm').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
            var urlParams = new URLSearchParams(window.location.search);
            let date = urlParams.get('date');
            let currentDate = moment(new Date()).format('YYYY-MM-DD');
            if (currentDate != date) {
                $("#drinkInDate").text(moment(urlParams.get('date')).format('MMMM Do'));
            } else {
                $("#drinkInDate").text('today');
            }
        });

        function updateStep() {
            var formData = $("#updateStepForm").serializeArray();
            $('#updateStepBtn').prop('disabled', true);
            $('#updateStepBtnLoader').show();
            $.ajax({
                type: "PUT",
                url: "{{ route('common.stepCounter.updateUserGoalLog') }}",
                data: formData,
                success: function(response) {
                    $('#updateStepBtn').prop('disabled', false);
                    $('#updateStepBtnLoader').hide();
                    if (response.success) {
                        $('#updateStepForm')[0].reset();
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('user.stepCounter', ['user_type' => $userType]) }}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#updateStepBtn').prop('disabled', false);
                    $('#updateStepBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                        _toast.error(errors.message);
                    } else {
                        _toast.error('Please try again..');
                    }
                },
            });
        };
    </script>
@endsection
