@extends('layouts.app')
<title>Sleep Tracker</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Sleep Tracker</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <!-- <h2 class="page-title text-capitalize mb-0">
                    Sleep Tracker
                </h2> -->
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
                <a href="{{ route('user.sleepTracker.addSleepForm', ['user_type' => $userType]) }}"
                    class="btn btn-secondary btn-orange ripple-effect-dark text-white">
                    INPUT RESULT
                </a>
                <a class="ms-2 btn btn-primary" href="{{ route('user.sleepTracker.setGoal', ['user_type' => $userType]) }}">
                    RESET MY GOAL
                </a>
            </div>
        </div>
        <!-- Fitness Challenge Widget -->
            <x-challenge-alert type="sleep-tracker"/>
         <!--Header Text start-->
         <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm" ></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!-- Header text End -->
        <div class="card">
            <div class="card-body water-tracker-card-body">
                <div class="row">
                    <div class="col-md-4 charts active" id="chartOneDiv">
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="chartOne" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num" id="percOne"></p>
                        </div>
                        <p>This Week</p>
                    </div>
                    <div class="col-md-4 charts" id="chartTwoDiv">
                        <div class="chart-head">
                            <canvas class="chart__canvas" id="chartTwo" width="160" height="160"
                                aria-label="Example doughnut chart showing data as a percentage" role="img"></canvas>
                            <p class="chart-num" id="percTwo"></p>
                        </div>
                        <p>This Month</p>
                    </div>
                    <div class="col-md-4">
                        <ul class="emoji-count-list" id="emojiCounts"></ul>
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
    loadHeaderText('sleep-tracker');
    $(document).ready(function() {
        getUserSleepLog();
    })
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
    let barChart = new Chart("barChart", {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Hours',
                data: [],
                backgroundColor: 'rgb(124, 124, 255)',
                barPercentage: 0.5,
                notes: [],
            }],
        },
        options: {
            tooltips: {
                // Disable the on-canvas tooltip
                enabled: false,
                custom: function (tooltipModel) {
                    // Tooltip Element
                    var tooltipEl = document.getElementById('chartjs-tooltip');
                    // Create element on first render
                    if (!tooltipEl) {
                        tooltipEl = document.createElement('div');
                        tooltipEl.id = 'chartjs-tooltip';
                        tooltipEl.style.background = 'rgba(0, 0, 0, 0.8)';
                        tooltipEl.style.color = 'white';
                        tooltipEl.style.borderRadius = '5px';
                        tooltipEl.style.padding = '10px';
                        tooltipEl.style.pointerEvents = 'none';
                        tooltipEl.style.position = 'absolute';
                        tooltipEl.style.transform = 'translate(-50%, 0)';
                        tooltipEl.style.transition = 'all 0.3s ease';
                        tooltipEl.style.zIndex = '1000';
                        tooltipEl.innerHTML = '<div id="tooltip-content"></div>';
                        document.body.appendChild(tooltipEl);
                    }
                    // Hide if no tooltip
                    if (tooltipModel.opacity === 0) {
                        tooltipEl.style.opacity = 0;
                        return;
                    }
                    // Set caret position
                    tooltipEl.classList.remove('above', 'below', 'no-transform');
                    if (tooltipModel.yAlign) {
                        tooltipEl.classList.add(tooltipModel.yAlign);
                    } else {
                        tooltipEl.classList.add('no-transform');
                    }
                    function getBody(bodyItem) {
                        return bodyItem.lines;
                    }

                    // Set Text
                    if (tooltipModel.body) {
                        console.log('tooltipModel', tooltipModel);
                        const dataPoint = tooltipModel.dataPoints[0]; // First data point
                        const index = dataPoint.index; // Get the index of the point
                        const datasetIndex = dataPoint.datasetIndex; // Get the dataset index

                        // Access custom data from your dataset
                        const dataset = this._data.datasets[datasetIndex];
                        const notes = dataset.notes[index];
                        var titleLines = tooltipModel.title || [];
                        var bodyLines = tooltipModel.body.map(getBody);
                        var innerHtml = '<div>';
                        titleLines.forEach(function(title) {
                            innerHtml += '<p style="margin: 0; font-size: 14px; font-weight: bold;">Date: ' + title + '</p>';
                        });
                        bodyLines.forEach(function(body, i) {
                            innerHtml += '<p style="margin: 0; font-size: 14px;">' + body + '</p>';
                            innerHtml += '<p style="margin: 0; font-size: 14px;">Note: ' + notes + '</p>';
                        });
                        innerHtml += '</div>';
                        var tooltipContent = tooltipEl.querySelector('#tooltip-content');
                        tooltipContent.innerHTML = innerHtml;
                    }
                    // Tooltip position
                    var position = this._chart.canvas.getBoundingClientRect();
                    tooltipEl.style.opacity = 1;
                    tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
                    tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
                    tooltipEl.style.fontFamily = tooltipModel._bodyFontFamily || "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
                    tooltipEl.style.fontSize = tooltipModel.bodyFontSize + 'px';
                    tooltipEl.style.fontStyle = tooltipModel._bodyFontStyle || 'normal';
                }
            },
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
                            var url = "{{ route('user.sleepTracker.editSleepForm', ['user_type' => $userType]) }}";
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

    function getUserSleepLog() {
        $.ajax({
            type: "GET",
            url: "{{ route('common.userSleepLog') }}",
            data: {},
            success: function(response) {
                if (response.success) {
                    userSleepLogs = response.data.userSleepLogs;
                    calculateDonutChartPercent('chartOne', userSleepLogs);
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
            let chartOneData = [data.chartOne.totalSleepValues, totalGoal];
            let chartOnePercent = data.chartOne.percent;
            initializeChartOne(chartOneData, chartOnePercent);
            $('#chartOneDiv').addClass('active');
            calculateLogs("chartOne");
        }
        if (data.chartTwo) {
            totalGoal = data.chartTwo.totalGoal;
            totalGoal = (totalGoal > 0) ? totalGoal : 1;
            let chartTwoData = [data.chartTwo.totalSleepValues, totalGoal];
            let chartTwoPercent = data.chartTwo.percent;
            initializeChartTwo(chartTwoData, chartTwoPercent);
            $('#chartTwoDiv').addClass('active');
        }
    }

    function calculateLogs(chart = "chartOne") {
        if (chart == "chartOne") {
            let chartOneData = [];
            let chartDates = [];
            let chartNotes = [];
            userSleepLogs.chartOne.dates.forEach((date) => {
                chartDates.push(moment(date).format('MM-DD-YYYY'));
                let dayValue = userSleepLogs.chartOne.data.find((obj) => obj.date == date);
                if (dayValue) {
                    chartOneData.push(dayValue.sleep_duration);
                    chartNotes.push(dayValue.sleep_notes);
                } else {
                    chartOneData.push(0);
                    chartNotes.push('NA');
                }
                //barChartData.push(date);
            });
            barChart.data.labels = chartDates;
            barChart.data.datasets[0].data = chartOneData;
            barChart.data.datasets[0].notes = chartNotes;
            barChart.update();
            displayAverageAndEmojiCounts(userSleepLogs.chartOne.emojiCountsOne);
            // calculateAverageSleepAndEmojis(userSleepLogs.chartOne);
        }

        if (chart == "chartTwo") {
            let chartTwoData = [];
            let chartDates = [];
            let chartNotes = [];
            userSleepLogs.chartTwo.dates.forEach((date) => {
                chartDates.push(moment(date).format('MM-DD-YYYY'));
                let dayValue = userSleepLogs.chartTwo.data.find((obj) => obj.date == date);
                if (dayValue) {
                    chartTwoData.push(dayValue.sleep_duration);
                    chartNotes.push(dayValue.sleep_notes);
                } else {
                    chartTwoData.push(0);
                    chartNotes.push('NA');
                }
                //barChartData.push(date);
            })
            barChart.data.labels = chartDates;
            barChart.data.datasets[0].data = chartTwoData;
            barChart.data.datasets[0].notes = chartNotes;
            barChart.update();
            displayAverageAndEmojiCounts(userSleepLogs.chartTwo.emojiCountsTwo);
            // calculateAverageSleepAndEmojis(userSleepLogs.chartTwo);
        }
    }
    function displayAverageAndEmojiCounts(emojiCounts) {
        // Output emoji counts with names
        const emojiContainer = $('#emojiCounts');
        emojiContainer.html(''); // Clear previous content
        for (const emoji in emojiCounts) {
            const { name, count } = emojiCounts[emoji];
            emojiContainer.append(`<li>${emoji} ${count} (${name})</li>`);
            // console.log(`<li>${emoji} ${count} (${name})</li>`)
        }
    }

    // function calculateAverageSleepAndEmojis(chartData) {
    //     const emojiCounts = {
    //         "😡": { name: "Sleep Not Good At All", count: 0 }, // Angry/Not Good at all
    //         "😢": { name: "Poor Sleep", count: 0 },
    //         "😐": { name: "Sleep was just OK", count: 0 },
    //         "😊": { name: "Pretty Good Sleep", count: 0 },
    //         "😴": { name: "Really Good Sleep", count: 0 }
    //     };
    //     let totalSleepThisMonth = 0;
    //     let totalSleepThisWeek = 0;
    //     let daysThisMonth = 0;
    //     let daysThisWeek = 0;
        
    //     const today = moment();
    //     const startOfMonth = today.clone().startOf('month');
    //     const startOfWeek = today.clone().startOf('isoWeek');
        
    //     chartData.dates.forEach((date, index) => {
    //         const dayValue = chartData.data[index];
    //         const logDate = moment(date);
    //         // Count for current month
    //          // Count for current month
    //         if (logDate.isSame(startOfMonth, 'month')) {
    //             daysThisMonth++;
    //             if (dayValue) {
    //                 totalSleepThisMonth += dayValue.sleep_duration; // Assuming sleep_duration is in hours
    //                 // Count emojis based on sleep quality
    //                 switch (dayValue.sleep_quality) {
    //                     case "angry":
    //                         emojiCounts["😡"].count++;
    //                         break;
    //                     case "sad":
    //                         emojiCounts["😢"].count++;
    //                         break;
    //                     case "neutral":
    //                         emojiCounts["😐"].count++;
    //                         break;
    //                     case "happy":
    //                         emojiCounts["😊"].count++;
    //                         break;
    //                     case "really_happy":
    //                         emojiCounts["😴"].count++;
    //                         break;
    //                 }
    //             }
    //         }
    //         if (logDate.isSame(startOfWeek, 'week')) {
    //             daysThisWeek++;
    //             if (dayValue) {
    //                 totalSleepThisWeek += dayValue.sleep_duration; // Assuming sleep_duration is in hours
    //                 // Count emojis based on sleep quality
    //                 switch (dayValue.sleep_quality) {
    //                     case "angry":
    //                         emojiCounts["😡"].count++;
    //                         break;
    //                     case "sad":
    //                         emojiCounts["😢"].count++;
    //                         break;
    //                     case "neutral":
    //                         emojiCounts["😐"].count++;
    //                         break;
    //                     case "happy":
    //                         emojiCounts["😊"].count++;
    //                         break;
    //                     case "really_happy":
    //                         emojiCounts["😴"].count++;
    //                         break;
    //                 }
    //             }
    //         }
    //     });
    //     // displayAverageAndEmojiCounts(emojiCounts);
    // }

</script>
@endsection
