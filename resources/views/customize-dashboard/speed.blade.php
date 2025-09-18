@php
        $userType = userType();
        $activeTab = '';
        $tabsStatus = ['10_yard', '40_yard', '50_yard', '60_yard', '60_feet', '80_feet', '90_feet', '1_mile', 'custom'];
        foreach ($settings as $key => $setting) {
            if (in_array($key, $tabsStatus) && empty($activeTab) && $settings[$key] == 'enabled') {
                $activeTab = $key;
            }
        }
        $allDisabled = true; // Assume all are disabled
        foreach ($settings as $key => $setting) {
            if ($setting == 'enabled') {
                $allDisabled = false; // Found at least one enabled
                break;
            }
        }
        if($allDisabled){
            $activeTab = '0_yard';
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
    <h4>
        @if(!$userType != 'parent' && empty($athlete) && empty($athlete_id))
            <a class="text-dark" href="{{ route('user.speed', ['user_type' => $userType]) }}" >
        @else
            <a class="text-dark" href="javascript:void(0)" >
        @endif
            Speed
        </a>
    </h4>
    @if(!$userType != 'parent' && empty($athlete) && empty($athlete_id))
        <a href="{{ route('user.speed', ['user_type' => $userType]) }}" >
    @else
    <a href="javascript:void(0)" >
        @endif
        <div class="card equal-height">
            <div>
                @if (
                    !empty($settings) &&
                        ($settings['10_yard'] == 'enabled' ||
                            $settings['40_yard'] == 'enabled' ||
                            $settings['50_yard'] == 'enabled' ||
                            $settings['60_yard'] == 'enabled' ||
                            $settings['60_feet'] == 'enabled' ||
                            $settings['80_feet'] == 'enabled' ||
                            $settings['90_feet'] == 'enabled' ||
                            $settings['1_mile'] == 'enabled' ||
                            $settings['custom'] == 'enabled' || $allDisabled)
                )
                    <section class="health-tab">
                        <ul class="nav nav-tabs athlete-tab" style="margin:0;" id="myTab" role="tablist">
                            @if ($settings['10_yard'] == 'enabled'|| $activeTab == '10_yard')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '10_yard' ? 'active' : '' }}"
                                        onClick="loadSpeedData('10_yard');" id="chart-one-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-one" type="button" role="tab" aria-controls="chart-one"
                                        aria-selected="false">10 yard</button>
                                </li>
                            @endif
                            @if ($settings['40_yard'] == 'enabled')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '40_yard' ? 'active' : '' }}"
                                        onClick="loadSpeedData('40_yard');" id="chart-two-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-two" type="button" role="tab" aria-controls="chart-two"
                                        aria-selected="false">40 yard </button>
                                </li>
                            @endif
                            @if ($settings['50_yard'] == 'enabled')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '50_yard' ? 'active' : '' }}"
                                        onClick="loadSpeedData('50_yard');" id="chart-three-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-three" type="button" role="tab" aria-controls="chart-three"
                                        aria-selected="false">50 yard </button>
                                </li>
                            @endif
                            @if ($settings['60_yard'] == 'enabled')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '60_yard' ? 'active' : '' }}"
                                        onClick="loadSpeedData('60_yard');" id="chart-four-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-four" type="button" role="tab" aria-controls="chart-four"
                                        aria-selected="false">60 yard </button>
                                </li>
                            @endif
                            @if ($settings['60_feet'] == 'enabled')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '60_feet' ? 'active' : '' }}"
                                        onClick="loadSpeedData('60_feet');" id="chart-five-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-five" type="button" role="tab" aria-controls="chart-five"
                                        aria-selected="false">60 feet </button>
                                </li>
                            @endif
                            @if ($settings['80_feet'] == 'enabled')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '80_feet' ? 'active' : '' }}"
                                        onClick="loadSpeedData('80_feet');" id="chart-six-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-six" type="button" role="tab" aria-controls="chart-six"
                                        aria-selected="false">80 feet </button>
                                </li>
                            @endif
                            @if ($settings['90_feet'] == 'enabled')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '90_feet' ? 'active' : '' }}"
                                        onClick="loadSpeedData('90_feet');" id="chart-seven-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-seven" type="button" role="tab" aria-controls="chart-seven"
                                        aria-selected="false">90 feet </button>
                                </li>
                            @endif
                            @if ($settings['1_mile'] == 'enabled')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '1_mile' ? 'active' : '' }}"
                                        onClick="loadSpeedData('1_mile');" id="chart-eight-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-eight" type="button" role="tab" aria-controls="chart-eight"
                                        aria-selected="false">1 mile </button>
                                </li>
                            @endif
                            @if ($settings['custom'] == 'enabled')
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == 'custom' ? 'active' : '' }}"
                                        onClick="loadSpeedData('custom');" id="chart-nine-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-nine" type="button" role="tab" aria-controls="chart-nine"
                                        aria-selected="false">Custom </button>
                                </li>
                            @endif
                            @if($allDisabled)
                            <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link top-radius font-weight-bold {{ $activeTab == '0_yard' ? 'active' : '' }}"
                                        onClick="loadSpeedData('0_yard');" id="chart-empty-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-empty" type="button" role="tab" aria-controls="chart-empty"
                                        aria-selected="false">0 yard</button>
                            </li>
                            @endif
                        </ul>
                    </section>
                    <section class="content  bottom-radius px-4 py-5 health-chart tab-content" id="myTabContent">
                        @if ($settings['10_yard'] == 'enabled')
                            <div class="tab-pane fade {{ $activeTab == '10_yard' ? 'show active' : '' }}" id="chart-one"
                                role="tabpanel" aria-labelledby="chart-one-tab">
                                <div id="chart_10_yard" style="display:none">
                                    <canvas id="barChartOne"></canvas>
                                </div>
                                <div id="chart_msg_10_yard"></div>
                            </div>
                        @endif
                        @if ($settings['40_yard'] == 'enabled')
                            <div class=" tab-pane fade {{ $activeTab == '40_yard' ? 'show active' : '' }}" id="chart-two"
                                role="tabpanel" aria-labelledby="chart-two-tab">
                                <div id="chart_40_yard" style="display:none">
                                    <canvas id="barChartTwo"></canvas>
                                </div>
                                <div id="chart_msg_40_yard"></div>
                            </div>
                        @endif
                        @if ($settings['50_yard'] == 'enabled')
                            <div class=" tab-pane fade {{ $activeTab == '50_yard' ? 'show active' : '' }}" id="chart-three"
                                role="tabpanel" aria-labelledby="chart-three-tab">
                                <div id="chart_50_yard" style="display:none">
                                    <canvas id="barChartThree"></canvas>
                                </div>
                                <div id="chart_msg_50_yard"></div>
                            </div>
                        @endif
                        @if ($settings['60_yard'] == 'enabled')
                            <div class=" tab-pane fade {{ $activeTab == '60_yard' ? 'show active' : '' }}" id="chart-four"
                                role="tabpanel" aria-labelledby="chart-four-tab">
                                <div id="chart_60_yard" style="display:none">
                                    <canvas id="barChartFour"></canvas>
                                </div>
                                <div id="chart_msg_60_yard"></div>
                            </div>
                        @endif
                        @if ($settings['60_feet'] == 'enabled')
                            <div class=" tab-pane fade {{ $activeTab == '60_feet' ? 'show active' : '' }}" id="chart-five"
                                role="tabpanel" aria-labelledby="chart-five-tab">
                                <div id="chart_60_feet" style="display:none">
                                    <canvas id="barChartFive"></canvas>
                                </div>
                                <div id="chart_msg_60_feet"></div>
                            </div>
                        @endif
                        @if ($settings['80_feet'] == 'enabled')
                            <div class=" tab-pane fade {{ $activeTab == '80_feet' ? 'show active' : '' }}" id="chart-six"
                                role="tabpanel" aria-labelledby="chart-six-tab">
                                <div id="chart_80_feet" style="display:none">
                                    <canvas id="barChartSix"></canvas>
                                </div>
                                <div id="chart_msg_80_feet"></div>
                                <div id="no_data_table_80_feet" style="display:none;">
                                    <canvas id="barChartSix"></canvas>
                                </div>
                            </div>
                        @endif
                        @if ($settings['90_feet'] == 'enabled')
                            <div class=" tab-pane fade {{ $activeTab == '90_feet' ? 'show active' : '' }}" id="chart-seven"
                                role="tabpanel" aria-labelledby="chart-seven-tab">
                                <div id="chart_90_feet" style="display:none">
                                    <canvas id="barChartSeven"></canvas>
                                </div>
                                <div id="chart_msg_90_feet"></div>
                            </div>
                        @endif
                        @if ($settings['1_mile'] == 'enabled')
                            <div class=" tab-pane fade {{ $activeTab == '1_mile' ? 'show active' : '' }}" id="chart-eight"
                                role="tabpanel" aria-labelledby="chart-eight-tab">
                                <div id="chart_1_mile" style="display:none">
                                    <canvas id="barChartEight"></canvas>
                                </div>
                                <div id="chart_msg_1_mile"></div>
                                <div id="no_data_table_1_mile" style="display:none;">
                                    <canvas id="barChartEight"></canvas>
                                </div>
                            </div>
                        @endif
                        @if ($settings['custom'] == 'enabled')
                            <div class=" tab-pane fade {{ $activeTab == 'custom' ? 'show active' : '' }}" id="chart-nine"
                                role="tabpanel" aria-labelledby="chart-nine-tab">
                                <div id="chart_custom" style="display:none">
                                    <canvas id="barChartCustom"></canvas>
                                </div>
                                <div id="chart_msg_custom"></div>
                            </div>
                        @endif
                        @if($allDisabled)
                            <div class="tab-pane fade show active" id="chart-empty"
                                role="tabpanel" aria-labelledby="chart-empty-tab">
                                <div id="chart_0_yard" style="display:none">
                                    <canvas id="barChartEmpty"></canvas>
                                </div>
                                <div id="chart_msg_0_yard"></div>
                            </div>
                        @endif
                    </section>
                @endif
            </div>
        </div>
    </a>                  
