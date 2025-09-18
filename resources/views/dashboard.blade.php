@extends('layouts.app')
<title>Dashboard</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userData = getUser();
        $userType = userType();
        $permissions =  getSidebarPermissions();
        $isCustomizeDashboard = isCustomizeDashboard();
        
    @endphp
  
    <div class="content-wrapper pb-4">
        @if (!empty($quote))
            <div class="quote-card-head">
                <div class="quote-card">
                    @if ($quote->quote_type == 'quote')
                        <i class="fas fa-quote-left"></i>
                        <p>{{ ucfirst($quote->description) }}</p>
                        <p class="author">{{ !empty($quote->author) ? $quote->author : '' }}</p>
                        <i class="fas fa-quote-right"></i>
                    @endif
                    @if ($quote->quote_type == 'message')
                        <!-- <h4>Good Morning, <span>Rose</span> </h4> -->
                        <p>{{ ucfirst($quote->description) }}</p>
                        <!-- <p class="author">dnjd</p> -->
                    @endif
                    <!-- <img src="{{ url('assets/images/quote-shape.svg') }}" alt=""> -->
                    <!-- <div class="quote-profile">
                                                                                                                        @if (!empty($userData->media) && !empty($userData->media->base_url))
    <img src="{{ $userData->media->base_url }}" alt="User Avatar" class="">
