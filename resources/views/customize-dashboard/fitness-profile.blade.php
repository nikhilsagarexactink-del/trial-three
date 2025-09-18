@php
    $userData = getUser();
    $userType = userType();  

@endphp

<div class="col-md-6 mainWidget_" data-id="{{ $widget_key }}" 
    @if($isCustomize)
        onmouseenter="showRemoveButton({{ json_encode($widget_key) }})"
        onmouseleave="hideRemoveButton({{ json_encode($widget_key) }})"
     @endif>
    @if($isCustomize)
        <button class="remove-widget-btn" id="remove-btn-{{ $widget_key }}" 
                onclick="removeWidget(event, {{ json_encode($widget_key) }})" 
                style="display: none;">&times;
        </button>
    @endif


                        <h4><a class="text-dark" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.fitnessProfile', ['user_type' => $userType]) }}">Fitness Activity</a>
                        </h4>
                        <div id="fitnessProfileSection" class="bg-white mb-4 p-4 dash-chart cursor-pointer equal-height">
                            <div class="row align-items-unset">
                                <div class="col-md-4 charts fitness-chart mb-0" id="chartOneDiv">
                                    <h6 class="text-center">TODAY</h6>
                                    <div class="chart-wrap">
                                        <canvas class="chart__canvas" id="chartOne" width="160" height="160"
                                            aria-label="Example doughnut chart showing data as a percentage"
                                            role="img"></canvas>
                                        <p class="chart-num" id="percOne"></p>
                                    </div>
                                    <p class="text-center" id="chartOneWorkOuts"></p>
                                    <p class="text-center" id="chartOneTotalTimes"></p>
                                </div>
                                <div class="col-md-4 charts fitness-chart mb-0" id="chartTwoDiv">
                                    <h6 class="text-center">THIS WEEK</h6>
                                    <div class="chart-wrap">
                                        <canvas class="chart__canvas" id="chartTwo" width="160" height="160"
                                            aria-label="Example doughnut chart showing data as a percentage"
                                            role="img"></canvas>
                                        <p class="chart-num" id="percTwo"></p>
                                    </div>
                                    <p class="text-center" id="chartTwoWorkOuts"></p>
                                    <p class="text-center" id="chartTwoTotalTimes"></p>
                                </div>
                                <div class="col-md-4 charts fitness-chart mb-0" id="chartThreeDiv">
                                    <h6 class="text-center">THIS MONTH</h6>
                                    <div class="chart-wrap">
                                        <canvas class="chart__canvas" id="chartThree" width="160" height="160"
                                            aria-label="Example doughnut chart showing data as a percentage"
                                            role="img"></canvas>
                                        <p class="chart-num" id="percThree"></p>
                                    </div>
                                    <p class="text-center" id="chartThreeWorkOuts"></p>
                                    <p class="text-center" id="chartThreeTotalTimes"></p>
                                </div>
                            </div>
                        </div>
                
</div>



