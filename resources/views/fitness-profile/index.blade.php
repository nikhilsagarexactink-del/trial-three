@extends('layouts.app')
<title>Fitness Profile</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userData = getUser();
        $userType = userType();
        $currentDate = getLocalDateTime('', 'Y-m-d');
        $date = date('Y-m-d');

    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper ">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb ">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Fitness Profile</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Fitness Dashboard
                </h2>
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0 d-flex flex-column align-items-end">
                <!-- <a href="{{ route('user.addSettingForm', ['user_type' => $userType]) }}" class="btn btn-outline-warning" id="addNewWorkOut">Add NEW WORKOUT</a> -->
                <span><a class="btn-setting"
                        href="{{ route('user.addSettingForm', ['user_type' => $userType]) }}">Settings</a> <i
                        class="fa fa-cog" aria-hidden="true"></i></span>
                <span><a class="btn-setting" id="workoutGoalBtn"
                        href="{{ route('user.indexWorkoutGoal', ['user_type' => $userType]) }}">Would you Like to Set up a
                        workout Goal? </a></span>
            </div>
        </div>
        <!-- <span><a class="btn-setting" href="{{ route('user.workout.userAdvancedWorkout', ['user_type' => $userType]) }}">Workout</a> </span> -->
        <!--Header Text start-->
        <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm"></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!-- Header text End -->
        <!-- filter section start -->
        <section>
            <div class="">
                <div id="workOutDivId" class="fitness-alert-text"></div>
            </div>
        </section>
        <div class="card fitness-box" id="chartDivBox">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="fitness-vt">
                            <h4>
                                My progress
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3 charts fitness-chart active" id="chartOneDiv">
                        <h3 class="text-center">TODAY</h3>
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="chartOne" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num fs-chart" id="percOne"></p>
                        </div>
                        <p class="text-center" id="chartOneWorkOuts"></p>
                        <p class="text-center" id="chartOneTotalTimes"></p>
                    </div>
                    <div class="col-md-3 charts fitness-chart" id="chartTwoDiv">
                        <h3 class="text-center">THIS WEEK</h3>
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="chartTwo" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num fs-chart" id="percTwo"></p>
                        </div>
                        <p class="text-center" id="chartTwoWorkOuts"></p>
                        <p class="text-center" id="chartTwoTotalTimes"></p>
                    </div>
                    <div class="col-md-3 charts fitness-chart" id="chartThreeDiv">
                        <h3 class="text-center">THIS MONTH</h3>
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="chartThree" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num fs-chart" id="percThree"></p>
                        </div>
                        <p class="text-center" id="chartThreeWorkOuts"></p>
                        <p class="text-center" id="chartThreeTotalTimes"></p>
                    </div>
                </div>
                <!-- </div> -->
            </div>
        </div>
        <div class="card fitness-box" id="chartDivBox">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-1">
                        <div class="fitness-vt">
                            <h4>
                                My Goal
                            </h4>
                        </div>
                    </div>

                    <div class="col-md-4 m-auto charts fitness-chart" id="chartTwoDiv">
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="goalChart" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num fs-chart" id="percGoal"></p>
                        </div>
                        <p class="text-center" id="chartWorkoutGoal">0/0</p>
                        <p class="text-center" id="chartGoalTimes">You haven't set a goal yet.</p>
                    </div>

                </div>
                <!-- </div> -->
            </div>
        </div>
        @if(!empty($rewardDetail) && !empty($rewardDetail->reward_game) && $rewardDetail->is_gamification == 1)
            <x-game-modal :rewardDetail="$rewardDetail" :module="'fitness-profile'" />
        @endif

    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <script>
        loadHeaderText('fitness-profile');
        let timeInterval = "";
        let rewardData = @json($rewardDetail??null);
        /**
         * Load Detail.
         * @response object.
         */
        function loadTodayWorkOutDetail() {
            $("#workOutDivId").html('{{ ajaxListLoader() }}');
            url = "{{ route('common.getTodayWorkOutDetail') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    date: @json($date)
                },
                success: function(response) {
                    if (response.success) {
                        $("#workOutDivId").html("");
                        $("#workOutDivId").append(response.data.html);
                        // if (response.data.nextDateArr && Object.keys(response.data.nextDateArr).length) {
                        //     let nextDate = moment(response.data.nextDateArr.date).format('dddd MMMM D');
                        //     $('#nextWorkoutDay').text('NEXT WORKOUT ' + nextDate.toUpperCase());
                        // }

                        if (response.data.isNewUser == 1) {
                            $("#chartDivBox").hide();
                        }
                        // if (response.data.workout.length && response.data.todayPendingWorkout > 0) {
                        //     $("#timerDiv").show();
                        // } else {
                        //     $("#timerDiv").hide();
                        // }
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }
        /**
         * Add Health Setting.
         * @request form fields
         * @response object.
         */
        function markAsComplete(data, isStop = true) {
            if (data && Object.keys(data).length) {
                let locale = {
                    OK: 'I Suppose',
                    CONFIRM: 'Complete',
                    CANCEL: 'Cancel'
                };
                bootbox.addLocale('custom', locale);
                if (isStop) {
                    stopTimer(); //Stope timer
                }

                bootbox.prompt({
                    title: 'Add Note.',
                    locale: 'custom',
                    inputType: 'textarea',
                    callback: function(note) {

                        if (note != null) {
                            $('.mark-complete').prop('disabled', true);
                            $('#markCompleteBtn' + data.id).prop('disabled', true);
                            $('#addBtnLoader' + data.id).show();
                            var fromTime = $('.from_time').text();
                            var toTime = $('.to_time').text();
                            var clockTime = $('.clock').text();
                            if (fromTime == "00:00") {
                                var currentDate = new Date();
                                fromTime = toTime = currentDate.getHours() + ":" + currentDate.getMinutes() +
                                    ":" + currentDate.getSeconds();
                            }
                            var url = "{{ route('common.markWorkOutComplete', ['id' => '%recordId%']) }}";
                            url = url.replace('%recordId%', data.id);
                            $.ajax({
                                type: "PUT",
                                url: url,
                                data: {
                                    date: moment().format('YYYY-MM-DD'),
                                    from_time: fromTime,
                                    to_time: toTime,
                                    completed_time: clockTime,
                                    note: note, //$('#note' + data.id).val(),
                                    exercise: data.value,
                                    duration: data.duration,
                                    feature_key: "complete-workout",
                                    module_id: data.id
                                },
                                success: function(response) {
                                    $('.mark-complete').prop('disabled', false);
                                    //$('#markCompleteBtn'+data.id).prop('disabled', false);
                                    //$('#addBtnLoader').hide();
                                    if (response.success) {
                                        $("#exerciseModal").modal("hide");
                                        resetTimer();
                                        _toast.customSuccess(response.message);
                                        setTimeout(function() {
                                            if(rewardData && rewardData.is_gamification == 1 && rewardData.reward_game){
                                                const userId = @json($userData->id);
                                                const rewardId = rewardData.id;
                                                const modalId = '#gameModal_' + userId + '_' + rewardId;
                                                $(modalId).modal('show'); // updated here
                                            }else{
                                                window.location.reload();
                                            }
                                        }, 500);
                                    } else {
                                        _toast.error('Somthing went wrong. please try again');
                                    }
                                },
                                error: function(err) {
                                    $('.mark-complete').prop('disabled', false);
                                    if (err.status === 422) {
                                        var errors = $.parseJSON(err.responseText);
                                        $.each(errors.errors, function(key, val) {
                                            $("#" + key + "-error").text(val);
                                        });
                                    } else {
                                        _toast.error('Please try again.');
                                    }
                                },
                            });
                        }
                    }
                });
            }

        };

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
                            // "#FCFBFE"
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
                            // ctxOne.font = '60px verdana';
                            ctxOne.font = '0 verdana';
                            // ctxOne.fillStyle = 'black';
                            ctxOne.fillStyle = '#EF8E47';
                            // ctxOne.fillText(percOne + "%", cx, cy);
                        }
                    },
                    onClick: function(e) {
                        console.log("============Chart Click===========");
                    }
                }
            });
            $ctxOnetext.html('');
            $ctxOnetext.append(percOne + "%");
        }

        function initializeChartTwo(data = [0, 0], percTwo = 0) {
            $ctxTwotext = $("#percTwo");
            let canvasTwo = document.getElementById('chartTwo');
            let ctxTwo = canvasTwo.getContext('2d');
            new Chart(ctxTwo, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            "#EF8E47",
                            "#e4e4e4"
                            // "#FCFBFE"
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
                            var cx = canvasTwo.width / 2;
                            var cy = canvasTwo.height / 2;
                            ctxTwo.textAlign = 'center';
                            ctxTwo.textBaseline = 'middle';
                            // ctxTwo.font = '60px verdana';
                            ctxTwo.font = '0 verdana';
                            // ctxTwo.fillStyle = 'black';
                            ctxTwo.fillStyle = '#EF8E47';
                            // ctxTwo.fillText(percTwo + "%", cx, cy);
                        }
                    },
                    onClick: function(e) {
                        console.log("============Chart Click===========");
                    }
                }
            });
            $ctxTwotext.html('');
            $ctxTwotext.append(percTwo + "%");
        }

        function initializeChartThree(data = [0, 0], percThree = 0) {
            $ctxThreetext = $("#percThree");
            let canvasThree = document.getElementById('chartThree');
            let ctxThree = canvasThree.getContext('2d');
            new Chart(ctxThree, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            "#EF8E47",
                            "#e4e4e4"
                            // "#FCFBFE"
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
                            var cx = canvasThree.width / 2;
                            var cy = canvasThree.height / 2;
                            ctxThree.textAlign = 'center';
                            ctxThree.textBaseline = 'middle';
                            // ctxThree.font = '60px verdana';
                            ctxThree.font = '0 verdana';
                            // ctxThree.fillStyle = 'black';
                            ctxThree.fillStyle = '#EF8E47';
                            // ctxThree.fillText(percThree + "%", cx, cy);
                        }
                    },
                    onClick: function(e) {
                        console.log("============Chart Click===========");
                    }
                }
            });
            $ctxThreetext.html('');
            $ctxThreetext.append(percThree + "%");
        }

        function initializeGoalChart(data = [0, 0], percOne = 0) {
            $ctxOnetext = $("#percGoal");
            let canvasOne = document.getElementById('goalChart');

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
        //Return Date Ranges
        function getDates(startDate, stopDate) {
            var dateArray = [];
            var currentDate = moment(startDate);
            var stopDate = moment(stopDate);
            while (currentDate <= stopDate) {
                dateArray.push(moment(currentDate).format('YYYY-MM-DD'))
                currentDate = moment(currentDate).add(1, 'days');
            }
            return dateArray;
        }

        /**
         * Load Detail.
         * @response object.
         */
        function loadTodayWorkOutReport() {
            //$("#workOutDivId").html('{{ ajaxListLoader() }}');
            url = "{{ route('common.getWorkOutReport') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    date: @json($date)
                },
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        //chart one
                        let todayTotalWorkOuts = data.today.total_workouts ? data.today.total_workouts : 1;
                        let chartOneData = [data.today.completed_workouts, (todayTotalWorkOuts - data.today
                            .completed_workouts)];
                        let chartOnePercent = data.today.completed_workouts ? ((data.today.completed_workouts *
                            100) / data.today.total_workouts).toFixed(0) : 0;
                        initializeChartOne(chartOneData, chartOnePercent);
                        $("#chartOneWorkOuts").text(data.today.completed_workouts + `/` + data.today
                            .total_workouts);
                        $("#chartOneTotalTimes").text(data.today.time.hours + ' hours ' + data.today.time
                            .minutes + ' min ' + data.today.time.seconds + ' sec today');
                        //chart two
                        let weekTotalWorkOuts = data.thisWeek.total_workouts ? data.thisWeek.total_workouts : 1;
                        let chartTwoData = [data.thisWeek.completed_workouts, (weekTotalWorkOuts - data.thisWeek
                            .completed_workouts)];
                        let chartTwoPercent = data.thisWeek.completed_workouts ? ((data.thisWeek
                            .completed_workouts * 100) / data.thisWeek.total_workouts).toFixed(0) : 0;
                        initializeChartTwo(chartTwoData, chartTwoPercent);
                        $("#chartTwoWorkOuts").text(data.thisWeek.completed_workouts + `/` + data.thisWeek
                            .total_workouts);
                        $("#chartTwoTotalTimes").text(data.thisWeek.time.hours + ' hours ' + data.thisWeek.time
                            .minutes + ' min ' + data.thisWeek.time.seconds + ' sec this week');
                        //chart three
                        let monthTotalWorkOuts = data.thisMonth.total_workouts ? data.thisMonth.total_workouts :
                            1;
                        let chartThreeData = [data.thisMonth.completed_workouts, (monthTotalWorkOuts - data
                            .thisMonth.completed_workouts)];
                        let chartThreePercent = data.thisMonth.completed_workouts ? ((data.thisMonth
                            .completed_workouts * 100) / data.thisMonth.total_workouts).toFixed(0) : 0;
                        initializeChartThree(chartThreeData, chartThreePercent);
                        $("#chartThreeWorkOuts").text(data.thisMonth.completed_workouts + `/` + data.thisMonth
                            .total_workouts);
                        $("#chartThreeTotalTimes").text(data.thisMonth.time.hours + ' hours ' + data.thisMonth
                            .time.minutes + ' min ' + data.thisMonth.time.seconds + ' sec this month');
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }

        function getWorkoutGoalDetail() {
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
                        if (workouts.length > 0) {
                            workouts.forEach((obj) => {
                                if (obj.is_completed == 1) {
                                    completedWorkouts++;
                                }
                            });
                        }
                        if (Object.keys(workoutGoal).length > 0) {
                            $("#chartGoalTimes").text("Complete " + workoutGoal.workouts +
                                " workout routines in the next " + workoutGoal.days + " days");
                            $("#chartWorkoutGoal").text(completedWorkouts + `/` + workoutGoal.workouts);
                            $("#workoutGoalBtn").html(
                                'Workout Goal Settings <i class="fa fa-cog" style="color: #000000;" aria-hidden="true"></i>'
                            );
                        }
                        let percentage = Math.round((completedWorkouts / totalWorkouts) * 100);
                        initializeGoalChart([completedWorkouts, totalWorkouts], percentage);
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });

        }

        clearTimeout(timeInterval)
        timeInterval = setTimeout(() => {
            loadTodayWorkOutDetail();
            loadTodayWorkOutReport();
        }, 1000);

        function showExerciseModal() {
            $('#exerciseModal').modal('show');
        }

        function closeExerciseModal() {
            $('#exerciseModal').modal('hide');
        }
        $(document).ready(function() {
            initializeChartOne();
            initializeChartTwo();
            initializeChartThree();

            // Load Goal Detail
            getWorkoutGoalDetail();
        });
    </script>
@endsection
<!-- <style>
    #calendar {
        max-width: 900px;
        margin: 0 auto;
    }
</style> -->