@else
    <img class="" src="{{ url('assets/images/default-user.jpg') }}" alt="User profile picture">
    @endif
                                                                                                                    </div> -->
                </div>
            </div>
        @endif
        @if(!empty($broadcastAlert))
        <div class="alert dashboard-alert" role="alert">
            <span class="alert-icon">
                
            <i class="fa fa-exclamation" aria-hidden="true"></i>
            </span>
            <div class="dashboard-alert-desc">
                <h6>{{$broadcastAlert['broadcast']['title']}}</h6>
                {!! $broadcastAlert['broadcast']['message'] !!}
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto" onclick="removeBroadcastDashboardAlert({{$broadcastAlert['broadcast']['id']}})" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="">
            
            <div class='customize-title-head'>
           
                @if(!empty($userDashboard))
                <h1 id="dashboardNameWrapper">{{ $userDashboard->dashboard_name }} </h1>
                @else
                <h1 id="dashboardNameWrapper">Dashboard</h1>
                @endif
                <div class="d-flex">
                    @if($userType == 'parent' && !empty($athletes) && count($athletes) > 0 )
                    <div class="mx-2">
                        <select class="form-select" onchange="chooseAthlete(event)"  name="athlete_id" id="athlete_id">
                        <option value="{{$userData->id}}" selected>Me ({{$userData->first_name}})</option>
                        @foreach($athletes as $athlete)
                        <option value="{{$athlete->id}}">{{$athlete->first_name}}</option>
                        @endforeach

                        </select>
                    </div>
                    @endif 
                   
                    @if($userType != 'admin' && $isCustomizeDashboard)
                    <div>
                        <a href="{{route('customizeDashboard',['user_type' => $userType])}}" class='btn btn-secondary'>Customize Dashboard</a>
                    </div>
                    @endif
                </div>
            </div>
            @if (!empty($userRedeemData) && !empty($userRedeemData->user))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ $userRedeemData->user->first_name . ' ' . $userRedeemData->user->last_name }} just earned a brand new PRIZE!
                <button type="button" class="btn-close btn-prize-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <!-- Fitness Challenge Widget -->
            <x-challenge-alert />
            <div class="row dashboard-card-head ">
                @if (Auth::guard('web')->check())
                    @if (Auth::guard('web')->user()->user_type == 'admin')
                        <div class="col-lg-3 col-6 dashboard-card">
                            <a href="{{ route('user.users', ['user_type' => $userType]) }}">
                                <div class="small-box bg-secondary">
                                    <div class="inner">
                                        <!-- <h3>{{ $userCount }}<sup style="font-size: 20px"></sup></h3>
                                                                                                                                            <p>Total Users</p> -->
                                        <h2>Users</h2>
                                        <p></p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users text-white" style="font-size: 50px !important"></i>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-3 col-6 dashboard-card">
                            <a href="{{ route('user.plans', ['user_type' => $userType]) }}">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <!-- <h3>{{ $planCount }}</h3>
                                                                                                                                            <p>Total Plans</p> -->
                                        <h2>Plans</h2>
                                        <p></p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-city text-white" style="font-size: 50px !important"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-6 dashboard-card">
                            <a href="{{ route('manageWidgets', ['user_type' => $userType]) }}">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <!-- <h3>{{ $userCount }}<sup style="font-size: 20px"></sup></h3>
                                                                                                                                            <p>Total Users</p> -->
                                        <h2>Manage Widgets</h2>
                                        <p></p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-th text-white" style="font-size: 50px !important"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                    @if (Auth::guard('web')->user()->user_type == 'parent')
                    <div class="col-lg-3 col-6">
                            <a href="{{ route('user.athlete', ['user_type' => $userType]) }}">
                                <div class="small-box bg-dark">
                                    <div class="inner">
                                        <h3 class="text-white">Athletes</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users text-white" style="font-size: 50px !important"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @if(! empty($userDashboard) || !empty($athletes))
                        <div id="">
                            <div class='row customize-row recipe-list-sec' id='list'>

                            </div>
                        </div>
                        @endif
                    @endif

                    @if (Auth::guard('web')->user()->user_type == 'content-creator')
                        @if(! empty($userDashboard))
                        <div class='row customize-row recipe-list-sec' id='list'>

                        </div>
                        
                        @else
                        <div class="col-lg-4 col-6">
                            <a href="{{ route('user.trainingVideo', ['user_type' => $userType]) }}">
                                <div class="small-box bg-light">
                                    <div class="inner">
                                        <h3>Training Videos</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-ninja text-dark" style="font-size: 50px !important"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                    @endif
                @endif
            </div>
        </div>
        @if (Auth::guard('web')->user()->user_type == 'athlete' && count($allowedWidgets) > 0)
           
            <div id="upsell-message" class="row"></div>
            @if(! empty($userDashboard))
            <div class='row customize-row recipe-list-sec' id='list'>

            </div>
            
            @else
            <div class="card fitness-box cursor-pointer {{ in_array('workouts', $allowedWidgets) ? '' : 'hide_widget' }}"
                onClick="redirectTo('{{ route('user.fitnessProfile', ['user_type' => $userType]) }}')">
                <div class="card-body" id="fitnessWeekLog"></div>
            </div>
            <div class="dash-chart-head">
                <div class="row">
                    <div class="{{ in_array('water-tracker', $allowedWidgets)?'':'hide_widget'}} col-md-5">
                        <h3><a href="{{ route('user.waterTracker', ['user_type' => $userType]) }}">Health Activity</a></h3>
                        <div class="bg-white mb-4 p-4 dash-chart water-chart">
                            <div class="row">
                                <div class="col-md-2 text-center cursor-pointer"
                                    onClick="redirectTo('{{ route('user.waterTracker', ['user_type' => $userType]) }}')">
                                    <img class="dash-chart-img" src="{{ url('assets/images/water-drop.png') }}">
                                    <h6 class="text-center">Water</h6>
                                </div>
                                <div class="col-md-10">
                                    <canvas id="barChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 {{in_array('progress-pictures',$allowedWidgets)?'':'hide_widget'}}">
                        <h3><a href="{{ route('user.fitnessProfile', ['user_type' => $userType]) }}">Fitness Activity</a>
                        </h3>
                        <div class="bg-white mb-4 p-4 dash-chart cursor-pointer"
                            onClick="redirectTo('{{ route('user.fitnessProfile', ['user_type' => $userType]) }}')">
                            <div class="row align-items-unset mb-4">
                                <!-- <h5 class="mb-0">Overview</h5>
                                                                                                                                <div class="fitness-cta">
                                                                                                                                    <button type="button" onclick="loadTrainingList('','',true)" class="btn btn-secondary ripple-effect-dark btn-fill">Month</button>
                                                                                                                                    <button type="button" onclick="resetFilter()" class="btn  ripple-effect btn-outline">Weak</button>
                                                                                                                                </div> -->
                                <div class="col-md-4 charts fitness-chart" id="chartOneDiv">
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
                                <div class="col-md-4 charts fitness-chart" id="chartTwoDiv">
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
                                <div class="col-md-4 charts fitness-chart" id="chartThreeDiv">
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
                </div>
            </div>
            <div class="bg-white p-4 radius-8">
                <div class="row ">
                    <div class="col-md-6 {{in_array('health-tracker',$allowedWidgets)?'':'hide_widget'}}" id="healthTracker"></div>
                    <div class="col-md-6 recipe-list-sec-head">
                        <div class="row recipe-list-sec {{in_array('new-recipes',$allowedWidgets)||in_array('my-workouts',$allowedWidgets)?'':'hide_widget'}}" id="latestTrainingRecipe">

                        </div>
                    </div>
                </div>
            </div>
            
            @endif
        @endif
        <!-- Add athele  Model -->
    <div class="modal fade show" id="addAtheleModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content p-4">
                <div class="modal-body text-center pb-0">
                    <h5 class="modal-title" id="exampleModalLabel">Add Your First Athlete Account.</h5>
                </div>

                <div class="modal-body text-center">
                    <a class="btn btn-secondary ripple-effect-dark text-white" href="{{ route('user.addAthleteForm', ['user_type' => $userType]) }}">Add</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Add athelel End -->
