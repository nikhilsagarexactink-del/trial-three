@extends('layouts.app')
@section('head')
<title>Health Tracker | Health Marker</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php 
    $userType = userType(); 
    $activeTab = '';
    foreach($settings as $key=>$setting){
        if(empty($activeTab) && $settings[$key]=='enabled'){
            $activeTab = $key;
        }
    }
    $currentDate = getTodayDate('Y-m-d');
    if(empty($activeTab)){
        $activeTab = 'health_marker_images_status';
    }
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard', ['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.healthTracker', ['user_type'=>$userType])}}">Health Tracker</a></li>
                    <li class="breadcrumb-item active">Health Marker</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-3">
                Health Marker
            </h2>

            <section class="my-2 row">
                <div class="col-md-8">
                    <select class="form-select" onchange="changeMarkerRange(event)" name="range" id="marker-range">
                        <option value="week">Weekly</option>
                        <option value="month">Monthly</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                    
            </section>
            <div class="row" id="markerDateRange">
                <div class="col-md-8" id="dateRangePickerDivId">
                    <div class="form-group">
                        <input id="daterangepicker" type="text" class="form-control text-center" placeholder="Date Range" name="date_range">
                    </div>
                </div>
            </div>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="health-tab">
        <ul class="nav nav-tabs athlete-tab" style="margin:0;" id="myTab" role="tablist">
            @if($settings['weight_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-weight-bold {{$activeTab=='weight_status' ? 'active' : ''}}" id="weightLog-tab" data-bs-toggle="tab" data-bs-target="#weightLog" type="button" role="tab" aria-controls="weightLog" aria-selected="false">Weight Log</button>
            </li>
            @endif
            @if($settings['body_fat_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-weight-bold {{$activeTab=='body_fat_status' ? 'active' : ''}}" id="BodyFatLog-tab" data-bs-toggle="tab" data-bs-target="#BodyFatLog" type="button" role="tab" aria-controls="BodyFatLog" aria-selected="false">Body Fat Log </button>
            </li>
            @endif
            @if($settings['bmi_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-weight-bold {{$activeTab=='bmi_status' ? 'active' : ''}}" id="BMILog-tab" data-bs-toggle="tab" data-bs-target="#BMILog" type="button" role="tab" aria-controls="BMILog" aria-selected="true">BMI Log</button>
            </li>
            @endif
            @if($settings['body_water_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-weight-bold {{$activeTab=='body_water_status' ? 'active' : ''}}" id="BodyWaterLog-tab" data-bs-toggle="tab" data-bs-target="#BodyWaterLog" type="button" role="tab" aria-controls="BodyWaterLog" aria-selected="true">Body Water Log </button>
            </li>
            @endif
            @if($settings['skeletal_muscle_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-weight-bold {{$activeTab=='skeletal_muscle_status' ? 'active' : ''}}" id="SkeletalMuscleLog-tab" data-bs-toggle="tab" data-bs-target="#SkeletalMuscleLog" type="button" role="tab" aria-controls="SkeletalMuscleLog" aria-selected="true">Skeletal Muscle Log </button>
            </li>
            @endif

            @if($settings['health_marker_images_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-weight-bold {{$activeTab=='health_marker_images_status' ? 'active' : ''}}" id="Images-tab" data-bs-toggle="tab" data-bs-target="#Images" type="button" role="tab" aria-controls="Images" aria-selected="true">Images </button>
            </li>
            @endif
        </ul>
    </section>
    <section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content" id="myTabContent">
        @if($settings['weight_status']=='enabled')
            <div class="tab-pane fade {{$activeTab=='weight_status' ? 'show active' : ''}}" id="weightLog" role="tabpanel" aria-labelledby="weightLog-tab">
                <div>
                    <!-- <label>Weight Log</label> -->
                      <div  class="mb-2" id="goalMessage" > </div>
                    <canvas id="weightLineChart"></canvas>
                </div>
            </div>
        @endif
        @if($settings['body_fat_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='body_fat_status' ? 'show active' : ''}}" id="BodyFatLog" role="tabpanel" aria-labelledby="BodyFatLog-tab">
            <div>
                <!-- <label>Body Fat Log</label> -->
                <canvas id="bodyFatLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['bmi_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='bmi_status' ? 'show active' : ''}}" id="BMILog" role="tabpanel" aria-labelledby="BMILog-tab">
            <div>
                <!-- <label>BMI Log</label> -->
                <canvas id="bmiLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['body_water_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='body_water_status' ? 'show active' : ''}}" id="BodyWaterLog" role="tabpanel" aria-labelledby="BodyWaterLog-tab">
            <div>
                <!-- <label>Body Water Log</label> -->
                <canvas id="bodyWaterLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['skeletal_muscle_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='skeletal_muscle_status' ? 'show active' : ''}}" id="SkeletalMuscleLog" role="tabpanel" aria-labelledby="SkeletalMuscleLog-tab">
            <div>
                <!-- <label>Skeletal Muscle Log</label> -->
                <canvas id="skeletalMuscleLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['health_marker_images_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='health_marker_images_status' ? 'show active' : ''}}" id="Images" role="tabpanel" aria-labelledby="Images-tab">
            <div id="imagesDiv"></div>
        </div>
        @endif
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
<script>
    let healthSettings = @json($settings);
    let settingWeightUnit =  healthSettings && healthSettings.weight ? healthSettings.weight === 'KILOGRAM/KG' ? 'KG' : 'LBS' : '';
    let goal = @json($weightGoal);
    let goalType = goal.goal_type;
    let goalWeight = goal.weight_goal;
    let weightUnit = null ;
    let startDate = '';
    let endDate = '';
    let images = [];
    let imagesHtml = '';

    var dates = new Set();
    // var startDate, endDate;
    var currentDate = moment(@json($currentDate));
    var range = 'month';

    // Set start and end dates based on the selected range
   

    let weightXValues = [];
    let weightYValues = [];
    let weightChart = '';

    function changeMarkerRange(event){
        range = event.target.value;
        loadHealthMarkerLog();
    }


    if (healthSettings.weight_status === 'enabled') {
        var ctx = document.getElementById('weightLineChart').getContext("2d")
        weightChart = new Chart("weightLineChart", {
            type: "line",
            data: {
                labels: weightXValues, // ✅ X-Axis labels (dates)
                datasets: [
                    {
                        label: "Weight Progress", // ✅ Solid Blue Line
                        data: weightYValues,
                        borderColor: "rgb(246, 137, 34)",
                        backgroundColor: "rgba(0,0,255,0.1)",
                        borderWidth: 2,
                        fill: false,
                        tension: 0, // ✅ Corrected from `lineTension`
                        borderDash: [] // ✅ Solid line
                    },
                    {
                        label: `Goal Weight ${goalWeight}`, // ✅ Dashed Red Line
                        data: weightXValues.map(() => goalWeight), // ✅ Ensure full line on X-axis
                        borderColor: "red",
                        borderDash: [5, 5], // ✅ Dashed line effect
                        borderWidth: 2,
                        fill: false,
                        tension: 0
                    }
                ]
            },
            plugins: {
                    legend: {
                        display: true,
                        position: "top"
                    },
                    tooltip: {
                        enabled: true
                    }
                }
        });
    }

    let bodyFatXValues = [];
    let bodyFatYValues = [];
    let bodyFatChart = '';
    if(healthSettings.body_fat_status=='enabled'){
        bodyFatChart = new Chart("bodyFatLineChart", {
            type: "line",
            data: {
                labels: bodyFatXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,0.1)",
                    borderColor: "rgb(246, 137, 34)",
                    borderWidth: 2,
                    data: bodyFatYValues
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0
                        }
                    }],
                }
            }
        });
    }
    let bmiXValues = [];
    let bmiYValues = [];
    let bmiChart = '';
    if(healthSettings.bmi_status=='enabled'){
        bmiChart = new Chart("bmiLineChart", {
            type: "line",
            data: {
                labels: bmiXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:"rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    borderWidth: 2,
                    data: bmiYValues
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0
                        }
                    }],
                }
            }
        });
    }
    let bodyWaterXValues = [];
    let bodyWaterYValues = [];
    let bodyWaterChart = '';
    if(healthSettings.body_water_status=='enabled'){
        bodyWaterChart = new Chart("bodyWaterLineChart", {
            type: "line",
            data: {
                labels: bodyWaterXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:  "rgba(0,0,255,0.1)" ,
                    borderColor:"rgb(246, 137, 34)",
                    borderWidth: 2,
                    data: bodyWaterYValues
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0
                        }
                    }],
                }
            }
        });
    }
    let skeletalMuscleXValues = [];
    let skeletalMuscleYValues = [];
    let skeletalMuscleChart = '';
    if(healthSettings.skeletal_muscle_status=='enabled'){
        skeletalMuscleChart = new Chart("skeletalMuscleLineChart", {
            type: "line",
            data: {
                labels: skeletalMuscleXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,0.1)" ,
                    borderColor: "rgb(246, 137, 34)",
                    borderWidth: 2,
                    data: skeletalMuscleYValues
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0
                        }
                    }],
                }
            }
        });
    }
    function loadHealthMarkerLog() {
        imagesHtml = '';
        currentDate = moment(@json($currentDate));
        $('#markerDateRange').hide();
        $('#marker-range').val(range);
        if (range === "month") {
            startDate = currentDate.clone().startOf('month').format('YYYY-MM-DD');
            endDate = currentDate.clone().endOf('month').format('YYYY-MM-DD');

        } else if (range === "week") {
            startDate = currentDate.clone().startOf('week').format('YYYY-MM-DD'); // default is Sunday
            endDate = currentDate.clone().endOf('week').format('YYYY-MM-DD');

        } else{
            $('#markerDateRange').show();
           startDate = $('#daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
            endDate = $('#daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'); 
            
        }


        // Generate date range for charts
      

        
        $.ajax({
            type: "GET",
            url: "{{route('common.healthTracker.loadHealthMarkerLog')}}",
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    //Weight Chart
                    if(healthSettings.weight_status=='enabled'){
                        calculateLogs(data, 10, 'weight');
                    }
                    if(healthSettings.body_fat_status=='enabled'){
                        calculateLogs(data, 10, 'body_fat');
                    }
                    if(healthSettings.bmi_status=='enabled'){
                        calculateLogs(data, 10, 'bmi');
                    }
                    if(healthSettings.body_water_status=='enabled'){
                        calculateLogs(data, 10, 'body_water');
                    }
                    if(healthSettings.skeletal_muscle_status=='enabled'){
                        calculateLogs(data, 10, 'skeletal_muscle');
                    }
                    if(healthSettings.health_marker_images_status=='enabled'){  
                        //Append images
                        data.forEach((obj)=>{
                            obj.images.forEach((image)=>{   
                                let imageUrl = image.media ? image.media.base_url : '';                         
                                let date = moment(image.created_at).format('MM-DD-YYYY');                         
                                images.push({
                                    id: image.id,
                                    date: date,//moment().format('MM-DD-YYYY'),
                                    url: imageUrl
                                });
                                imagesHtml += `<li class="list-inline-item" >
                                                        <div class="uploaded-image-list">                            
                                                            <a href="javascript:void(0)" class="plan-link" data-lity data-lity-target="`+imageUrl+`">
                                                                <img style="height:50px;width:50px;" src="`+imageUrl+`" alt="">
                                                            </a>                        
                                                        </div>
                                                        <span>`+date+`</span>       
                                                    </li>`;
                            })                        
                                                
                        }); 
                        if(images.length) {
                            $('#imagesDiv').html(`<ul class="uploaded-image-list">`+imagesHtml+`</ul>`);
                        }else{
                            $('#imagesDiv').html(`<div class="alert alert-danger" role="alert">No Record Found.</div>`);
                        }
                        
                    }
                }
            }
        })
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

    function calculateLogs(data = [], days = 10, chart) {
    let currentDate = endDate; // moment().format('YYYY-MM-DD');
    let prevDate = startDate; // moment().subtract(days, 'd').format('YYYY-MM-DD');
    let dates = getDates(prevDate, currentDate);
    let chartXData = [];
    let chartYData = [];
    let currentWeight = null;

    // Initialize a map to hold the data for quick access
    let dataMap = {};
    data.forEach((obj) => {
        dataMap[obj.date] = obj; // Map the date to the data object
    });

    dates.forEach((date) => {
        let dayValue = dataMap[date]; // Get the data for the current date
        chartXData.push(moment(date).format('MM-DD-YYYY')); // Always push the date

        if (chart === 'weight' && dayValue && dayValue.weight != null) {
            currentWeight = parseFloat(dayValue.weight); // Ensure it's a number
            weightUnit = dayValue.weight_lbl === 'KILOGRAM/KG' ? 'KG' : 'LBS';
            chartYData.push(currentWeight); // Use parsed weight to avoid errors
        } else if (chart == 'body_fat' && dayValue && dayValue.body_fat != null) {
            chartYData.push(parseInt(dayValue.body_fat));
        } else if (chart == 'bmi' && dayValue && dayValue.bmi != null) {
            chartYData.push(parseInt(dayValue.bmi));
        } else if (chart == 'body_water' && dayValue && dayValue.body_water != null) {
            chartYData.push(parseInt(dayValue.body_water));
        } else if (chart == 'skeletal_muscle' && dayValue && dayValue.skeletal_muscle != null) {
            chartYData.push(parseInt(dayValue.skeletal_muscle));
        } else {
            chartYData.push(0); // Push null for missing data
        }
    });

    // Update the chart based on the collected data
    if (chart && chart == 'weight') {
        let goalMessageDiv = document.getElementById("goalMessage");
        goalMessageDiv.style.display = "none";
        let message = "";

        if (goalType === 'above' && currentWeight < goalWeight) {
            message = `<p class="weight-goal-msg"> You are just ${goalWeight - currentWeight} ${weightUnit} away from your goal. Keep it up!</p>`;
        } else if (goalType === 'below' && currentWeight > goalWeight) {
            message = `<p class="weight-goal-msg ">You are ${currentWeight - goalWeight} ${weightUnit} away from your goal. Keep going!</p>`;
        } else {
            message = '<p class="weight-goal-success">Congratulations! You\'ve reached your goal weight.</p>';
        }
        if (goal && goal.weight_goal) {
            if (goal && goal.weight_goal && message.trim() !== "" && currentWeight != null) {
                goalMessageDiv.innerHTML = message;
                goalMessageDiv.style.display = "block";
            } else {
                goalMessageDiv.style.display = "none";
            }
        }
        weightChart.data.labels = chartXData;  // Ensure X-axis labels update correctly
        weightChart.data.datasets[0].label = currentWeight != null ? `Weight Progress ${currentWeight}${weightUnit != null ? weightUnit : settingWeightUnit}` : 'Weight Progress';
        weightChart.data.datasets[0].data = chartYData;

        weightChart.data.datasets[1].label = goal.weight_goal != null ? `Goal Weight  ${goal.weight_goal}${weightUnit != null ? weightUnit : settingWeightUnit}` : 'Goal Weight';
        weightChart.data.datasets[1].data = Array(chartYData.length).fill(goal.weight_goal);

        weightChart.update();
    } else if (chart && chart == 'body_fat') {
        bodyFatChart.data.labels = chartXData;
        bodyFatChart.data.datasets[0].data = chartYData;
        bodyFatChart.update();
    } else if (chart && chart == 'bmi') {
        bmiChart.data.labels = chartXData;
        bmiChart.data.datasets[0].data = chartYData;
        bmiChart.update();
    } else if (chart && chart == 'body_water') {
        bodyWaterChart.data.labels = chartXData;
        bodyWaterChart.data.datasets[0].data = chartYData;
        bodyWaterChart.update();
    } else if (chart && chart == 'skeletal_muscle') {
        skeletalMuscleChart.data.labels = chartXData;
        skeletalMuscleChart.data.datasets[0].data = chartYData;
        skeletalMuscleChart.update();
    }
}


    $(function() {
        let healthMarker = @json($settings);   
        if(healthMarker.log_marker == "MONTHLY"){
            startDate = moment().startOf('month');
            endDate = moment().endOf('month');
        } else if(healthMarker.log_marker == "WEEKLY"){
            startDate = moment().startOf('week');
            endDate = moment().endOf('week');
        } else if(healthMarker.log_marker == "DAILY"){
            startDate = moment();
            endDate = moment();
        } else if(healthMarker.log_marker == "EVERY_OTHER_WEEK"){
            startDate = moment().add(1, 'weeks').startOf('week');
            endDate = moment().add(1, 'weeks').endOf('week');
        } else {
            startDate = moment().startOf('month');
            endDate = moment().endOf('month');
        }
        $("#daterangepicker").daterangepicker({
            startDate: startDate,
            endDate: endDate
        }).on("change", function() {
            loadHealthMarkerLog();
        });
        loadHealthMarkerLog();
    });
</script>
@endsection