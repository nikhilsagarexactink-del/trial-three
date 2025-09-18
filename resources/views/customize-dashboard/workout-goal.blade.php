@php
    $userType = userType();
    $currentDate = getLocalDateTime('', 'Y-m-d');
    $todayGoal = [];
@endphp
<div class="col-md-3 mainWidget_ "  data-id="{{ $widget_key }}"   
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
    <h4><a class="text-dark" href="{{ route('user.indexWorkoutGoal', ['user_type' => $userType]) }}">Workout Goal</a></h4>

    <section id="workoutGoalSection" class="content white-bg px-0 cursor-pointer" >
            
        <div class="row justify-content-center">
            <div class="col-md-10 mb-0 charts active" id="chartOneDiv_{{ $widget_key }}">
                <div class="chart-head">
                    <canvas class="chart__canvas" id="workoutGoalChartOne_{{ $widget_key }}" width="160" height="160"></canvas>
                    <p class="chart-num" id="percOne_{{ $widget_key }}"></p>
                </div>
            </div>
        </div>
        <div class="filterHead d-flex flex-column">
            @if (!empty($myGoal))   
            <div class="white-bg p-3 h-100">
                <ul class="workout-goal-card-list">
                    <li><b>Total Days: </b>{{ $myGoal['days'] }}</li>
                    <li><b>Total Workouts: </b>{{ $myGoal['workouts'] }}</li>
                    <li><b>Completed Workouts: </b><span id="completedWorkouts_{{  $widget_key  }}"></span></li>
                </ul>
            
            @else
            <div class="white-bg p-3 h-100">
                <ul class="workout-goal-card-list">
                    <li><b>You haven't set a goal yet</b></li>
                </ul> 
            </div>          
            @endif
        </div>
    </section>
</div>

<script>
    document.getElementById("workoutGoalSection").addEventListener("click", function() {
        @if($userType == 'parent' && !empty($athlete))
            // Do nothing
        @else
            window.location.href = "{{ route('user.indexWorkoutGoal', ['user_type' => $userType]) }}";
        @endif
    });
    
    var widgetKey = @json($widget_key);

    function initializeGoalChartOne(data = [0, 0], percOne = 0) {
        let canvasId = 'workoutGoalChartOne_' + widgetKey;
        let canvasOne = document.getElementById(canvasId);

        if (!canvasOne) {
            console.error("Canvas element not found for ID:", canvasId);
            return; // Exit if the canvas is not found
        }

        let ctxOne = canvasOne.getContext('2d');
        let chartOne = new Chart(ctxOne, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: data,
                    backgroundColor: ["#EF8E47", "#e4e4e4"],
                    borderWidth: 0
                }]
            },
            options: {
                cutoutPercentage: 84,
                responsive: true,
                tooltips: { enabled: false },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    onComplete: function() {
                        var cx = canvasOne.width;
                        var cy = canvasOne.height;
                        ctxOne.textAlign = 'center';
                        ctxOne.textBaseline = 'middle';
                        ctxOne.font = '0px verdana';
                        ctxOne.fillStyle = '#EF8E47';
                    }
                }
            }
        });

        let $ctxOnetext = $("#percOne_" + widgetKey);
        $ctxOnetext.html('');
        $ctxOnetext.append(percOne + "%");
    }

    function getWorkoutGoalDetail(widgetKey) {
        $.ajax({
            type: "GET",
            url: "{{ route('common.getWorkoutGoalDetail') }}",
            data: {
                'athlete_id' : @json($athlete_id)?@json($athlete_id):"",
            },
            success: function(response) {
                if (response.success) {
                    let workouts = response.data.workouts;
                    let workoutGoal = (response.data.userWorkoutGoal && response.data.userWorkoutGoal.workout_goal) ? response.data.userWorkoutGoal.workout_goal : {};
                    let totalWorkouts = workoutGoal.workouts || 1;
                    let completedWorkouts = 0;
                        workouts.forEach((obj) => {
                            if (obj.is_completed == 1) {
                                completedWorkouts++;
                            }
                        });
                    $("#completedWorkouts_" + widgetKey).text(completedWorkouts?completedWorkouts: "0");
                    let percentage = Math.round((completedWorkouts / totalWorkouts) * 100);
                    setTimeout(() => {
                        initializeGoalChartOne([completedWorkouts, totalWorkouts], percentage);
                    }, 300);
                    
                }
            },
            error: function() {
                _toast.error('Something went wrong.');
            }
        });
    }

    $(document).ready(function (){
        getWorkoutGoalDetail("{{ $widget_key }}");
    });
</script>