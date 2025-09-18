@extends('layouts.app')
@section('head')
    <title>Water Tracker | Add Input</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php 
    $userType = userType(); 
    $userData = getUser();
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
                action="{{ route('common.waterTracker.saveUserGoalLog') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="mb-2"><span id="drinkInDate">How much water did you drink today
                                            ?</span></label>
                                    <div class="water-input">
                                        <input type="text" class="form-control" placeholder="" name="water_value">
                                        <span><strong>ounces</strong></span>
                                    </div>
                                    <span id="water_value-error" class="help-block error-help-block text-danger"></span>

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
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addWaterBtn"
                        onClick="addWater()">Submit<span id="addWaterBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.waterTracker', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
            <!-- Modal -->
            @if(!empty($rewardDetail) && !empty($rewardDetail->reward_game) && $rewardDetail->is_gamification == 1)
               <x-game-modal :rewardDetail="$rewardDetail" :module="'water-tracker'" :module-id="($waterGoal->id ?? 0)" />
            @endif


        </section>

        
    <!-- Main Content Start -->
@endsection

@section('js')
    {{-- {{!! JsValidator::formRequest('App\Http\Requests\WaterTrackerGoalLogRequest','#addWaterForm') !!}} --}}
    <script>
        /**
         * Add Water.
         * @request form fields
         * @response object.
         */
        let rewardData = @json($rewardDetail ?? null);

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
                url: "{{ route('common.waterTracker.saveUserGoalLog') }}",
                data: formData,
                success: function(response) {
                    $('#addWaterBtn').prop('disabled', false);
                    $('#addWaterBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addWaterForm')[0].reset();
                        setTimeout(function() {
                            if(rewardData && rewardData.is_gamification == 1 && rewardData.reward_game){
                                const userId = @json($userData->id);
                                const rewardId = rewardData.id;
                                const modalId = '#gameModal_' + userId + '_' + rewardId;
                                $(modalId).modal('show'); // updated here
                            }else{
                                window.location.href = "{{ route('user.waterTracker', ['user_type' => $userType]) }}";
                            }
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

        $(function() {
            var todayDate = new Date();
            todayDate = moment(todayDate).format('YYYY-MM-DD')
            console.log('TOday date', todayDate);
            $("#datepicker").datepicker({
                "dateFormat": "yy-mm-dd",
                "setDate": new Date(),
                onSelect: function(dateString, txtDate) {
                    if (dateString == todayDate) {
                        $("#drinkInDate").text('How much water did you drink today ?');
                    } else if (dateString > todayDate) {
                        $("#drinkInDate").text('How much water will you drink ' + moment(dateString)
                            .format('MMMM Do') + ' ?');
                    } else {
                        $("#drinkInDate").text('How much water did you drink ' + moment(dateString)
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
<style>
    .modal-body {
        overflow: visible !important;
    }
</style>
