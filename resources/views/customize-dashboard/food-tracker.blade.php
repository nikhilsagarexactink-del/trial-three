@php
    $userData = getUser();
    $userType = userType(); 
    $activeTab = ''; 
    if($mealContentStatus->calories_status != 'enabled' && $mealContentStatus->carbohydrates_status != 'enabled' && $mealContentStatus->proteins_status != 'enabled'){
        $activeTab = 'defaultCaloriesLog';
    }

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
    <h4><a class="text-dark" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.foodTracker', ['user_type' => $userType]) }}">Food Tracker</a></h4>
    <div id="foodTrackerSection" class="bg-white mb-4 p-4 dash-chart cursor-pointer equal-height">
        @if($mealContentStatus->calories_status == 'enabled' || $mealContentStatus->carbohydrates_status == 'enabled' || $mealContentStatus->proteins_status == 'enabled')
            <div class="" id="widget-food-status"></div>
        @else
            <section class="health-tab">
                <ul class="nav nav-tabs athlete-tab" style="margin:0;" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link top-radius font-calories-bold {{$activeTab=='defaultCaloriesLog' ? 'active' : ''}}" id="defaultCaloriesLog-tab" data-bs-toggle="tab" data-bs-target="#defaultCaloriesLog" type="button" role="tab" aria-controls="defaultCaloriesLog" aria-selected="false">Calories Intake</button>
                    </li>
                </ul>
            </section>
            <section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content" id="myTabContent">
                <div class="tab-pane fade {{$activeTab=='defaultCaloriesLog' ? 'show active' : ''}}" id="defaultCaloriesLog" role="tabpanel" aria-labelledby="defaultCaloriesLog-tab">
                    <div>
                        <!-- <label>Weight Log</label> -->
                        <canvas id="caloriesLineChart"></canvas>
                    </div>
                </div>   
            </section>
            <p class="text-danger px-5">
                To track your total meal content please enable meal content status (calories,carbs,protein)
            </p>
        @endif
    </div>
</div>
<script>
var range = 'week';
var activeTab = @json($activeTab);
var setting = @json($mealContentStatus);
var dates = new Set();
    var defaultStartDate, defaultEndDate;

    // Set start and end dates based on the selected range
    document.getElementById("foodTrackerSection").addEventListener("click", function() {
        @if($userType == 'parent' && !empty($athlete))
            // Do nothing
        @else
            window.location.href = "{{ route('user.foodTracker', ['user_type' => $userType]) }}";
        @endif
    });
    
    defaultStartDate = moment().subtract(1, 'week').format('YYYY-MM-DD');
    defaultEndDate = moment().format('YYYY-MM-DD');

    // Generate date range for charts
    var defaultCurrentDate = moment(defaultStartDate);
    while (defaultCurrentDate.isSameOrBefore(defaultEndDate, 'day')) {    
        dates.add(defaultCurrentDate.format('MM-DD-YYYY'));
        defaultCurrentDate.add(1, 'day'); // Move to the next date **outside** the loop
    }
    dates = Array.from(dates);

     $(document).ready(function() {
        if(setting && setting.calories_status === 'enabled' || setting.carbohydrates_status === 'enabled' || setting.proteins_status === 'enabled'){
            displayUserFoodStatus();
        }else{
            // Initialize chart for the default active tab on page load
            initializeDefaultChart(activeTab);
        }


            // Handle tab change events
            $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (event) {
                activeTab = $(event.target).attr("id").replace("-tab", ""); // Extracting tab ID
                initializeChart(activeTab); // Load chart for the new active tab
            });

            
       
    });

    function initializeDefaultChart(activeTab) {
        var data = [];
        var mealDate = '';
        createDefaultChart("caloriesLineChart", "Calories Intake", "rgb(255, 99, 132)", data);          
    }
    function displayUserFoodStatus(){
        $("#widget-food-status").html('<div class="text-center">{{ ajaxListLoader() }}</div>');

        let url = "{{ route('common.foodTracker.foodStatus') }}";
        

        $.ajax({
            type: "GET",
            url: url,
            data: {
                'range' : range,
                'athlete_id' : @json($athlete_id)?@json($athlete_id):"",
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.data);
                    $("#widget-food-status").html(response.data.html);
                }
            },
            error: function() {
                _toast.error('Something went wrong.');
            }
        });
    }
    function createDefaultChart(chartId, label, borderColor, data) {
                var ctx = document.getElementById(chartId);
                if (!ctx) return; // Prevent errors if the canvas doesn't exist

                new Chart(ctx.getContext("2d"), {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: label,
                            data: data,
                            fill: false,
                            borderColor: borderColor,
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: false
                            }
                        }
                    }
                });
            }

</script>