@endsection
@section('js')
{!! JsValidator::formRequest('App\Http\Requests\ParentAccountRequest','#addParentForm') !!}

<script>
    let user_type = @json($userType);
    var barChartData = [];
    const sessionData = @json(session()->all());
    var athletes = @json($athletes);
    var athleteOption = document.getElementById('athlete_id');
    var selectedAthlete = null;
    var athlete_id = @json($userData->id);
    var widgets = @json($widgetKeys);
    function showAddParent() {
        $('#addParentModal').modal('show');
    }
    function hideParentModal() {
        $('#addParentForm')[0].reset();
        $('#addParentModal').modal('hide');
    }

    function saveParent() {
        var formData = $("#addParentForm").serializeArray();
        if ($('#addParentForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                url: "{{route('common.requestParentAccount')}}",
                type: "POST",
                data: formData,
                success: function(data) {
                    if (data.success) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        _toast.success(data.message);
                        hideParentModal();
                    } else {
                        _toast.error(data.message);
                    }
                },
                error: function(err) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    }
                },
            });
        }
    }

    /**
     * Check parent athlete user.
     * @request search, status
     * @response object.
     */
    function hasParentAthlete(user_type) 
    {
        var url = "{{route('users.hasParentAthlete', ['user_type' => '__USER_TYPE__'])}}";
        url = url.replace('__USER_TYPE__', user_type);    
        $.ajax({
            type: "GET",
            url: url,
            success: function(response) {
                if (response.success) {
                    if(!response.data){
                        $('#addAtheleModel').modal('show');
                    }
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }
    // 
    //     // Page was refreshed
    //     location.reload();
    // }
    window.onpopstate = function(event) {
        window.location.reload();
    };
    $(document).ready(function () {

        displayUpsellMessage('dashboard_widget');
        if("{{ $userType }}" !== "admin" || (@json($userDashboard) !== null||[] && "{{ $userType }}" !== "athlete") || "{{$userType}}" == 'parent' ){
            dynamicWidgetsList();
            
        }
        else {
            if(@json($userType) === 'athlete' && @json($userDashboard) === null || []){
                loadDashboardActivityList();
                loadDetail();
                getLatestTrainingRecipe();
                initializeChartOne();
                initializeChartTwo();
                initializeChartThree();
                loadTodayWorkOutReport();
            }
        } 
        if(user_type == 'parent'){
            hasParentAthlete(user_type)
        }
    });
    const current_user = @json($userData);
    const currentDay = @json($currentDay);
    const userDashboard = @json($userDashboard);
    var athleteWidgets = [];

    function dynamicWidgetsList() {
        const widgetsListContainer = $('#list');
        if(@json($userType) != 'parent'){
            athlete_id =null;
        }
        $.ajax({
            url: "{{ route('common.displayActiveWidgets') }}",
            type: "POST",
            data: {
                widgets: widgets || [],
                user_data: current_user || null,
                currentDay: currentDay || null,
                isCustomize: false,
                athlete_id: athlete_id || null,
                perPage: 1,
                type: 'my-workouts',
            },
            success: function(response) {
                $('#list').html(response.data);
                if (!response.data) {

                    const dashboardNameWrapper = document.getElementById('dashboardNameWrapper');
                    if (athlete_id !== current_user.id) {
                        dashboardNameWrapper.innerHTML = `<h1> Dashboard </h1>`;
                    }
                }
                if ((selectedAthlete !== undefined && selectedAthlete !== null)|| athlete_id != current_user.id) {
                    // If widgets is empty, show "No widgets selected."
                    if (widgets.length === 0) {
                        widgetsListContainer.html('<p>No widgets selected.</p>');
                    }
                }
            },
            error: function(err) {
                widgetsListContainer.html('<p>Error loading widgets list.</p>');
            },
        });
    }
       
    if(@json($userType) === "athlete" && (@json($userDashboard) == null) || (@json($userDashboard) ==[])  ){
        function getDays() {
            var startOfWeek = moment().startOf('week').add('d', 1); //.format('YYYY-MM-DD');
            var endOfWeek = moment().endOf('week').add('d', 1); //.format('YYYY-MM-DD');
            var daysArr = [];
            var currentDate = moment(startOfWeek);
            var stopDate = moment(endOfWeek);
            while (currentDate <= stopDate) {
                daysArr.push(moment(currentDate).format('YYYY-MM-DD'))
                currentDate = moment(currentDate).add(1, 'days');
            }
            return daysArr;
        }
        var barChart = new Chart("barChart", {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Data',
                    data: [],
                    // backgroundColor: 'rgb(124, 124, 255)',
                    backgroundColor: '#f37121',
                    barPercentage: 0.5
                }]
            },
            options: {
                legend: {
                    display: false,
                },
                plugins: {
                    datalabels: {
                        color: 'white',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                onClick: function(evt, data) {}
            }
        });

        function calculateActivityLog(waterActivityLog = [], heightActivityLog = []) {
            let dates = getDays();
            let dayValues = [];
            let chartXData = [];
            let chartYData = [];
            dates.forEach((date) => {
                let water = waterActivityLog[date] ? waterActivityLog[date] : 0;
                barChartData.push(water);
                dayValues.push(moment(date).format('ddd'));
            })
            barChart.data.labels = dayValues;
            barChart.data.datasets[0].data = barChartData;
            barChart.update();

        }

        function initializeChartOne(data = [0, 0], percOne = 0) {
            $ctxOnetext = $("#percOne");
            let canvasOne = document.getElementById('chartOne');
            let ctxOne = canvasOne?.getContext('2d');

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
                            var cx = canvasOne?.width / 2;
                            var cy = canvasOne?.height / 2;
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
            let ctxTwo = canvasTwo?.getContext('2d');

            new Chart(ctxTwo, {
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
                            var cx = canvasTwo?.width / 2;
                            var cy = canvasTwo?.height / 2;
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
            let ctxThree = canvasThree?.getContext('2d');
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
                            var cx = canvasThree?.width / 2;
                            var cy = canvasThree?.height / 2;
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
    }

    /**
     * Load list.
     * @request search, status
     * @response object.
     */
    function loadDashboardActivityList() {
        $("#fitnessWeekLog").html('<div class="text-center">{{ ajaxListLoader() }}</div>');
        let startOfWeek = moment().startOf('week').add('d', 1).format('YYYY-MM-DD');
        let endOfWeek = moment().endOf('week').add('d', 1).format('YYYY-MM-DD');
        let daysActivities = {};
        $.ajax({
            type: "GET",
            url: "{{ route('common.loadDashboardActivityList') }}",
            data: {
                from_date: startOfWeek,
                to_date: endOfWeek,
                date: moment().format('YYYY-MM-DD'),
            },
            success: function(response) {
                if (response.success) {
                    response.data.water.forEach((obj) => {
                        if (daysActivities.hasOwnProperty(obj.date)) {
                            daysActivities[obj.date] += parseInt(obj.water_value);
                        } else {
                            daysActivities[obj.date] = parseInt(obj.water_value);
                        }
                    });
                    calculateActivityLog(daysActivities, response.data.height);
                    $("#fitnessWeekLog").html("");
                    $('#fitnessWeekLog').append(response.data.fitnessWeekData);
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }

    /**
     * Load detail.
     * @request search, status
     * @response object.
     */
    function loadDetail() {
        $("#healthTracker").html('<div class="text-center">{{ ajaxListLoader() }}</div>');
        url = "{{ route('common.healthTracker.detail') }}";
        $.ajax({
            type: "GET",
            url: url,
            data: {
                type: "dashboard",
                allowedWidgets : @json($allowedWidgets),
            },
            success: function(response) {
                if (response.success) {
                    $("#healthTracker").html("");
                    $('#healthTracker').append(response.data.html);
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }


    /**
     * Load detail.
     * @request search, status
     * @response object.
     */
    function getLatestTrainingRecipe() {
        $("#latestTrainingRecipe").html('<div class="text-center">{{ ajaxListLoader() }}</div>');
        url = "{{ route('common.dashboard.getLatestTrainingRecipe') }}";
        $.ajax({
            type: "GET",
            url: url,
            data: {
                perPage: 1,
                allowedWidgets : @json($allowedWidgets),
            },
            success: function(response) {
                if (response.success) {
                    $("#latestTrainingRecipe").html("");
                    $('#latestTrainingRecipe').append(response.data.html);
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }
    /**
     * Save Favourite
     * @request form fields
     * @response object.
     */
    function addRecipeToFavourite(favourite, id) {
        var url = "{{ route('common.saveRecipeFavourite', ['id' => '%recordId%']) }}";
        url = url.replace('%recordId%', id);
        $.ajax({
            type: "POST",
            url: url,
            data: {
                _token: "{{ csrf_token() }}",
                favourite: favourite
            },
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    if (data.is_favourite) {
                        $("#isRecipeUnFavourite" + id).show();
                        $("#isRecipeFavourite" + id).hide();
                    } else {
                        $("#isRecipeUnFavourite" + id).hide();
                        $("#isRecipeFavourite" + id).show();
                    }
                } else {
                    _toast.error('Somthing went wrong. please try again');
                }
            },
            error: function(err) {
                cosole.log(err);
            },
        });
    };

    /**
     * Save Favourite
     * @request form fields
     * @response object.
     */
    function addVideoToFavourite(favourite, id) {
        var url = "{{ route('common.saveTrainingVideoFavourite', ['id' => '%recordId%']) }}";
        url = url.replace('%recordId%', id);
        $.ajax({
            type: "POST",
            url: url,
            data: {
                _token: "{{ csrf_token() }}",
                favourite: favourite
            },
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    if (data.is_favourite) {
                        $("#isVideoUnFavourite" + id).show();
                        $("#isVideoFavourite" + id).hide();
                    } else {
                        $("#isVideoUnFavourite" + id).hide();
                        $("#isVideoFavourite" + id).show();
                    }
                } else {
                    _toast.error('Something went wrong. please try again');
                }
            },
            error: function(err) {
            },
        });
    };

    /*
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
                date: moment().format('YYYY-MM-DD')
            },
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    //chart one
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
                    let chartThreeTotalWorkout = data.thisMonth.total_workouts - data.thisMonth
                        .completed_workouts;
                    chartThreeTotalWorkout = (data.thisMonth.total_workouts > 0) ? chartThreeTotalWorkout :
                        1;
                    let chartThreeData = [data.thisMonth.completed_workouts, chartThreeTotalWorkout];
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

    function redirectTo(redirectTo) {
        window.location.href = redirectTo;
    }

    if (@json($userType) === "athlete" && isEmpty(@json($userDashboard))) {
        loadDashboardActivityList();
        loadDetail();
        getLatestTrainingRecipe();
        loadTodayWorkOutReport();
    }
    function isEmpty(value) {
    // Handle null and undefined
    if (value === null || value === undefined) {
        return true;
    }

    // Handle boolean
    if (typeof value === "boolean") {
        return !value; // true if it's `false`
    }

    // Handle numbers (including NaN)
    if (typeof value === "number") {
        return isNaN(value) || value === 0; // true if NaN or 0
    }

    // Handle strings
    if (typeof value === "string") {
        return value.trim().length === 0; // true if empty or only spaces
    }

    // Handle arrays
    if (Array.isArray(value)) {
        return value.length === 0; // true if array has no elements
    }

    // Handle objects
    if (typeof value === "object") {
        return Object.keys(value).length === 0; // true if object has no keys
    }

    // Handle other types (e.g., functions, symbols, etc.)
    return false; // assume non-empty for unsupported types
}

   
    /*
    * Broadcast dashboard alert remove .
    * @response object.
    */
    function removeBroadcastDashboardAlert(id) {
        url = "{{ route('common.removeBroadcastAlert', ':id') }}";
        url = url.replace(':id', id);
        $.ajax({
            type: "POST",
            url: url,
            success: function(response) {
                if (response.success) {
                    // _toast.success(response.message);
                    loadDetail();                            
                }
            },
            error: function() {
                // _toast.error('Somthing went wrong.');
            }
        });
    }

    function removeUpsell(event, upsell) {
        event.stopPropagation();
        // Remove the widget from the DOM
        const upsellElement = document.getElementById(`upsellMessage_${upsell.id}`);
        if (upsellElement) {
            upsellElement.remove();
        }
        if( (upsell.frequency !== 'always' || upsell.frequency !== 'once_per_login' )){
            var url = "{{ route('common.removeUserUpsell') }}";
            $.ajax({
                type: "PUT",
                url: url,
                data: {
                    '_token': "{{ csrf_token() }}",
                    'upsell_id' : upsell.id,
                },
                success: function(response) {
                    if (response.success) {
                        // loadList();
                        // _toast.success(response.message);
                    } else {
                        // _toast.error(response.message);
                    }
                },
                error: function(err) {
                    var errors = $.parseJSON(err.responseText);
                    _toast.error(errors.message);
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        // _toast.error(errors.message);
                    }
                }
            });
        }
    }

    function chooseAthlete(event) {
        athlete_id = event?event.target.value:"";
        var widgetsListContainer = $('#list');
        widgets= [];
        let dashboardWrapper = document.getElementById('dashboardNameWrapper');
        if (!userDashboard) {
                dashboardWrapper.innerHTML = `<h1 id="dashboardNameWrapper">Dashboard</h1>`; // Fixed incorrect use of `html()`
                widgetsListContainer.html('');
        } else {
            dashboardWrapper.innerHTML = `<h1 id="dashboardNameWrapper">${userDashboard.dashboard_name}</h1>`;
        }
        selectedAthlete = athletes.find(athlete => athlete?.id == athlete_id);  
        getAthleteDashboard();
        dynamicWidgetsList();
    }

    
    function getAthleteDashboard() {
        document.getElementById('athlete_id')   ? document.getElementById('athlete_id').disabled = true  : null;
        $('#list').html(`<div class="text-center">{{ ajaxListLoader() }}</div>`);
        url = "{{route('getDynamicDashboard')}}";
        $.ajax({
            type: "GET",
            url: url,
            data: {
                userType: @json($userType),
                athlete_id: athlete_id,
            },
            success: function(response) {
                if (response.success) {
                    const dashboardData = response.data;
                    document.getElementById('athlete_id')   ? document.getElementById('athlete_id').disabled = false  : null;
                    if (dashboardData) {
                        const athleteDashboardName = dashboardData.dashboard_name;
                        const dashboardNameWrapper = document.getElementById('dashboardNameWrapper');
                        dashboardNameWrapper.innerHTML = `<h1>${athleteDashboardName}</h1>`;
                        athleteWidgets=[];
                        dashboardData.widgets.map((widget)=>{
                            athleteWidgets.push(widget.widget.widget_key);
                        })
                        widgets = athleteWidgets;
                        dynamicWidgetsList(); 
                    } 
                } else {
                    console.error("Failed to retrieve dashboard data:", response.message);
                }
            },
            error: function(err) {
                var errors = $.parseJSON(err.responseText);
                _toast.error(errors.message);
            }
        });
    }

    function showRemoveButton(){
    }
    function hideRemoveButton(){
    }
</script>
@endsection
