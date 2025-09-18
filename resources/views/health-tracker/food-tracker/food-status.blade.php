@php
    $userType = userType();
    $currentDate = date('Y-m-d');
    $userData = getUser();
    $activeTab = '';

    if($report && !empty($report['meal_content_status'])){
        if ($report['meal_content_status']->calories_status && $report['meal_content_status']->calories_status == 'enabled') {
        $activeTab = 'caloriesLog';  
        }else if ($report['meal_content_status']->carbohydrates_status && $report['meal_content_status']->carbohydrates_status == 'enabled') 
        {
            $activeTab = 'carbohydratesLog'; // Change to match tab ID
        } 
        elseif ($report['meal_content_status']->proteins_status &&  $report['meal_content_status']->proteins_status == 'enabled') {
                $activeTab = 'proteinsLog'; // Change to match tab ID
        }
    }
    
    
    
@endphp
@if($report['meal_content_status']->calories_status == 'enabled'||$report['meal_content_status']->carbohydrates_status == 'enabled'|| $report['meal_content_status']->proteins_status == 'enabled')
<section class="health-tab">
        <ul class="nav nav-tabs athlete-tab" style="margin:0;" id="myTab" role="tablist">
            @if($report['meal_content_status']->calories_status=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-calories-bold {{$activeTab=='caloriesLog' ? 'active' : ''}}" id="caloriesLog-tab" data-bs-toggle="tab" data-bs-target="#caloriesLog" type="button" role="tab" aria-controls="caloriesLog" aria-selected="false">Calories Intake</button>
            </li>
            @endif
            @if($report['meal_content_status']->carbohydrates_status=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-calories-bold {{$activeTab=='carbohydratesLog' ? 'active' : ''}}" id="carbohydratesLog-tab" data-bs-toggle="tab" data-bs-target="#carbohydratesLog" type="button" role="tab" aria-controls="carbohydratesLog" aria-selected="false">Carbs Intake</button>
            </li>
            @endif
            @if($report['meal_content_status']->proteins_status=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-calories-bold {{$activeTab=='proteinsLog' ? 'active' : ''}}" id="proteinsLog-tab" data-bs-toggle="tab" data-bs-target="#proteinsLog" type="button" role="tab" aria-controls="proteinsLog" aria-selected="true">Protein Intake</button>
            </li>
            @endif
        </ul>
</section>
<section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content" id="myTabContent">
        @if($report['meal_content_status']->calories_status=='enabled')
            <div class="tab-pane fade {{$activeTab=='caloriesLog' ? 'show active' : ''}}" id="caloriesLog" role="tabpanel" aria-labelledby="caloriesLog-tab">
                <div>
                    <!-- <label>Weight Log</label> -->
                    <canvas id="caloriesLineChart"></canvas>
                </div>
            </div>
        @endif
        @if($report['meal_content_status']->carbohydrates_status=='enabled')
        <div class=" tab-pane fade {{$activeTab=='carbohydratesLog' ? 'show active' : ''}}" id="carbohydratesLog" role="tabpanel" aria-labelledby="carbohydratesLog-tab">
            <div>
                <!-- <label>Body Fat Log</label> -->
                <canvas id="carbohydratesLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($report['meal_content_status']->proteins_status=='enabled')
        <div class=" tab-pane fade {{$activeTab=='proteinsLog' ? 'show active' : ''}}" id="proteinsLog" role="tabpanel" aria-labelledby="proteinsLog-tab">
            <div>
                <!-- <label>BMI Log</label> -->
                <canvas id="proteinsLineChart"></canvas>
            </div>
        </div>
        @endif
</section>
@endif
<script>
    $(document).ready(function() {
    var setting = @json($report['meal_content_status']);
    var allData = @json($report['userMeals']);
    var range = @json($range);
    var activeTab = @json($activeTab); // Get default active tab from PHP
    var dates = new Set();
    var startDate, endDate;
    var currentDate = moment(@json($currentDate));;

    // Set start and end dates based on the selected range
    if (range === "month") {
        startDate = currentDate.clone().subtract(1, 'month').format('YYYY-MM-DD');
    } else if (range === "week") {
        startDate = currentDate.clone().subtract(1, 'week').format('YYYY-MM-DD');
    } else {
        startDate = currentDate.clone().subtract(1, 'year').format('YYYY-MM-DD');
    }
    endDate = currentDate.clone().format('YYYY-MM-DD');

    // Generate date range for charts
    var currentDate = moment(startDate);
    while (currentDate.isSameOrBefore(endDate, 'day')) {    
        dates.add(currentDate.format('MM-DD-YYYY'));
        currentDate.add(1, 'day'); // Move to the next date **outside** the loop
    }
    dates = Array.from(dates);

    // Function to create a chart
    function createChart(chartId, label, borderColor, data) {
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

    // Function to initialize the correct chart based on activeTab
    function initializeChart(activeTab) {
        var data = [];
        var mealDate = '';
        
        if (setting.calories_status == "enabled" && activeTab === 'caloriesLog') {
            var caloriesData = [];
            allData.forEach((meal) => {
                let formattedDate = moment(meal.meal_date, "YYYY-MM-DD").format("MM-DD-YYYY"); // Convert to match dates array
                caloriesData.push({ mealCalories: meal.calories, mealDate: formattedDate });
            });

            var caloriesMap = {};
            // Initialize all dates in the map to 0
            dates.forEach(date => {
                caloriesMap[date] = 0;
            });

            // Sum up calories per date
            caloriesData.forEach(meal => {
                caloriesMap[meal.mealDate] += parseInt(meal.mealCalories || 0, 10);
            });

            data = Object.values(caloriesMap);

            createChart("caloriesLineChart", "Calories Intake", "rgb(255, 99, 132)", data);
        }
        if (setting.carbohydrates_status == "enabled" && activeTab === 'carbohydratesLog') {
            var carbsData = [];
            allData.forEach((meal) => {
                let formattedDate = moment(meal.meal_date, "YYYY-MM-DD").format("MM-DD-YYYY");
                carbsData.push({ mealCarbs: meal.carbohydrates, mealDate: formattedDate });
            });

            var carbsMap = {};
            dates.forEach(date => carbsMap[date] = 0);

            carbsData.forEach(meal => {
                carbsMap[meal.mealDate] += parseInt(meal.mealCarbs|| 0, 10);
            });

            data = Object.values(carbsMap);

            createChart("carbohydratesLineChart", "Carbs Intake", "rgb(54, 162, 235)", data);
        }
        if (setting.proteins_status == "enabled" && activeTab === 'proteinsLog') {
            var proteinsData = [];
            allData.forEach((meal) => {
                let formattedDate = moment(meal.meal_date, "YYYY-MM-DD").format("MM-DD-YYYY");
                proteinsData.push({ mealProteins: meal.proteins, mealDate: formattedDate });
            });

            var proteinsMap = {};
            dates.forEach(date => proteinsMap[date] = 0);

            proteinsData.forEach(meal => {
                proteinsMap[meal.mealDate] += parseInt(meal.mealProteins|| 0, 10);
            });

            data = Object.values(proteinsMap);

            createChart("proteinsLineChart", "Protein Intake", "rgb(75, 192, 192)", data);
        }
    }


    // Initialize chart for the default active tab on page load
    initializeChart(activeTab);

    // Handle tab change events
    $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (event) {
        activeTab = $(event.target).attr("id").replace("-tab", ""); // Extracting tab ID
        initializeChart(activeTab); // Load chart for the new active tab
    });
});

</script>