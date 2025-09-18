@extends('layouts.app')
@section('head')
<title>Health Tracker | Health Measurement</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php 
    $userType = userType();
    $currentDate = getTodayDate('Y-m-d');
    $activeTab = '';
    $tabsStatus = ['height_status', 'neck_status', 'shoulder_status','chest_status','waist_status','abdomen_status','hip_status','bicep_left_status','bicep_right_status','thigh_left_status','thigh_right_status','calf_left_status','calf_right_status'];
    foreach($settings as $key=>$setting){
        //print_r($setting);die;
        if(in_array($key, $tabsStatus) && empty($activeTab) && $settings[$key]=='enabled'){
            $activeTab = $key;
        }
    }
    
    if(empty($activeTab)){
        $activeTab = 'health_measurement_images_status';
    }
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard', ['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.healthTracker', ['user_type'=>$userType])}}">Health Tracker</a></li>
                    <li class="breadcrumb-item active">Health Measurements</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-3">
                Health Measurements 
            </h2>
            <section class="my-2 row">
                <div class="col-md-8">
                    <select class="form-select" onchange="changeMeasurementRange(event)" name="range" id="measurement-range">
                        <option value="week">Weekly</option>
                        <option value="month">Monthly</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>      
            </section>
            <div class="row" id="measurementDateRange">
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
        <ul class="nav nav-tabs athlete-tab" id="myTab" role="tablist">
            @if($settings['height_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='height_status' ? 'active' : ''}}" id="heightLog-tab" data-bs-toggle="tab" data-bs-target="#heightLog" type="button" role="tab" aria-controls="heightLog" aria-selected="false">Height</button>
            </li>
            @endif
            @if($settings['neck_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='neck_status' ? 'active' : ''}}" id="neck-tab" data-bs-toggle="tab" data-bs-target="#neck" type="button" role="tab" aria-controls="neck" aria-selected="false">Neck</button>
            </li>
            @endif
            @if($settings['shoulder_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='shoulder_status' ? 'active' : ''}}" id="shoulder-tab" data-bs-toggle="tab" data-bs-target="#shoulder" type="button" role="tab" aria-controls="shoulder" aria-selected="true">Shoulder</button>
            </li>
            @endif
            @if($settings['chest_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='chest_status' ? 'active' : ''}}" id="chest-tab" data-bs-toggle="tab" data-bs-target="#chest" type="button" role="tab" aria-controls="chest" aria-selected="true">Chest</button>
            </li>
            @endif
            @if($settings['waist_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='waist_status' ? 'active' : ''}}" id="waist-tab" data-bs-toggle="tab" data-bs-target="#waist" type="button" role="tab" aria-controls="waist" aria-selected="true">Waist</button>
            </li>
            @endif
            @if($settings['abdomen_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='abdomen_status' ? 'active' : ''}}" id="abdomen-tab" data-bs-toggle="tab" data-bs-target="#abdomen" type="button" role="tab" aria-controls="abdomen" aria-selected="true">Abdomen</button>
            </li>
            @endif
            @if($settings['hip_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='hip_status' ? 'active' : ''}}" id="hip-tab" data-bs-toggle="tab" data-bs-target="#hip" type="button" role="tab" aria-controls="hip" aria-selected="true">HIP</button>
            </li>
            @endif
            @if($settings['bicep_left_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='bicep_left_status' ? 'active' : ''}}" id="bicepLeft-tab" data-bs-toggle="tab" data-bs-target="#bicepLeft" type="button" role="tab" aria-controls="bicepLeft" aria-selected="true">Bicep L</button>
            </li>
            @endif
            @if($settings['bicep_right_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='bicep_right_status' ? 'active' : ''}}" id="bicepRight-tab" data-bs-toggle="tab" data-bs-target="#bicepRight" type="button" role="tab" aria-controls="bicepRight" aria-selected="true">Bicep R</button>
            </li>
            @endif
            @if($settings['thigh_left_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='thigh_left_status' ? 'active' : ''}}" id="thighLeft-tab" data-bs-toggle="tab" data-bs-target="#thighLeft" type="button" role="tab" aria-controls="thighLeft" aria-selected="true">Thigh L</button>
            </li>
            @endif
            @if($settings['thigh_right_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='thigh_right_status' ? 'active' : ''}}" id="thighRight-tab" data-bs-toggle="tab" data-bs-target="#thighRight" type="button" role="tab" aria-controls="thighRight" aria-selected="true">Thigh R</button>
            </li>
            @endif
            @if($settings['calf_left_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='calf_left_status' ? 'active' : ''}}" id="calfLeft-tab" data-bs-toggle="tab" data-bs-target="#calfLeft" type="button" role="tab" aria-controls="calfLeft" aria-selected="true">Calf L</button>
            </li>
            @endif
            @if($settings['calf_right_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link  font-weight-bold {{$activeTab=='calf_right_status' ? 'active' : ''}}" id="calfRight-tab" data-bs-toggle="tab" data-bs-target="#calfRight" type="button" role="tab" aria-controls="calfRight" aria-selected="true">Calf R</button>
            </li>
            @endif

            @if($settings['health_measurement_images_status']=='enabled')
            <li class="nav-item" role="presentation">
                <button class="nav-link top-radius font-weight-bold {{$activeTab=='health_measurement_images_status' ? 'active' : ''}}" id="Images-tab" data-bs-toggle="tab" data-bs-target="#Images" type="button" role="tab" aria-controls="Images" aria-selected="true">Images </button>
            </li>
            @endif
        </ul>
    </section>
    <section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content" id="myTabContent">
        @if($settings['height_status']=='enabled')
        <div class="tab-pane fade {{$activeTab=='height_status' ? 'show active' : ''}}" id="heightLog" role="tabpanel" aria-labelledby="heightLog-tab">
            <div>
                <canvas id="heightLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['neck_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='neck_status' ? 'show active' : ''}}" id="neck" role="tabpanel" aria-labelledby="neck-tab">
            <div>
                <canvas id="neckLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['shoulder_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='shoulder_status' ? 'show active' : ''}}" id="shoulder" role="tabpanel" aria-labelledby="shoulder-tab">
            <div>
                <canvas id="shoulderLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['chest_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='chest_status' ? 'show active' : ''}}" id="chest" role="tabpanel" aria-labelledby="chest-tab">
            <div>
                <canvas id="chestLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['waist_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='waist_status' ? 'show active' : ''}}" id="waist" role="tabpanel" aria-labelledby="waist-tab">
            <div>
                <canvas id="waistLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['abdomen_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='abdomen_status' ? 'show active' : ''}}" id="abdomen" role="tabpanel" aria-labelledby="abdomen-tab">
            <div>
                <canvas id="abdomenLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['hip_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='hip_status' ? 'show active' : ''}}" id="hip" role="tabpanel" aria-labelledby="hip-tab">
            <div>
                <canvas id="hipLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['bicep_left_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='bicep_left_status' ? 'show active' : ''}}" id="bicepLeft" role="tabpanel" aria-labelledby="bicepLeft-tab">
            <div>
                <canvas id="bicepLeftLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['bicep_right_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='bicep_right_status' ? 'show active' : ''}}" id="bicepRight" role="tabpanel" aria-labelledby="bicepRight-tab">
            <div>
                <canvas id="bicepRightLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['thigh_left_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='thigh_left_status' ? 'show active' : ''}}" id="thighLeft" role="tabpanel" aria-labelledby="thighLeft-tab">
            <div>
                <canvas id="thighLeftLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['thigh_right_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='thigh_right_status' ? 'show active' : ''}}" id="thighRight" role="tabpanel" aria-labelledby="thighRight-tab">
            <div>
                <canvas id="thighRightLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['calf_left_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='calf_left_status' ? 'show active' : ''}}" id="calfLeft" role="tabpanel" aria-labelledby="calfLeft-tab">
            <div>
                <canvas id="calfLeftLineChart"></canvas>
            </div>
        </div>
        @endif
        @if($settings['calf_right_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='calf_right_status' ? 'show active' : ''}}" id="calfRight" role="tabpanel" aria-labelledby="calfRight-tab">
            <div>
                <canvas id="calfRightLineChart"></canvas>
            </div>
        </div>
        @endif

        @if($settings['health_measurement_images_status']=='enabled')
        <div class=" tab-pane fade {{$activeTab=='health_measurement_images_status' ? 'show active' : ''}}" id="Images" role="tabpanel" aria-labelledby="Images-tab">
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
    let startDate = '';
    let endDate = '';
    let images = [];
    let imagesHtml = '';
    let heightXValues = [];
    let heightYValues = [];
    let heightChart = '';
    let tooltipLabels = [];
    var range = 'month';
    var dates = new Set();
    // var startDate, endDate;
    var currentDate = moment(@json($currentDate));
    if(healthSettings.height_status=='enabled'){
        heightChart = new Chart("heightLineChart", {
            type: "line",
            data: {
                labels: heightXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:"rgba(0,0,255,0.1)" ,
                    borderColor: "rgb(246, 137, 34)",
                    data: heightYValues
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 3, // Set minimum value to 3 feet
                            max: 8, // Set maximum value to 8 feet
                            stepSize: 1,
                            callback: function(value) {
                                return value + ' ft'; // Append 'ft' to the labels
                            }
                        }
                    }],
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem) {
                            tooltipLabels.map((item,index)=>{
                                if(tooltipItem.index == index){
                                    tooltipItem.yLabel = item; 
                                }
                            });
                            return tooltipItem.yLabel + ' ft'; // Format to two decimal places
                        }
                    }
                }
            }
        });
    }


    function changeMeasurementRange(event){
        range = event.target.value;
        loadHealthMeasurementLog();
    }

    let neckXValues = [];
    let neckYValues = [];
    let neckChart = '';
    if(healthSettings.neck_status=='enabled'){
        neckChart = new Chart("neckLineChart", {
            type: "line",
            data: {
                labels: neckXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,0.1)" ,
                    borderColor: "rgb(246, 137, 34)",
                    data: neckYValues
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

    let shoulderXValues = [];
    let shoulderYValues = [];
    let shoulderChart = '';
    if(healthSettings.shoulder_status=='enabled'){
        shoulderChart = new Chart("shoulderLineChart", {
            type: "line",
            data: {
                labels: shoulderXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:"rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    data: shoulderYValues
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

    let chestXValues = [];
    let chestYValues = [];
    let chestChart = '';
    if(healthSettings.chest_status=='enabled'){
        chestChart = new Chart("chestLineChart", {
            type: "line",
            data: {
                labels: chestXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    data: chestYValues
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

    let waistXValues = [];
    let waistYValues = [];
    let waistChart = '';
    if(healthSettings.waist_status=='enabled'){
        waistChart = new Chart("waistLineChart", {
            type: "line",
            data: {
                labels: waistXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    data: waistYValues
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

    let abdomenXValues = [];
    let abdomenYValues = [];
    let abdomenChart = '';
    if(healthSettings.abdomen_status=='enabled'){
        abdomenChart = new Chart("abdomenLineChart", {
            type: "line",
            data: {
                labels: abdomenXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:  "rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    data: abdomenYValues
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

    let hipXValues = [];
    let hipYValues = [];
    let hipChart = '';
    if(healthSettings.hip_status=='enabled'){
        hipChart = new Chart("hipLineChart", {
            type: "line",
            data: {
                labels: hipXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:  "rgba(0,0,255,0.1)",
                    borderColor: "rgb(246, 137, 34)",
                    data: hipYValues
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

    let bicepLeftXValues = [];
    let bicepLeftYValues = [];
    let bicepLeftChart = '';
    if(healthSettings.bicep_left_status=='enabled'){
        bicepLeftChart = new Chart("bicepLeftLineChart", {
            type: "line",
            data: {
                labels: bicepLeftXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    data: bicepLeftYValues
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

    let bicepRightXValues = [];
    let bicepRightYValues = [];
    let bicepRightChart = '';
    if(healthSettings.bicep_right_status=='enabled'){
        bicepRightChart = new Chart("bicepRightLineChart", {
            type: "line",
            data: {
                labels: bicepRightXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:  "rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    data: bicepRightYValues
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

    let thighLeftXValues = [];
    let thighLeftYValues = [];
    let thighLeftChart = '';
    if(healthSettings.thigh_left_status=='enabled'){
        thighLeftChart = new Chart("thighLeftLineChart", {
            type: "line",
            data: {
                labels: thighLeftXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    data: thighLeftYValues
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

    let thighRightXValues = [];
    let thighRightYValues = [];
    let thighRightChart = '';
    if(healthSettings.thigh_right_status=='enabled'){
        thighRightChart = new Chart("thighRightLineChart", {
            type: "line",
            data: {
                labels: thighRightXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:  "rgba(0,0,255,0.1)",
                    borderColor: "rgb(246, 137, 34)",
                    data: thighRightYValues
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

    let calfLeftXValues = [];
    let calfLeftYValues = [];
    let calfLeftChart = '';
    if(healthSettings.calf_left_status=='enabled'){
        calfLeftChart = new Chart("calfLeftLineChart", {
            type: "line",
            data: {
                labels: calfLeftXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,0.1)",
                    borderColor:  "rgb(246, 137, 34)",
                    data: thighRightYValues
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

    let calfRightXValues = [];
    let calfRightYValues = [];
    let calfRightChart = '';
    if(healthSettings.calf_right_status=='enabled'){
        calfRightChart = new Chart("calfRightLineChart", {
            type: "line",
            data: {
                labels: calfRightXValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor:  "rgba(0,0,255,0.1)",
                    borderColor: "rgb(246, 137, 34)",
                    data: calfRightYValues
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

    function loadHealthMeasurementLog() {
        $('#measurementDateRange').hide();
        $('#measurement-range').val(range);
        imagesHtml = '';
        currentDate = moment(@json($currentDate));
        if (range === "month") {
            startDate = currentDate.clone().startOf('month').format('YYYY-MM-DD');
            endDate = currentDate.clone().endOf('month').format('YYYY-MM-DD');

        } else if (range === "week") {
            startDate = currentDate.clone().startOf('week').format('YYYY-MM-DD'); // default is Sunday
            endDate = currentDate.clone().endOf('week').format('YYYY-MM-DD');

        } else{
            $('#measurementDateRange').show();
           startDate = $('#daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
            endDate = $('#daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'); 
            
        }
        $.ajax({
            type: "GET",
            url: "{{route('common.healthTracker.loadHealthMeasurementLog')}}",
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    //Height Chart
                    if(healthSettings.height_status=='enabled'){
                        calculateLogs(data, 10, 'height');
                    }
                    if(healthSettings.neck_status=='enabled'){
                        calculateLogs(data, 10, 'neck');
                    }
                    if(healthSettings.shoulder_status=='enabled'){
                        calculateLogs(data, 10, 'shoulder');
                    }
                    if(healthSettings.chest_status=='enabled'){
                        calculateLogs(data, 10, 'chest');
                    }
                    if(healthSettings.waist_status=='enabled'){
                        calculateLogs(data, 10, 'waist');
                    }
                    if(healthSettings.abdomen_status=='enabled'){
                        calculateLogs(data, 10, 'abdomen');
                    }
                    if(healthSettings.hip_status=='enabled'){
                        calculateLogs(data, 10, 'hip');
                    }
                    if(healthSettings.bicep_left_status=='enabled'){
                        calculateLogs(data, 10, 'bicep_left');
                    }
                    if(healthSettings.bicep_right_status=='enabled'){
                        calculateLogs(data, 10, 'bicep_right');
                    }
                    if(healthSettings.thigh_left_status=='enabled'){
                        calculateLogs(data, 10, 'thigh_left');
                    }
                    if(healthSettings.thigh_right_status=='enabled'){
                        calculateLogs(data, 10, 'thigh_right');
                    }
                    if(healthSettings.calf_left_status=='enabled'){
                        calculateLogs(data, 10, 'calf_left');
                    }
                    if(healthSettings.calf_right_status=='enabled'){
                        calculateLogs(data, 10, 'calf_right');
                    }
                    if(healthSettings.health_measurement_images_status=='enabled'){                    
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
                                imagesHtml += `<li class="list-inline-item">
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
        let currentDate = endDate; //moment().format('YYYY-MM-DD');
        let prevDate = startDate; //moment().subtract(days, 'd').format('YYYY-MM-DD');
        let dates = getDates(prevDate, currentDate);
        let chartXData = [];
        let chartYData = [];

        let dataMap = {};
        data.forEach((obj) => {
            dataMap[obj.date] = obj; // Map the date to the data object
        });

        dates.forEach((date) => {
            let dayValue = dataMap[date]; // Get the data for the current date
            // chartXData.push(moment(date).format('MM-DD-YYYY'));
           
            if (dayValue) {
                if (chart == 'height' && dayValue.height != null) {
                // 
                let userHeight = dayValue.height ? parseFloat(dayValue.height) : 0;
                let feet = Math.floor(userHeight / 12);
                let inches = Math.round(userHeight % 12);
                let heightValue =  `${feet}.${inches}`; 
                let heightDecimal =  (feet * 12 + inches )/12;
                // Convert to a decimal value
                chartXData.push(moment(date).format('MM-DD-YYYY'));
                chartYData.push(heightDecimal);
                tooltipLabels.push(heightValue);
            } else if (chart == 'neck' && dayValue.neck != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.neck));
                } else if (chart == 'shoulder' && dayValue.shoulder != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.shoulder));
                } else if (chart == 'chest' && dayValue.chest != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.chest));
                } else if (chart == 'waist' && dayValue.waist != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.waist));
                } else if (chart == 'abdomen' && dayValue.abdomen != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.abdomen));
                } else if (chart == 'hip' && dayValue.hip != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.hip));
                } else if (chart == 'bicep_left' && dayValue.bicep_left != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.bicep_left));
                } else if (chart == 'bicep_right' && dayValue.bicep_right != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.bicep_right));
                } else if (chart == 'thigh_left' && dayValue.thigh_left != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.thigh_left));
                } else if (chart == 'thigh_right' && dayValue.thigh_right != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.thigh_right));
                } else if (chart == 'calf_left' && dayValue.calf_left != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.calf_left));
                } else if (chart == 'calf_right' && dayValue.calf_right != null) {
                    chartXData.push(moment(date).format('MM-DD-YYYY'));
                    chartYData.push(parseInt(dayValue.calf_right));
                } 
            }else if(chart == 'height'){
                chartXData.push(moment(date).format('MM-DD-YYYY'));
                chartYData.push(3);
                tooltipLabels.push("3.0");
            }else{
                chartXData.push(moment(date).format('MM-DD-YYYY'));
                chartYData.push(0);
            }
        })
        if (chart && chart == 'height') {
            heightChart.data.labels = chartXData;
            heightChart.data.datasets[0].data = chartYData;
            heightChart.data.tooltipLabels = tooltipLabels; // Custom tooltips data array
            heightChart.update();
        } else if (chart && chart == 'neck') {
            neckChart.data.labels = chartXData;
            neckChart.data.datasets[0].data = chartYData;
            neckChart.update();
        } else if (chart && chart == 'shoulder') {
            shoulderChart.data.labels = chartXData;
            shoulderChart.data.datasets[0].data = chartYData;
            shoulderChart.update();
        } else if (chart && chart == 'chest') {
            chestChart.data.labels = chartXData;
            chestChart.data.datasets[0].data = chartYData;
            chestChart.update();
        } else if (chart && chart == 'waist') {
            waistChart.data.labels = chartXData;
            waistChart.data.datasets[0].data = chartYData;
            waistChart.update();
        } else if (chart && chart == 'abdomen') {
            abdomenChart.data.labels = chartXData;
            abdomenChart.data.datasets[0].data = chartYData;
            abdomenChart.update();
        } else if (chart && chart == 'hip') {
            hipChart.data.labels = chartXData;
            hipChart.data.datasets[0].data = chartYData;
            hipChart.update();
        } else if (chart && chart == 'bicep_left') {
            bicepLeftChart.data.labels = chartXData;
            bicepLeftChart.data.datasets[0].data = chartYData;
            bicepLeftChart.update();
        } else if (chart && chart == 'bicep_right') {
            bicepRightChart.data.labels = chartXData;
            bicepRightChart.data.datasets[0].data = chartYData;
            bicepRightChart.update();
        } else if (chart && chart == 'thigh_left') {
            thighLeftChart.data.labels = chartXData;
            thighLeftChart.data.datasets[0].data = chartYData;
            thighLeftChart.update();
        } else if (chart && chart == 'thigh_right') {
            thighRightChart.data.labels = chartXData;
            thighRightChart.data.datasets[0].data = chartYData;
            thighRightChart.update();
        } else if (chart && chart == 'calf_left') {
            calfLeftChart.data.labels = chartXData;
            calfLeftChart.data.datasets[0].data = chartYData;
            calfLeftChart.update();
        } else if (chart && chart == 'calf_right') {
            calfRightChart.data.labels = chartXData;
            calfRightChart.data.datasets[0].data = chartYData;
            calfRightChart.update();
        }

    }

    $(function() {
        let healthMeasurment = @json($settings);   
        // startDate = moment().subtract(10, "days");
        // endDate = moment();
        if(healthMeasurment.log_marker == "MONTHLY"){
            startDate = moment().startOf('month');
            endDate = moment().endOf('month');
        } else if(healthMeasurment.log_marker == "WEEKLY"){
            startDate = moment().startOf('week');
            endDate = moment().endOf('week');
        } else if(healthMeasurment.log_marker == "DAILY"){
            startDate = moment();
            endDate = moment();
        } else if(healthMeasurment.log_marker == "EVERY_OTHER_WEEK"){
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
            loadHealthMeasurementLog();
        });
        loadHealthMeasurementLog();
    });
</script>
@endsection