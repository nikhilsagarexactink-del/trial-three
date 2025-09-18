@extends('layouts.app')
@section('head')
    <title>Water Tracker | Update Input</title>
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
                                href="{{ route('user.waterTracker', ['user_type' => $userType]) }}">Water Tracker</a></li>
                        <li class="breadcrumb-item active">Update Input</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Update Tracking
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            <form id="updateWaterForm" class="form-head" method="PUT" novalidate autocomplete="false"
                action="{{ route('common.waterTracker.updateUserGoalLog') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="mb-2">How much water did you drink <span id="drinkInDate">today</span>
                                        ?</label>
                                    <div class="water-input">
                                        <input type="text" class="form-control"
                                            value="{{ !empty($data['water_value']) ? $data['water_value'] : '' }}"
                                            name="water_value">
                                        <span><strong>ounces</strong></span>
                                    </div>
                                    <!-- <span id="water_value-error" class="help-block error-help-block"></span> -->
                                    <input type="hidden" class="form-control" value="{{ $date }}" name="date">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateWaterBtn"
                        onClick="updateWater()">Update<span id="updateWaterBtnLoader"
                            class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.waterTracker', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>

        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <script>
        /**
         * Add Water.
         * @request form fields
         * @response object.
         */
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('updateWaterForm').addEventListener('keydown', function(event) {
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

        function updateWater() {
            var formData = $("#updateWaterForm").serializeArray();
            $('#updateWaterBtn').prop('disabled', true);
            $('#updateWaterBtnLoader').show();
            $.ajax({
                type: "PUT",
                url: "{{ route('common.waterTracker.updateUserGoalLog') }}",
                data: formData,
                success: function(response) {
                    $('#updateWaterBtn').prop('disabled', false);
                    $('#updateWaterBtnLoader').hide();
                    if (response.success) {
                        $('#updateWaterForm')[0].reset();
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('user.waterTracker', ['user_type' => $userType]) }}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#updateWaterBtn').prop('disabled', false);
                    $('#updateWaterBtnLoader').hide();
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
