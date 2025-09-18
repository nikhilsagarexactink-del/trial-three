@extends('layouts.app')
<title>Goals</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $userData = getUser();
        $currentDate = getLocalDateTime('', 'Y-m-d');
        $todayGoal = [];
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Goals</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Goals
                </h2>
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
                <a href="javascript:void(0)" onClick="showGoalModal()"
                    class="btn btn-secondary ripple-effect-dark text-white">
                    Set Goal
                </a>
            </div>
        </div>


        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="filterHead d-flex flex-column">
                <h3 class="h-24 font-semi"><span >Current Goal</span>{{ empty($myGoal) ? '- NA' : '' }}
                </h3>
                @if (!empty($myGoal))                  
                    <ul class="d-flex justify-content-between goal-list">
                        <li><b>Total Days: </b>{{ $myGoal['days'] }}</li>
                        <li><b>Total Workouts: </b>{{ $myGoal['workouts'] }}</li>
                        <li><b>Completed Workouts: </b><span id="completedWorkouts"></span></li>
                    </ul>
                @endif
            </div>
        </div>

        <section class="content white-bg">
            <div class="row justify-content-center">
                <div class="col-md-4 charts active" id="chartOneDiv">
                    <div class="chart-head">
                        <canvas class="chart__canvas" id="chartOne" width="160" height="160"
                            aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                        <p class="chart-num" id="percOne"></p>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="workoutGoalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Set Workout Goal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onClick="closeGoalModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="workoutGoalForm">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Select Workout:</label>
                                <select class="js-states form-control selectpicker" name="workout_goal_id"
                                    id="workoutGoalFieldId">
                                    <option value="">Select Goal</option>
                                    @foreach ($workouts as $workout)
                                        <option value="{{ $workout->id }}" {{ !empty($myGoal) && $myGoal['goal_id'] == $workout->id ? 'selected' : '' }}>
                                            Complete {{ $workout->workouts }} workout routines in the next
                                            {{ $workout->days }} days
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onClick="closeGoalModal()">Close</button>
                        <button type="button" class="btn btn-primary" id="addBtn" onClick="saveWorkoutGoal()">Add<span
                                id="addBtnLoader" class="spinner-border spinner-border-sm"
                                style="display: none;"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Game Modal -->
        @if(!empty($rewardDetail) && !empty($rewardDetail->reward_game) && $rewardDetail->is_gamification == 1)
            <x-game-modal :rewardDetail="$rewardDetail" :module="'workout-builder'" />
        @endif
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        function initializeChartOne(data = [0, 0], percOne = 0) {
            $ctxOnetext = $("#percOne");
            let canvasOne = document.getElementById('chartOne');

            let ctxOne = canvasOne.getContext('2d');
            let chartOne = new Chart(ctxOne, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            "#EF8E47",
                            "#e4e4e4"
                        ],
                        borderWidth: 0 // Width of border around the chart
                    }]
                },
                options: {
                    cutoutPercentage: 84,
                    responsive: true,
                    tooltips: {
                        enabled: false // Hide tooltips
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        onComplete: function() {
                            var cx = canvasOne.width / 2;
                            var cy = canvasOne.height / 2;
                            ctxOne.textAlign = 'center';
                            ctxOne.textBaseline = 'middle';
                            // ctxOne.font = '80px verdana';
                            ctxOne.font = '0px verdana';
                            ctxOne.fillStyle = '#EF8E47';
                            // ctxOne.fillText(percOne + "%", cx, cy);
                        }
                    },
                    onClick: function(e) {}
                }
            });

            $ctxOnetext.html('');
            $ctxOnetext.append(percOne + "%");
        }

        function showGoalModal() {
            $('#workoutGoalForm')[0].reset();
            $('#workoutGoalModal').modal('show');
        }

        function closeGoalModal() {
            $('#workoutGoalForm')[0].reset();
            $('#workoutGoalModal').modal('hide');
        }

        function saveWorkoutGoal() {
            var rewardData = @json($rewardDetail ?? null);
            let goalId = $("#workoutGoalFieldId").val();
            if (goalId) {
                $('#addBtn').prop('disabled', true);
                $('#addBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveWorkoutGoal') }}",
                    data: {
                        id: goalId
                    },
                    success: function(response) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        var previousData = response.data.previous_data;
                        if (response.success) {
                            _toast.success(response.message);
                            $('#workoutGoalModal').modal('hide');

                            setTimeout(function() {
                                if (rewardData && 
                                    rewardData.is_gamification == 1 &&
                                    rewardData.reward_game &&
                                    (!previousData || previousData.to_date < @json($currentDate))
                                ){

                                    const userId = @json($userData->id ?? null);
                                    const rewardId = rewardData.id;
                                    const modalId = '#gameModal_' + userId + '_' + rewardId;
                                    $(modalId).modal('show'); // updated here
                                }else{
                                    window.location.href =
                                    "{{ route('user.indexWorkoutGoal', ['user_type' => $userType]) }}";
                                }
                            }, 500);
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        _toast.error('Please try again.');
                    },
                });
            } else {
                _toast.error('Select workout goal');
            }
        }

        function getWorkoutGoalDetail(userWorkoutGoalId) {
            $.ajax({
                type: "GET",
                url: "{{ route('common.getWorkoutGoalDetail') }}",
                data: {},
                success: function(response) {
                    if (response.success) {
                        let workouts = response.data.workouts;
                        let workoutGoal = (response.data.userWorkoutGoal && response.data.userWorkoutGoal
                            .workout_goal) ? response.data.userWorkoutGoal.workout_goal : {};
                        let totalWorkouts = workoutGoal.workouts || 1;
                        let completedWorkouts = 0;
                        workouts.forEach((obj) => {
                            if (obj.is_completed == 1) {
                                completedWorkouts++;
                            }
                        })
                        $("#completedWorkouts").text(completedWorkouts);
                        let percentage = Math.round((completedWorkouts / totalWorkouts) * 100);
                        initializeChartOne([completedWorkouts, totalWorkouts], percentage);
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });

        }

        getWorkoutGoalDetail();
        //initializeChartOne([0, 1], 2);
    </script>
@endsection
