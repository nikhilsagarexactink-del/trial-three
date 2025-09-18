@extends('layouts.app')
<title>Speed</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $activeTab = '';
        $tabsStatus = ['10_yard', '40_yard', '50_yard', '60_yard', '60_feet', '80_feet', '90_feet', '1_mile', 'custom'];
        foreach ($settings as $key => $setting) {
            if (in_array($key, $tabsStatus) && empty($activeTab) && $settings[$key] == 'enabled') {
                $activeTab = $key;
            }
        }
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Speed</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Speed
                </h2>
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
                <a href="{{ route('user.speedUserForm', ['user_type' => $userType]) }}"
                    class="btn btn-secondary btn-orange ripple-effect-dark text-white">
                    INPUT RESULT
                </a>
                <a class="ms-2 btn btn-primary" href="{{ route('user.speedSettings', ['user_type' => $userType]) }}">
                    Settings <i class="fa fa-cog" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <!--Header Text start-->
        <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm" ></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!-- Header text End -->
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
                    $settings['custom'] == 'enabled'))
            <section class="health-tab">
                <ul class="nav nav-tabs athlete-tab" style="margin:0;" id="myTab" role="tablist">
                    @if ($settings['10_yard'] == 'enabled')
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

            </section>
        @else
            <div class="alert alert-danger" role="alert">
                You need to configure your speed settings, please <a
                    href="{{ route('user.speedSettings', ['user_type' => $userType]) }}">Click
                    Here </a> to get started.
            </div>
        @endif

        <!-- Modal -->
        <div class="modal fade" id="timeListModal" tabindex="-1" role="dialog" aria-labelledby="timeListModal"
            aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Running Time (<span id="selectedTabTitle"></span>)
                        </h5>
                        <button type="button" onClick="closeModal()" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group" id="runningTimeList">

                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onClick="closeModal()" class="btn btn-secondary"
                            data-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
