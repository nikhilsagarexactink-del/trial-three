@extends('layouts.app')
@section('head')
    <title>Athlete | View</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php $userType = userType(); @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">

        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.athlete', ['user_type' => $userType]) }}">Manage
                                Athlete</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <div class="left-side mb-2">
                    <!-- Page Title Start -->
                    <h2 class="page-title text-capitalize mb-0">
                        {{ ucfirst($data->first_name . ' ' . $data->last_name) }} Progress
                    </h2>
                    <!-- Page Title End -->
                </div>
                <div class="right-side">
                    <a href="javascript:void(0)" onClick="loginAsUser({{ $data }})">Need to help
                        {{ ucfirst($data->first_name) }}?</a>
                </div>

            </div>
        </div>


        <section class="content white-bg ">
            <div class="container">
                <ul class="nav nav-tabs athlete-tab" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" onClick="getUserGoalLog()" id="waterTracker-tab"
                            data-bs-toggle="tab" data-bs-target="#waterTracker" type="button" role="tab"
                            aria-controls="waterTracker" aria-selected="false">Water Tracker</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="healthTracker-tab" data-bs-toggle="tab" data-bs-target="#healthTracker"
                            type="button" role="tab" aria-controls="healthTracker" aria-selected="false">Health
                            Tracker</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="true">Profile
                            Detail</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="waterTracker" role="tabpanel"
                        aria-labelledby="waterTracker-tab">
                        <div class="container">
                            <div class="card">
                                <div class="card-body water-tracker-card-body">
                                    <div class="row">
                                        <div class="col-md-4 charts active" id="chartOneDiv">
                                            <div class="chart-head">
                                                <canvas class="chart__canvas" id="chartOne" width="160" height="160"
                                                    aria-label="Example doughnut chart showing data as a percentage"
                                                    role="img"></canvas>
                                                <p class="chart-num" id="percOne"></p>
                                            </div>
                                            <p>14 Day Water Tracking Chart</p>
                                        </div>
                                        <div class="col-md-4 charts" id="chartTwoDiv">
                                            <div class="chart-head">
                                                <canvas class="chart__canvas" id="chartTwo" width="160" height="160"
                                                    aria-label="Example doughnut chart showing data as a percentage"
                                                    role="img"></canvas>
                                                <p class="chart-num" id="percTwo"></p>
                                            </div>
                                            <p>30 Day Water Tracking Chart</p>
                                        </div>
                                        <div class="col-md-4 charts" id="chartThreeDiv">
                                            <div class="chart-head">
                                                <canvas class="chart__canvas" id="chartThree" width="160" height="160"
                                                    aria-label="Example doughnut chart showing data as a percentage"
                                                    role="img"></canvas>
                                                <p class="chart-num" id="percThree"></p>
                                            </div>
                                            <p>90 Day Water Tracking Chart</p>
                                        </div>
                                        <div class="col-md-12">
                                            <canvas id="barChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="healthTracker" role="tabpanel" aria-labelledby="healthTracker-tab">
                        <div class="container">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-5 xs-margin-30px-bottom">
                                            <div class="team-single-img mx-fit">
                                                @if (!empty($data->media_id) && !empty($data->media->base_url))
                                                    <img src="{{ $data->media->base_url }}" alt="">
                                                @else
                                                    <img src="{{ asset('assets/images/default-user.jpg') }}"
                                                        alt="">
                                                @endif
                                                <div
                                                    class="bg-light-gray padding-30px-all md-padding-25px-all sm-padding-20px-all">
                                                    <h4
                                                        class="margin-10px-bottom font-size24 md-font-size22 sm-font-size20 font-weight-600">
                                                        {{ ucfirst($data->first_name . ' ' . $data->last_name) }}</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-8 col-md-7">
                                            <div class="team-single-text padding-50px-left sm-no-padding-left">
                                                <div class="contact-info-section margin-40px-tb">
                                                    <h3>Health Markers</h3>
                                                    <ul class="list-style9 no-margin mb-4">
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Next Check In
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker) ? formatDate($healthTracker['markerNextDate'], 'M d, Y') : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Last Check In
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker) ? $healthTracker['markerLastDate'] : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Weight(lbs)
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['marker']) ? $healthTracker['marker']['weight'] . ' LBS' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Body Fat(%)
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['marker']) ? $healthTracker['marker']['body_fat'] . ' %' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">BMI :</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['marker']) ? $healthTracker['marker']['bmi'] . ' %' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Body Water(%)
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['marker']) ? $healthTracker['marker']['body_water'] . ' %' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Skeletal
                                                                        Muscle(%) :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['marker']) ? $healthTracker['marker']['skeletal_muscle'] . ' %' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <h3>Health Measurements</h3>
                                                    <ul class="list-style9 no-margin">
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Next Check In
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker) ? formatDate($healthTracker['measurementNextDate'], 'M d, Y') : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Last Check In
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker) ? $healthTracker['measurementLastDate'] : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">HEIGHT
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['height'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Neck :</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['neck'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Shoulder
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['shoulder'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Chest
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['chest'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Waist
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['waist'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Abdomen
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['abdomen'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Hip :</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['hip'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Bicep L
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['bicep_left'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Bicep R
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['bicep_right'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Thigh L
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['thigh_left'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Thigh R
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['thigh_right'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Calf L
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['calf_left'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Calf R
                                                                        :</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($healthTracker['measurement']) ? $healthTracker['measurement']['calf_right'] . ' inch' : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="container">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-lg-4 col-md-5 xs-margin-30px-bottom">
                                            <div class="team-single-img">
                                                @if (!empty($data->media_id) && !empty($data->media->base_url))
                                                    <img src="{{ $data->media->base_url }}" alt="">
                                                @else
                                                    <img src="{{ asset('assets/images/default-user.jpg') }}"
                                                        alt="">
                                                @endif
                                            </div>
                                            <div
                                                class="bg-light-gray padding-30px-all md-padding-25px-all sm-padding-20px-all">
                                                <h4
                                                    class="margin-10px-bottom font-size24 md-font-size22 sm-font-size20 font-weight-600">
                                                    {{ ucfirst($data->first_name . ' ' . $data->last_name) }}</h4>
                                            </div>
                                        </div>

                                        <div class="col-lg-8 col-md-7">
                                            <div class="team-single-text padding-50px-left sm-no-padding-left">
                                                <div class="contact-info-section margin-40px-tb">
                                                    <ul class="list-style9 no-margin">
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Email:</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ $data->email }}</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Age:</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($data->age) ? $data->age : '-' }}</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Gender:</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($data->gender) ? ucfirst($data->gender) : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Favorite
                                                                        Sport:</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    @if (!empty($data->sports))
                                                                        <p>
                                                                            @php
                                                                                $sports = [];
                                                                                foreach ($data->sports as $sport) {
                                                                                    if (!empty($sport->sport)) {
                                                                                        $sports[] = $sport->sport->name;
                                                                                    }
                                                                                }
                                                                                echo implode(', ', $sports);
                                                                            @endphp
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </li>
                                                        @if (!empty($data->favorite_sport))
                                                            <li>
                                                                <div class="row">
                                                                    <div class="col-md-5 col-5"><strong
                                                                            class="margin-10px-left text-green">Favorite
                                                                            Sport Play Years:</strong></div>
                                                                    <div class="col-md-7 col-7">
                                                                        <p>{{ !empty($data->favorite_sport_play_years) ? $data->favorite_sport_play_years : '-' }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">School
                                                                        Name:</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($data->school_name) ? ucfirst($data->school_name) : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Grade:</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($data->grade) ? $data->grade : '-' }}</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Country:</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($data->country) ? ucfirst($data->country) : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">State:</strong>
                                                                </div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($data->state) ? ucfirst($data->state) : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Zip
                                                                        Code:</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($data->zip_code) ? $data->zip_code : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-5 col-5"><strong
                                                                        class="margin-10px-left text-green">Favorite
                                                                        Athlete:</strong></div>
                                                                <div class="col-md-7 col-7">
                                                                    <p>{{ !empty($data->favorite_athlete) ? ucfirst($data->favorite_athlete) : '-' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <script>
        let userGoal = {};
        let userGoalLogs = [];

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
                                url = url + '?date=' + labelDate;
                                setTimeout(function() {
                                    window.location.href = url;
                                }, 500)
                            }
                        })
                    }
                }
            }
        });

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

        function getUserGoalLog() {
            $.ajax({
                type: "GET",
                url: "{{ route('common.waterTracker.getGoalLog') }}",
                data: {
                    userId: "{{ $data->id }}"
                },
                success: function(response) {
                    if (response.success) {
                        userGoalLogs = response.data.userGoalLogs;
                        calculateDonutChartPercent('chartOne', userGoalLogs);
                    }
                }
            })
        }

        function calculateDonutChartPercent(chart, data) {
            $('.charts').removeClass('active');
            if (data.chartOne) {
                totalGoal = data.chartOne.totalGoal;
                let chartOneData = [data.chartOne.totalWaterValues, totalGoal];
                let chartOnePercent = data.chartOne.percent;
                initializeChartOne(chartOneData, chartOnePercent);
                $('#chartOneDiv').addClass('active');
                calculateLogs("chartOne");
            }
            if (data.chartTwo) {
                totalGoal = data.chartTwo.totalGoal;
                let chartTwoData = [data.chartTwo.totalWaterValues, totalGoal];
                let chartTwoPercent = data.chartTwo.percent;
                initializeChartTwo(chartTwoData, chartTwoPercent);
                $('#chartTwoDiv').addClass('active');
            }
            if (data.chartThree) {
                totalGoal = data.chartThree.totalGoal;
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
                userGoalLogs.chartTwo.dates.forEach((date) => {
                    let dayValue = userGoalLogs.chartTwo.data.find((obj) => obj.date == date);
                    if (dayValue) {
                        chartTwoData.push(dayValue.water_value);
                    } else {
                        chartTwoData.push(0);
                    }
                    //barChartData.push(date);
                })
                barChart.data.labels = userGoalLogs.chartTwo.dates;
                barChart.data.datasets[0].data = chartTwoData;
                barChart.update();
            }

            if (chart == "chartThree") {
                let chartThreeData = [];
                userGoalLogs.chartThree.dates.forEach((date) => {
                    let dayValue = userGoalLogs.chartThree.data.find((obj) => obj.date == date);
                    if (dayValue) {
                        chartThreeData.push(dayValue.water_value);
                    } else {
                        chartThreeData.push(0);
                    }
                    //barChartData.push(date);
                })
                barChart.data.labels = userGoalLogs.chartThree.dates;
                barChart.data.datasets[0].data = chartThreeData;
                barChart.update();
            }
        }
        getUserGoalLog();
    </script>
@endsection
