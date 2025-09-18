@extends('layouts.app')
<title>Water Tracker</title>
@section('content')
    @include('layouts.sidebar')
    @php $userType = userType(); @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Water Tracker</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <!-- <h2 class="page-title text-capitalize mb-0">
                    Water Tracker
                </h2> -->
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
                <a href="{{ route('user.waterTracker.addWaterForm', ['user_type' => $userType]) }}"
                    class="btn btn-secondary btn-orange ripple-effect-dark text-white">
                    INPUT RESULT
                </a>
                <a class="ms-2 btn btn-primary" href="{{ route('user.waterTracker.setGoal', ['user_type' => $userType]) }}">
                    RESET MY GOAL
                </a>
            </div>
        </div>
        <!-- Fitness Challenge Widget -->
            <x-challenge-alert type="water-intake"/>
        <!-- Header text Start -->
        <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm" ></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!-- Header text End -->
        <!-- filter section start -->
        <div class="card">
            <div class="card-body water-tracker-card-body">
                <div class="row">
                    <div class="col-md-4 charts active" id="chartOneDiv">
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="chartOne" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num" id="percOne"></p>
                        </div>
                        <p>14 day goal</p>
                    </div>
                    <div class="col-md-4 charts" id="chartTwoDiv">
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="chartTwo" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num" id="percTwo"></p>
                        </div>
                        <p>30 day goal</p>
                    </div>
                    <div class="col-md-4 charts" id="chartThreeDiv">
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="chartThree" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num" id="percThree"></p>
                        </div>
                        <p>90 day goal</p>
                    </div>
                    <div class="col-md-12">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        let userGoal = {};
        let userGoalLogs = [];
        let barChartData = [];
        getUserGoalLog();
        loadHeaderText('water-tracker');

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
                    onClick: function(e) {
                        calculateLogs("chartOne");
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
                            // ctxTwo.font = '80px verdana';
                            ctxTwo.font = '0px verdana';
                            ctxTwo.fillStyle = '#EF8E47';
                            // ctxTwo.fillText(percTwo + "%", cx, cy);
                        }
                    },
                    onClick: function(e) {
                        calculateLogs("chartTwo");
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
            new Chart(ctxThree, {
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
                            var cx = canvasThree.width / 2;
                            var cy = canvasThree.height / 2;
                            ctxThree.textAlign = 'center';
                            ctxThree.textBaseline = 'middle';
                            // ctxThree.font = '80px verdana';
                            ctxThree.font = '0px verdana';
                            ctxThree.fillStyle = '#EF8E47';
                            // ctxThree.fillText(percThree + "%", cx, cy);
                        }
                    },
                    onClick: function(e) {
                        calculateLogs("chartThree");
                    }
                }
            });
            $ctxThreeText.html('');
            $ctxThreeText.append(percThree + "%");
        }
        let barChart = new Chart("barChart", {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Data',
                    data: [],
                    backgroundColor: 'rgb(124, 124, 255)',
                    barPercentage: 0.5
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        color: 'white',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                onClick: function(evt, data) {
                    var chartData = barChart.getElementAtEvent(evt);
                    if (chartData && chartData.length) {
                        let labelDate = chartData[0]._model.label;
                        bootbox.confirm('Are you sure you want to edit this date detail ?', function(result) {
                            if (result) {
                                var url =
                                    "{{ route('user.waterTracker.editWaterForm', ['user_type' => $userType]) }}";
                                url = url + '?date=' + moment(labelDate).format('YYYY-MM-DD');
                                setTimeout(function() {
                                    window.location.href = url;
                                }, 500)
                            }
                        })
                    }
                }
            }
        });

        function getUserGoalLog() {
            $.ajax({
                type: "GET",
                url: "{{ route('common.waterTracker.getGoalLog') }}",
                data: {},
                success: function(response) {
                    if (response.success) {
                        userGoalLogs = response.data.userGoalLogs;
                        calculateDonutChartPercent('chartOne', userGoalLogs);
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

        function calculateDonutChartPercent(chart, data) {
            $('.charts').removeClass('active');
            if (data.chartOne) {
                totalGoal = data.chartOne.totalGoal;
                totalGoal = (totalGoal > 0) ? totalGoal : 1;
                let chartOneData = [data.chartOne.totalWaterValues, totalGoal];
                let chartOnePercent = data.chartOne.percent;
                initializeChartOne(chartOneData, chartOnePercent);
                $('#chartOneDiv').addClass('active');
                calculateLogs("chartOne");
            }
            if (data.chartTwo) {
                totalGoal = data.chartTwo.totalGoal;
                totalGoal = (totalGoal > 0) ? totalGoal : 1;
                let chartTwoData = [data.chartTwo.totalWaterValues, totalGoal];
                let chartTwoPercent = data.chartTwo.percent;
                initializeChartTwo(chartTwoData, chartTwoPercent);
                $('#chartTwoDiv').addClass('active');
            }
            if (data.chartThree) {
                totalGoal = data.chartThree.totalGoal;
                totalGoal = (totalGoal > 0) ? totalGoal : 1;
                let chartThreeData = [data.chartThree.totalWaterValues, totalGoal];
                let chartThreePercent = data.chartThree.percent;
                initializeChartThree(chartThreeData, chartThreePercent);
                $('#chartThreeDiv').addClass('active');
            }

        }

        function calculateLogs(chart = "chartOne") {
            if (chart == "chartOne") {
                let chartOneData = [];
                let chartDates = [];
                userGoalLogs.chartOne.dates.forEach((date) => {
                    chartDates.push(moment(date).format('MM-DD-YYYY'));
                    let dayValue = userGoalLogs.chartOne.data.find((obj) => obj.date == date);
                    if (dayValue) {
                        chartOneData.push(dayValue.water_value);
                    } else {
                        chartOneData.push(0);
                    }
                    //barChartData.push(date);
                })
                barChart.data.labels = chartDates;
                barChart.data.datasets[0].data = chartOneData;
                barChart.update();
            }

            if (chart == "chartTwo") {
                let chartTwoData = [];
                let chartTwoDates = [];
                userGoalLogs.chartTwo.dates.forEach((date) => {
                    chartTwoDates.push(moment(date).format('MM-DD-YYYY'));
                    let dayValue = userGoalLogs.chartTwo.data.find((obj) => obj.date == date);
                    if (dayValue) {
                        chartTwoData.push(dayValue.water_value);
                    } else {
                        chartTwoData.push(0);
                    }
                    //barChartData.push(date);
                })
                barChart.data.labels = chartTwoDates;
                barChart.data.datasets[0].data = chartTwoData;
                barChart.update();
            }

            if (chart == "chartThree") {
                let chartThreeData = [];
                let chartThreeDates = [];
                userGoalLogs.chartThree.dates.forEach((date) => {
                    chartThreeDates.push(moment(date).format('MM-DD-YYYY'));
                    let dayValue = userGoalLogs.chartThree.data.find((obj) => obj.date == date);
                    if (dayValue) {
                        chartThreeData.push(dayValue.water_value);
                    } else {
                        chartThreeData.push(0);
                    }
                    //barChartData.push(date);
                })
                barChart.data.labels = chartThreeDates;
                barChart.data.datasets[0].data = chartThreeData;
                barChart.update();
            }
        }
    </script>
@endsection
