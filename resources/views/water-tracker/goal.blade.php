@extends('layouts.app')
@section('head')
    <title>Water Tracker | Add Water</title>
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
                                href="{{ route('user.waterTracker', ['user_type' => $userType]) }}">Water Tracker</a></li>
                        <li class="breadcrumb-item active">Add Input</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Water Tracking
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="addWaterForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.waterTracker.saveGoal') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="mb-2">WHAT DO YOU WANT TO SET YOUR DAILY WATER INTAKE GOAL TO BE
                                        ?</label>
                                    <div class="water-input">
                                        <input type="text" class="form-control" placeholder="" name="goal"
                                            value="{{ @!empty($goal) ? $goal->goal : 0 }}">
                                        <span><strong>ounces</strong></span>
                                    </div>
                                    <span id="goal-error" class="help-block error-help-block text-danger"></span>
                                </div>
                                <a href="javascript:void(0)" onClick="getGoalInfo()">LEARN MORE ABOUT SETTING YOUR
                                    GOAL</a><br>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addWaterBtn"
                        onClick="addWater()">SAVE<span id="addWaterBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.waterTracker', ['user_type' => $userType]) }}">CANCEL</a>
                </div>
            </form>

        </section>

        <div class="modal fade" id="waterTrackerInfoModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Water Tracker Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onClick="closeExerciseModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                @if (!empty($settings['water-tracker-description']))
                                    {!! $settings['water-tracker-description'] !!}
                                @else
                                    <p>No record found.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            document.getElementById('addWaterForm').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        });

        function addWater() {
            var formData = $("#addWaterForm").serializeArray();
            $('#addWaterBtn').prop('disabled', true);
            $('#addWaterBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.waterTracker.saveGoal') }}",
                data: formData,
                success: function(response) {
                    $('#addWaterBtn').prop('disabled', false);
                    $('#addWaterBtnLoader').hide();
                    if (response.success) {
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
                    $('#addWaterBtn').prop('disabled', false);
                    $('#addWaterBtnLoader').hide();
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

        function getGoalInfo() {
            $('#waterTrackerInfoModal').modal('show');
        }

        function closeExerciseModal() {
            $('#waterTrackerInfoModal').modal('hide');
        }

        $(function() {
            $("#datepicker").datepicker({
                "dateFormat": "yyyy-mm-dd",
                "setDate": new Date()
            });

            $("#selectedDateField").val(moment($("#datepicker").datepicker("getDate")).format('YYYY-MM-DD'));
        });
    </script>
@endsection