</div>
<script>
        var settings = @json($settings);
        var selectedTab = '';
        var currentTabData = [];
        barChartData = [];
        var barChartOne = '';
        var barChartTwo = '';
        var barChartThree = '';
        var barChartFour = '';
        var barChartFive = '';
        var barChartSix = '';
        var barChartSeven = '';
        var barChartEight = '';
        var barChartCustom = '';
        var barChartEmpty = '';
        var chartOneDateObj = {};
        var chartTwoDateObj = {};
        var allDisabled = @json($allDisabled);
        var athlete = @json($athlete);
        loadSpeedData('{{ $activeTab }}');

        // if (!settings || typeof settings !== 'object') {
        //     settings = {}; // Initialize as an empty object if it's not defined
        // }
        
        if (allDisabled) {
            barChartEmpty = new Chart("barChartEmpty", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'No Data Available',
                        data: [],
                        backgroundColor: 'transparent',
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false,
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        enabled: false // Disable tooltips for empty chart
                    }
                }
            });
            $("#chart_0_yard").show(); // Show the empty chart
        }
        if (settings && settings['10_yard'] == 'enabled') {
            barChartOne = new Chart("barChartOne", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: [],
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false,
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartOneDateObj[labelDate] && chartOneDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' seconds';
                                }
                            }
                        }
                    },
                    onClick: function(evt, data) {
                        var chartData = barChartOne.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartOneDateObj[labelDate] && chartOneDateObj[labelDate].length) {
                                var liHtml = ``;
                                chartOneDateObj[labelDate].forEach((time) => {
                                    liHtml += `<li class="list-group-item">${time} Seconds</li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }
                        }
                    }
                }
            });
        }
        if (settings && settings['40_yard'] == 'enabled') {
            barChartTwo = new Chart("barChartTwo", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: [],
                        //barPercentage: 0.5
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false, // Do not draw border for the grid lines
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartTwoDateObj[labelDate] && chartTwoDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' seconds';
                                }
                            }
                        }
                    },

                    onClick: function(evt, data) {
                        var chartData = barChartTwo.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartTwoDateObj[labelDate] && chartTwoDateObj[labelDate].length) {
                                var liHtml = ``;
                                chartTwoDateObj[labelDate].forEach((time) => {
                                    liHtml += `<li class="list-group-item">` + time + ` Seconds</li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }

                        }
                    }
                }
            });
        }
        if (settings && settings['50_yard'] == 'enabled') {
            barChartThree = new Chart("barChartThree", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: [],
                        //barPercentage: 0.5
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false, // Do not draw border for the grid lines
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartThreeDateObj[labelDate] && chartThreeDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' seconds';
                                }
                            }
                        }
                    },
                    // plugins: {
                    //     datalabels: {
                    //         color: 'white',
                    //         font: {
                    //             weight: 'bold'
                    //         }
                    //     }
                    // },
                    onClick: function(evt, data) {
                        var chartData = barChartThree.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartThreeDateObj[labelDate] && chartThreeDateObj[labelDate]
                                .length) {
                                var liHtml = ``;
                                chartThreeDateObj[labelDate].forEach((time) => {
                                    liHtml += `<li class="list-group-item">` + time + ` Seconds</li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }

                        }
                    }
                }
            });
        }
        if (settings && settings['60_yard'] == 'enabled') {
            barChartFour = new Chart("barChartFour", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: [],
                        //barPercentage: 0.5
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            },
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false, // Do not draw border for the grid lines
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartFourDateObj[labelDate] && chartFourDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' seconds';
                                }
                            }
                        }
                    },
                    onClick: function(evt, data) {
                        var chartData = barChartFour.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartFourDateObj[labelDate] && chartFourDateObj[labelDate]
                                .length) {
                                var liHtml = ``;
                                chartFourDateObj[labelDate].forEach((time) => {
                                    liHtml += `<li class="list-group-item">` + time + ` Seconds</li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }

                        }
                    }
                }
            });
        }
        if (settings && settings['60_feet'] == 'enabled') {
            barChartFive = new Chart("barChartFive", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: [],
                        barPercentage: 0.5
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false, // Do not draw border for the grid lines
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartFiveDateObj[labelDate] && chartFiveDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' seconds';
                                }
                            }
                        }
                    },
                    onClick: function(evt, data) {
                        var chartData = barChartFive.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartFiveDateObj[labelDate] && chartFiveDateObj[labelDate]
                                .length) {
                                var liHtml = ``;
                                chartFiveDateObj[labelDate].forEach((time) => {
                                    liHtml += `<li class="list-group-item">` + time + ` Seconds</li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }

                        }
                    }
                }
            });
        }
        if (settings && settings['80_feet'] == 'enabled') {
            barChartSix = new Chart("barChartSix", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: [],
                        barPercentage: 0.5
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false, // Do not draw border for the grid lines
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartSixDateObj[labelDate] && chartSixDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' seconds';
                                }
                            }
                        }
                    },
                    onClick: function(evt, data) {
                        var chartData = barChartSix.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartSixDateObj[labelDate] && chartSixDateObj[labelDate].length) {
                                var liHtml = ``;
                                chartSixDateObj[labelDate].forEach((time) => {
                                    liHtml += `<li class="list-group-item">` + time + ` Seconds</li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }

                        }
                    }
                }
            });
        }
        if (settings && settings['90_feet'] == 'enabled') {
            barChartSeven = new Chart("barChartSeven", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: '#f37121',
                        barPercentage: 0.5
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false, // Do not draw border for the grid lines
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartSevenDateObj[labelDate] && chartSevenDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' seconds';
                                }
                            }
                        }
                    },
                    onClick: function(evt, data) {
                        var chartData = barChartSeven.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartSevenDateObj[labelDate] && chartSevenDateObj[labelDate]
                                .length) {
                                var liHtml = ``;
                                chartSevenDateObj[labelDate].forEach((time) => {
                                    liHtml += `<li class="list-group-item">` + time + ` Seconds</li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }

                        }
                    }
                }
            });
        }
        if (settings && settings['1_mile'] == 'enabled') {
            barChartEight = new Chart("barChartEight", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: [],
                        barPercentage: 0.5
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false, // Do not draw border for the grid lines
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Minutes',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartEightDateObj[labelDate] && chartEightDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' Minutes';
                                }
                            }
                        }
                    },
                    onClick: function(evt, data) {
                        var chartData = barChartEight.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartEightDateObj[labelDate] && chartEightDateObj[labelDate]
                                .length) {
                                var liHtml = ``;
                                chartEightDateObj[labelDate].forEach((time) => {
                                    liHtml += `<li class="list-group-item">` + time + ` Minutes </li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }

                        }
                    }
                }
            });
        }
        if (settings && settings['custom'] == 'enabled') {
            barChartCustom = new Chart("barChartCustom", {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        backgroundColor: [],
                        barPercentage: 0.5
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.05,
                            gridLines: {
                                display: false,
                                color: 'white',
                            },
                            ticks: {
                                fontColor: 'white',
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                color: 'white',
                                drawBorder: false, // Do not draw border for the grid lines
                                zeroLineColor: 'white',
                            },
                            ticks: {
                                min: 0,
                                fontColor: 'white',
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Minutes',
                                fontColor: 'white',
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var labelDate = data.labels[tooltipItem.index];
                                if (chartCustomDateObj[labelDate] && chartCustomDateObj[labelDate].length > 1) {
                                    return 'Multiple Results, Click to view All';
                                } else {
                                    return tooltipItem.yLabel + ' Minutes';
                                }
                            }
                        }
                    },
                    onClick: function(evt, data) {
                        var chartData = barChartCustom.getElementAtEvent(evt);
                        if (chartData && chartData.length) {
                            var labelDate = chartData[0]._model.label;
                            if (labelDate && chartCustomDateObj[labelDate] && chartCustomDateObj[labelDate]
                                .length) {
                                var liHtml = ``;
                                currentTabData.forEach((obj) => {
                                    liHtml += `<li class="list-group-item">` + obj.distance + `-` + obj
                                        .running_time + ` Minutes</li>`;
                                })
                                $('#selectedTabTitle').html(selectedTab);
                                $('#runningTimeList').html(liHtml);
                                $('#timeListModal').modal('show');
                            }

                        }
                    }
                }
            });
        }
        function initializeDefaultChart(chart, chartId) {
            if (chart) { // Check if the chart is defined
                const defaultLabels = []; 
                const defaultData = Array(10).fill(10); // Create an array of 10 with value 10

                chart.data.labels = defaultLabels;
                chart.data.datasets[0].data = defaultData;
                chart.data.datasets[0].backgroundColor = 'transparent'; 
                chart.update();
            } else {
                console.error(`Chart with ID ${chartId} is not defined.`);
            }
        }
        

        function calculateData(data) {
            var barData = {
                dates: {},
                labels: [],
                datasets: [],
                backgroundColors: []
            };
            var finalDateArr = [];
            var dateObj = {};
            data.forEach((obj) => {
                if (!dateObj[obj.date]) {
                    dateObj[obj.date] = [];
                    dateObj[obj.date].push(obj.running_time);
                } else {
                    dateObj[obj.date].push(obj.running_time);
                }
            })

            Object.keys(dateObj).forEach((date) => {
                var timeObj = {};
                var minutes = 0;
                var seconds = 0;
                var avgTime = 0;
                if (dateObj[date].length > 1) {
                    dateObj[date].forEach((runTime) => {
                        var splitTime = runTime.split('.');
                        minutes += splitTime.length ? parseInt(splitTime[0]) : 0;
                        var secondsValue = splitTime.length > 1 ? parseInt(splitTime[1]) : 0;
                        if (secondsValue > 0) {
                            seconds += secondsValue;
                        }
                    });

                    if (seconds > 0) {
                        var s1 = seconds / 60;
                        seconds = s1 >= 1 ? s1 : 0;
                        minutes += seconds >= 1 ? seconds : 0;
                    }
                    avgTime = (minutes / dateObj[date].length).toFixed(2);
                    barData.backgroundColors.push('red'); // Red for multiple values
                } else {
                    avgTime = dateObj[date].length ? dateObj[date][0] : 0;
                    barData.backgroundColors.push('#f37121'); // Orange for single value
                }
                finalDateArr.push(avgTime);
            })
            barData.labels = Object.keys(dateObj);
            barData.datasets = finalDateArr;
            barData.dates = dateObj;
            return barData;
        }

        function loadSpeedData(distance = '10_yard') {
            $("#chart_" + distance).hide();
            selectedTab = distance.replace('_', ' ');
            currentTabData = [];
            $("#chart_msg_" + distance).html('<div class="text-center">{{ ajaxListLoader() }}</div>');
            $.ajax({
                type: "GET",
                url: "{{ route('common.loadSpeedData') }}",
                data: {
                    distance: distance,
                    athlete_id : @json($athlete_id),
                },
                success: function(response) {
                    $("#chart_msg_" + distance).html('');
                    if (response.success) {
                        if (response.data.length) {
                            $("#chart_" + distance).show();
                            if (distance == '10_yard') {
                                var chartOneData = calculateData(response.data);
                                chartOneDateObj = chartOneData.dates;
                                barChartOne.data.labels = chartOneData.labels;
                                barChartOne.data.datasets[0].data = chartOneData.datasets;
                                barChartOne.data.datasets[0].backgroundColor = chartOneData.backgroundColors;
                                barChartOne.update();
                            } else if (distance == '40_yard') {
                                var chartTwoData = calculateData(response.data);
                                chartTwoDateObj = chartTwoData.dates;
                                barChartTwo.data.labels = chartTwoData.labels;
                                barChartTwo.data.datasets[0].data = chartTwoData.datasets;
                                barChartTwo.data.datasets[0].backgroundColor = chartTwoData.backgroundColors;
                                barChartTwo.update();
                            } else if (distance == '50_yard') {
                                var chartThreeData = calculateData(response.data);
                                chartThreeDateObj = chartThreeData.dates;
                                barChartThree.data.labels = chartThreeData.labels;
                                barChartThree.data.datasets[0].data = chartThreeData.datasets;
                                barChartThree.data.datasets[0].backgroundColor = chartThreeData
                                    .backgroundColors;
                                barChartThree.update();
                            } else if (distance == '60_yard') {
                                var chartFourData = calculateData(response.data);
                                chartFourDateObj = chartFourData.dates;
                                barChartFour.data.labels = chartFourData.labels;
                                barChartFour.data.datasets[0].data = chartFourData.datasets;
                                barChartFour.data.datasets[0].backgroundColor = chartFourData.backgroundColors;
                                barChartFour.update();
                            } else if (distance == '60_feet') {
                                var chartFiveData = calculateData(response.data);
                                chartFiveDateObj = chartFiveData.dates;
                                barChartFive.data.labels = chartFiveData.labels;
                                barChartFive.data.datasets[0].data = chartFiveData.datasets;
                                barChartFive.data.datasets[0].backgroundColor = chartFiveData.backgroundColors;
                                barChartFive.update();
                            } else if (distance == '80_feet') {
                                var chartSixData = calculateData(response.data);
                                chartSixDateObj = chartSixData.dates;
                                barChartSix.data.labels = chartSixData.labels;
                                barChartSix.data.datasets[0].data = chartSixData.datasets;
                                barChartSix.data.datasets[0].backgroundColor = chartSixData.backgroundColors;
                                barChartSix.update();
                            } else if (distance == '90_feet') {
                                var chartSevenData = calculateData(response.data);
                                chartSevenDateObj = chartSevenData.dates;
                                barChartSeven.data.labels = chartSevenData.labels;
                                barChartSeven.data.datasets[0].data = chartSevenData.datasets;
                                barChartSeven.data.datasets[0].backgroundColor = chartSevenData
                                    .backgroundColors;
                                barChartSeven.update();
                            } else if (distance == '1_mile') {
                                var chartEightData = calculateData(response.data);
                                chartEightDateObj = chartEightData.dates;
                                barChartEight.data.labels = chartEightData.labels;
                                barChartEight.data.datasets[0].data = chartEightData.datasets;
                                barChartEight.data.datasets[0].backgroundColor = chartEightData
                                    .backgroundColors;
                                barChartEight.update();
                            } else if (distance == 'custom') {
                                currentTabData = response.data;
                                var chartCustomData = calculateData(response.data);
                                chartCustomDateObj = chartCustomData.dates;
                                barChartCustom.data.labels = chartCustomData.labels;
                                barChartCustom.data.datasets[0].data = chartCustomData.datasets;
                                barChartCustom.data.datasets[0].backgroundColor = chartCustomData
                                    .backgroundColors;
                                barChartCustom.update();
                            }
                        } else {
                            $("#chart_" + distance).show();
                            
                                if (distance === '10_yard') {
                                    initializeDefaultChart(barChartOne, "barChartOne");
                                } else if (distance === '40_yard') {
                                    initializeDefaultChart(barChartTwo, "barChartTwo");
                                } else if (distance === '50_yard') {
                                    initializeDefaultChart(barChartThree, "barChartThree");
                                } else if (distance === '60_yard') {
                                    initializeDefaultChart(barChartFour, "barChartFour");
                                } else if (distance === '60_feet') {
                                    initializeDefaultChart(barChartFive, "barChartFive");
                                } else if (distance === '80_feet') {
                                    initializeDefaultChart(barChartSix, "barChartSix");
                                } else if (distance === '90_feet') {
                                    initializeDefaultChart(barChartSeven, "barChartSeven");
                                } else if (distance === '1_mile') {
                                    initializeDefaultChart(barChartEight, "barChartEight");
                                } else if (distance === 'custom') {
                                    initializeDefaultChart(barChartCustom, "barChartCustom");
                                } else if (distance === "0_yard") {
                                    initializeDefaultChart(barChartEmpty, "barChartEmpty");
                                }
                            
                            
                            
                        //     $("#chart_msg_" + distance).html(`
                        //     <div class="text-center" role="alert">
                        //         <a href="{{ route('user.speedUserForm', ['user_type' => $userType]) }}" class="btn btn-secondary btn-orange ripple-effect-dark text-white">No Speed Results to Report, Ready to add one?</a>
                        //     </div>
                        // `);
                        }
                    }
                },
                error: function(err) {
                    $("#chart_msg_" + distance).html('');
                }
            })
        }

        function closeModal() {
            $('#timeListModal').modal('hide');
        }
</script>