@section('js')
    <script>
        loadHeaderText('speed');
        let settings = @json($settings);
        let selectedTab = '';
        let currentTabData = [];
        let barChartData = [];
        let barChartOne = '';
        let barChartTwo = '';
        let barChartThree = '';
        let barChartFour = '';
        let barChartFive = '';
        let barChartSix = '';
        let barChartSeven = '';
        let barChartEight = '';
        let barChartCustom = '';
        let chartOneDateObj = {};
        let chartTwoDateObj = {};
        loadSpeedData('{{ $activeTab }}');

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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartOneDateObj[labelDate] && chartOneDateObj[labelDate].length) {
                                let liHtml = ``;
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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartTwoDateObj[labelDate] && chartTwoDateObj[labelDate].length) {
                                let liHtml = ``;
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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartThreeDateObj[labelDate] && chartThreeDateObj[labelDate]
                                .length) {
                                let liHtml = ``;
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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartFourDateObj[labelDate] && chartFourDateObj[labelDate]
                                .length) {
                                let liHtml = ``;
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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartFiveDateObj[labelDate] && chartFiveDateObj[labelDate]
                                .length) {
                                let liHtml = ``;
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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartSixDateObj[labelDate] && chartSixDateObj[labelDate].length) {
                                let liHtml = ``;
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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartSevenDateObj[labelDate] && chartSevenDateObj[labelDate]
                                .length) {
                                let liHtml = ``;
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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartEightDateObj[labelDate] && chartEightDateObj[labelDate]
                                .length) {
                                let liHtml = ``;
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
                                let labelDate = data.labels[tooltipItem.index];
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
                            let labelDate = chartData[0]._model.label;
                            if (labelDate && chartCustomDateObj[labelDate] && chartCustomDateObj[labelDate]
                                .length) {
                                let liHtml = ``;
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

        function calculateData(data) {
            let barData = {
                dates: {},
                labels: [],
                datasets: [],
                backgroundColors: []
            };
            let finalDateArr = [];
            let dateObj = {};
            data.forEach((obj) => {
                if (!dateObj[obj.date]) {
                    dateObj[obj.date] = [];
                    dateObj[obj.date].push(obj.running_time);
                } else {
                    dateObj[obj.date].push(obj.running_time);
                }
            })

            Object.keys(dateObj).forEach((date) => {
                let timeObj = {};
                let minutes = 0;
                let seconds = 0;
                let avgTime = 0;
                if (dateObj[date].length > 1) {
                    dateObj[date].forEach((runTime) => {
                        let splitTime = runTime.split('.');
                        minutes += splitTime.length ? parseInt(splitTime[0]) : 0;
                        let secondsValue = splitTime.length > 1 ? parseInt(splitTime[1]) : 0;
                        if (secondsValue > 0) {
                            seconds += secondsValue;
                        }
                    });

                    if (seconds > 0) {
                        let s1 = seconds / 60;
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
                    distance: distance
                },
                success: function(response) {
                    $("#chart_msg_" + distance).html('');
                    if (response.success) {
                        if (response.data.length) {
                            $("#chart_" + distance).show();
                            if (distance == '10_yard') {
                                let chartOneData = calculateData(response.data);
                                chartOneDateObj = chartOneData.dates;
                                barChartOne.data.labels = chartOneData.labels;
                                barChartOne.data.datasets[0].data = chartOneData.datasets;
                                barChartOne.data.datasets[0].backgroundColor = chartOneData.backgroundColors;
                                barChartOne.update();
                            } else if (distance == '40_yard') {
                                let chartTwoData = calculateData(response.data);
                                chartTwoDateObj = chartTwoData.dates;
                                barChartTwo.data.labels = chartTwoData.labels;
                                barChartTwo.data.datasets[0].data = chartTwoData.datasets;
                                barChartTwo.data.datasets[0].backgroundColor = chartTwoData.backgroundColors;
                                barChartTwo.update();
                            } else if (distance == '50_yard') {
                                let chartThreeData = calculateData(response.data);
                                chartThreeDateObj = chartThreeData.dates;
                                barChartThree.data.labels = chartThreeData.labels;
                                barChartThree.data.datasets[0].data = chartThreeData.datasets;
                                barChartThree.data.datasets[0].backgroundColor = chartThreeData
                                    .backgroundColors;
                                barChartThree.update();
                            } else if (distance == '60_yard') {
                                let chartFourData = calculateData(response.data);
                                chartFourDateObj = chartFourData.dates;
                                barChartFour.data.labels = chartFourData.labels;
                                barChartFour.data.datasets[0].data = chartFourData.datasets;
                                barChartFour.data.datasets[0].backgroundColor = chartFourData.backgroundColors;
                                barChartFour.update();
                            } else if (distance == '60_feet') {
                                let chartFiveData = calculateData(response.data);
                                chartFiveDateObj = chartFiveData.dates;
                                barChartFive.data.labels = chartFiveData.labels;
                                barChartFive.data.datasets[0].data = chartFiveData.datasets;
                                barChartFive.data.datasets[0].backgroundColor = chartFiveData.backgroundColors;
                                barChartFive.update();
                            } else if (distance == '80_feet') {
                                let chartSixData = calculateData(response.data);
                                chartSixDateObj = chartSixData.dates;
                                barChartSix.data.labels = chartSixData.labels;
                                barChartSix.data.datasets[0].data = chartSixData.datasets;
                                barChartSix.data.datasets[0].backgroundColor = chartSixData.backgroundColors;
                                barChartSix.update();
                            } else if (distance == '90_feet') {
                                let chartSevenData = calculateData(response.data);
                                chartSevenDateObj = chartSevenData.dates;
                                barChartSeven.data.labels = chartSevenData.labels;
                                barChartSeven.data.datasets[0].data = chartSevenData.datasets;
                                barChartSeven.data.datasets[0].backgroundColor = chartSevenData
                                    .backgroundColors;
                                barChartSeven.update();
                            } else if (distance == '1_mile') {
                                let chartEightData = calculateData(response.data);
                                chartEightDateObj = chartEightData.dates;
                                barChartEight.data.labels = chartEightData.labels;
                                barChartEight.data.datasets[0].data = chartEightData.datasets;
                                barChartEight.data.datasets[0].backgroundColor = chartEightData
                                    .backgroundColors;
                                barChartEight.update();
                            } else if (distance == 'custom') {
                                currentTabData = response.data;
                                let chartCustomData = calculateData(response.data);
                                chartCustomDateObj = chartCustomData.dates;
                                barChartCustom.data.labels = chartCustomData.labels;
                                barChartCustom.data.datasets[0].data = chartCustomData.datasets;
                                barChartCustom.data.datasets[0].backgroundColor = chartCustomData
                                    .backgroundColors;
                                barChartCustom.update();
                            }
                        } else {
                            $("#chart_msg_" + distance).html(`
                            <div class="text-center" role="alert">
                                <a href="{{ route('user.speedUserForm', ['user_type' => $userType]) }}" class="btn btn-secondary btn-orange ripple-effect-dark text-white">No Speed Results to Report, Ready to add one?</a>
                            </div>
                        `);
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
@endsection