<script>

    var chartOne, chartTwo, chartThree;
    var athlete = @json($athlete);
    $(document).ready(function () {
        
                    initializeChartOne();
                    initializeChartTwo();
                    initializeChartThree();
                    loadTodayWorkOutReport();
               
    });

    document.getElementById("fitnessProfileSection").addEventListener("click", function() {
        @if($userType == 'parent' && !empty($athlete))
            // Do nothing
        @else
            window.location.href = "{{ route('user.fitnessProfile', ['user_type' => $userType]) }}";
        @endif
    });

    function loadTodayWorkOutReport() {
        
                                let data = @json($results);
                               let chartOneTotalWorkout = data.today.total_workouts - data.today.completed_workouts;
                                chartOneTotalWorkout = (data.today.total_workouts > 0) ? chartOneTotalWorkout : 1;
                                let chartOneData = [data.today.completed_workouts, chartOneTotalWorkout];
                                let chartOnePercent = data.today.completed_workouts ? ((data.today.completed_workouts *
                                    100) / data.today.total_workouts).toFixed(0) : 0;
                                initializeChartOne(chartOneData, chartOnePercent);
                                $("#chartOneWorkOuts").text(data.today.completed_workouts + `/` + data.today
                                    .total_workouts);
                                $("#chartOneTotalTimes").text(data.today.time.hours + ' hours ' + data.today.time
                                    .minutes + ' min ' + data.today.time.seconds + ' sec today');
                                //chart two
                                let chartTwoTotalWorkout = data.thisWeek.total_workouts - data.thisWeek
                                    .completed_workouts;
                                chartTwoTotalWorkout = (data.thisWeek.total_workouts > 0) ? chartTwoTotalWorkout : 1;
                                let chartTwoData = [data.thisWeek.completed_workouts, chartTwoTotalWorkout];
                                let chartTwoPercent = data.thisWeek.completed_workouts ? ((data.thisWeek
                                    .completed_workouts * 100) / data.thisWeek.total_workouts).toFixed(0) : 0;
                                initializeChartTwo(chartTwoData, chartTwoPercent);
                                $("#chartTwoWorkOuts").text(data.thisWeek.completed_workouts + `/` + data.thisWeek
                                    .total_workouts);
                                $("#chartTwoTotalTimes").text(data.thisWeek.time.hours + ' hours ' + data.thisWeek.time
                                    .minutes + ' min ' + data.thisWeek.time.seconds + ' sec this week');

                                //chart three
                                let chartThreeTotalWorkout = data.thisMonth.total_workouts - data.thisMonth.completed_workouts;
                                chartThreeTotalWorkout = (data.thisMonth.total_workouts > 0) ? chartThreeTotalWorkout : 1;
                                let chartThreeData = [data.thisMonth.completed_workouts, chartThreeTotalWorkout];
                                let chartThreePercent = data.thisMonth.completed_workouts ? ((data.thisMonth.completed_workouts * 100) / data.thisMonth.total_workouts).toFixed(0) : 0;
                                initializeChartThree(chartThreeData, chartThreePercent);
                                $("#chartThreeWorkOuts").text(data.thisMonth.completed_workouts + `/` + data.thisMonth.total_workouts);
                                $("#chartThreeTotalTimes").text(data.thisMonth.time.hours + ' hours ' + data.thisMonth.time.minutes + ' min ' + data.thisMonth.time.seconds + ' sec this month');
                        
                }

                function initializeChartOne(data = [0, 0], percOne = 0) {
                $ctxOnetext = $("#percOne");
                 let canvasOne = document.getElementById('chartOne');
                let ctxOne = canvasOne.getContext('2d');
                    if (chartOne) {
                        chartOne.destroy();
                    }
                
                 chartOne = new Chart(ctxOne, {
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
                                ctxOne.font = '0px verdana';
                                // ctxOne.fillStyle = 'black';
                                ctxOne.fillStyle = '#EF8E47';
                                // ctxOne.fillText(percOne + "%", cx, cy);
                            }
                        },
                        onClick: function(e) {
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
                if (chartTwo) {
                        chartTwo.destroy();
                    }
                chartTwo = new Chart(ctxTwo, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                "#EF8E47",
                                "#e4e4e4"
                            ],
                            // hoverBorderColor: 'red',
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
                                ctxTwo.font = '0px verdana';
                                // ctxTwo.fillStyle = 'black';
                                ctxTwo.fillStyle = '#EF8E47';
                                // ctxTwo.fillText(percTwo + "%", cx, cy);
                            }
                        },
                        onClick: function(e) {
                        }
                    }
                });
                $ctxTwotext.html('');
                $ctxTwotext.append(percTwo + "%");
            }

            function initializeChartThree(data = [0, 0], percThree = 0) {
                $ctxThreeText = $("#percThree");
                let canvasThree = document.getElementById('chartThree');
                let ctxThree = canvasThree.getContext('2d');
                if (chartThree) {
                        chartThree.destroy();
                    }
                chartThree =  new Chart(ctxThree, {
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
                                ctxThree.font = '0 verdana';
                                // ctxThree.fillStyle = 'black';
                                ctxThree.fillStyle = '#EF8E47';
                                // ctxThree.fillText(percThree + "%", cx, cy);
                            }
                        },
                        onClick: function(e) {
                        }
                    }
                });
                $ctxThreeText.html('');
                $ctxThreeText.append(percThree + "%");
            }

               
                            
                function redirectTo(redirectTo) {
                    window.location.href = redirectTo;
                }

</script>
