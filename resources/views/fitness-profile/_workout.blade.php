@php
$userType = userType(); @endphp
@if (!empty($completeWorkoutReward) && (count($data) > 0 && $todayPendingWorkout != 0))
    <div class="alert alert-secondary" role="alert">Complete a workout and earn {{ $completeWorkoutReward->point }}
        rewards points.</div>
@endif
<div class="card fitness-box">
    <div class="card-body today-card">
        <div class="fitness-date">{{ date('m-d-Y') }}
            @if (count($data) > 0 && $todayPendingWorkout == 0)
                <span class="text-success today-completed">Great Job Today!</span>
            @endif
        </div>

        <div class="row">
            @if ((!empty($data) && count($data) > 0) || (!empty($customExercises) && count($customExercises) > 0))
                <div class="row">
                    <div class="col-md-1 col-xxl-2">
                        <div class="fitness-vt">
                            <h4>
                                Today
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-7 col-xxl-5">
                        <div class="d-flex ">
                            <ul class="list-unstyled exercise-time">
                                @foreach ($data as $key => $detail)
                                    <li>
                                        <div class="exercise-time-box">
                                            <img src="{{ asset('assets/images/default-health.png') }}">
                                            <span>{{ str_replace('_', ' ', $detail['exercise']) }}</span>
                                            @if (!empty($detail['duration']))
                                                <h6>{{ $detail['duration'] }} MINUTES</h6>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <ul class="list-unstyled fitness-task justify-content-between">
                                @foreach ($data as $key => $detail)
                                    <li>
                                        @if ($detail['is_completed'] == 1)
                                            <span data-toggle="tooltip" data-placement="top"
                                                title="{{ $detail['note'] }}">{{ $detail['completed_time_txt'] }}</span>
                                        @else
                                            @if ($detail['type'] != 'session')
                                                <button type="button" class="btn btn-start start-btn"
                                                    dataIndex="{{ $key }}" data="{{ $detail }}"
                                                    id="start_btn_{{ $detail['id'] }}"
                                                    onclick="loadTrainingVideo('{{ str_replace('_', ' ', $detail['exercise']) }}')">Start</button>
                                            @endif
                                            <button type="button" class="btn btn-start  mark-complete"
                                                {{ $detail['type'] != 'session' ? 'disabled' : '' }}
                                                id="mark_complete_btn_{{ $detail['id'] }}"
                                                onClick="markAsComplete({{ $detail }})">Mark Complete</button>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!--Start custom sets group-->
                        @foreach ($customExercises as $exerciseKey => $exercise)
                            <span style="font-weight: bold;">{{ $exercise['workout'] }}</span></br>
                            @foreach ($exercise['sets'] as $setKey => $sets)
                                @php $count = 0; @endphp
                                @if ($count == 0)
                                    <span>Set {{ !empty($sets) ? '(' . $sets[0]['set_no'] . ')' : '' }}</span>
                                @endif
                                <div class="d-flex justify-content-between">
                                    <ul class="list-unstyled exercise-time">
                                        @foreach ($sets as $setExKey => $exercise)
                                            @php $count++; @endphp
                                            <li>
                                                <div class="exercise-time-box">
                                                    <img src="{{ asset('assets/images/default-health.png') }}">
                                                    <span>{{ $exercise['exercise'] }}</span>
                                                    <h6>{{ !empty($exercise['duration']) ? $exercise['duration'] : 0 }}
                                                        MINUTES</h6>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <ul class="list-unstyled fitness-task justify-content-between">
                                        @foreach ($sets as $setExKey => $exercise)
                                            <li>
                                                @if ($exercise['is_completed'] == 1)
                                                    <span data-toggle="tooltip" data-placement="top"
                                                        title="{{ $exercise['note'] }}">{{ $exercise['completed_time_txt'] }}</span>
                                                @else
                                                    <button type="button" class="btn btn-start start-btn"
                                                        dataIndex="{{ $setExKey }}" data="{{ $exercise }}"
                                                        id="start_btn_{{ $exercise['id'] }}"
                                                        onclick="loadTrainingVideo('{{ str_replace('_', ' ', $exercise['exercise']) }}')">Start</button>
                                                    <button type="button" class="btn btn-start  mark-complete"
                                                        disabled="true" id="mark_complete_btn_{{ $exercise['id'] }}"
                                                        onClick="markAsComplete({{ $exercise }})">Mark
                                                        Complete</button>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        @endforeach
                        <!--End custom sets group-->
                    </div>
                    <div class="col-md-4" id="timerDiv" style="display:none">
                        <div class="d-flex justify-content-center align-items-center timer-clock">
                            <div class="stopwatch ">
                                <div class="clockwrapper">
                                    <div class="outerdot paused"></div>
                                    <div class="exercise-timer">
                                        <h1>READY</h1>
                                        <span class="clock">00 : 00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="controls exercise-btn">
                                <!-- <div class="start btn"><i class="fas fa-play"></i> Start</div>
                        <div class="stop btn"><i class="fas fa-redo-alt"></i> Reset</div> -->
                                <button type="button" class="start btn"><i class="fas fa-play"></i> Start</button>
                                <button type="button" class="stop btn"><i class="fas fa-redo-alt"></i> Reset</button>
                            </div>
                            <div class="from_time" style="display:none"></div>
                            <div class="to_time" style="display:none"></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-md-10 col-xxl-8  text-center mx-auto">
                    <h2 class="text-warning">No Workout Today, Want to Add One?</h2>
                    <a href="{{ route('user.addSettingForm', ['user_type' => $userType]) }}"
                        class="btn btn-outline-warning">Add Workout</a>
                </div>
            @endif
            <div class="col-md-4 ms-auto video-section" id="videoSection"></div>
        </div>
    </div>
    <!-- <a class="btn" href="{{ route('common.openLityPopup') }}" data-lity="">Open File</a> -->
</div>

<div class="card fitness-box">
    <div class="card-body">
        <ul class="this-week-row">
            <li>
                <!-- <img class="" src="{{ url('assets/images/this-week.png') }}"> -->
                <div class="fitness-vt">
                    <h4>
                        This Week
                    </h4>
                </div>
            </li>
            @foreach ($weekData as $key => $week)
                <li>
                    <div class="week-days-head">
                        <span class="week-days">{{ $key }}</span>
                        @if (!empty($week))
                            <ul class="list-unstyled">
                                @foreach ($week as $key => $weekArr)
                                    <li>
                                        <img src="{{ asset('assets/images/default-health.png') }}">
                                        <span
                                            class="font-weight-bold h6">{{ str_replace('_', ' ', $weekArr['exercise']) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <img class="day-off" src="{{ url('assets/images/day-off.png') }}">
                        @endif
                    </div>
                </li>
            @endforeach
            {{-- <li class="edit-schedule">
                <div class="week-setting-container">
                    <a href="{{ route('user.addSettingForm', ['user_type' => $userType]) }}" class="left-link"> View My
                        Workout
                        Calendar</a>
                    <a href="{{ route('user.addSettingForm', ['user_type' => $userType]) }}" class="right-link"> Edit
                        Schedule</a>
                </div>
            </li> --}}
        </ul>
        <div class="week-setting-container mt-2">
            <a href="{{ route('user.addSettingForm', ['user_type' => $userType, 'tab' => 'calendar']) }}"
                class="left-link"> View
                My
                Workout
                Calendar</a>
            <a href="{{ route('user.addSettingForm', ['user_type' => $userType]) }}" class="right-link"> Edit
                Schedule</a>
        </div>
    </div>
    <!--Training Video Modal-->
    <div class="modal fitness-modal fade" id="trainingVideoModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" onClick="hideVideoModal()" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form-group mb-0">
                            <input type="hidden" name="categoryName" id="searchCategoryName" value="">
                            <input type="text" class="form-control" placeholder="Search" name="search"
                                id="searchFiledId">
                            <button type="submit" class="btn btn-secondary ripple-effect"><i
                                    class="fas fa-search"></i></button>
                            <button type="button" class="btn btn-outline-danger ripple-effect"
                                style="margin-left: 10px;" id="clearSearchFilterId">Reset</button>
                        </div>
                    </form>
                    <div id="activityIndicator"></div>
                    <div class="popup-popular-list">
                        <h2 class="most-recent"><b>Favorites</b></h2>
                        <ul class="popup-video-list">
                        </ul>
                    </div>
                    <h2 class="mb-0"><b>Most Recent</b></h2>
                    <div class="modal-body" id="loadVideoList">
                        <div id="paginationLink" class="pb-5 mt-3"></div>
                    </div>
                    <!--Pagination-->
                    <div class="modal-footer">
                        <button type="button" onClick="hideVideoModal()" class="btn btn-secondary"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Training Video Modal-->
    </div>
    <script>
        let currentExercise = {};
        const DEFAULT_THUMBNAIL = "{{ url('assets/images/default-image.png') }}";
        let categoryTag = '';
        let exercises = @json($data);

        function showVideoModal(category) {
            categoryTag = category;
            // Display the category in the modal
            document.getElementById('exampleModalLabel').textContent = 'Category: ' + category;
            document.getElementById('searchCategoryName').value = category;
            $('#trainingVideoModal').modal('show');
            loadTrainingVideo(categoryTag);
        }

        function hideVideoModal() {
            $('#searchFilterForm')[0].reset();
            $('#trainingVideoModal').modal('hide');
        }
        $(document).ready(function() {
            $('.start-btn').on('click', function(e) {
                //console.log("==============44=========88==============");
                //console.log(JSON.parse($(this).attr('data')));
                $('.start-btn').removeClass('btn-primary');
                $('.start-btn').addClass('btn-start');
                // $('#' + this.id).removeClass('btn-start');
                $('#' + this.id).addClass('btn-blue');
                $('#timerDiv').show();
                //let index = $(this).attr('dataIndex');
                //currentExercise = exercises[index];
                currentExercise = JSON.parse($(this).attr('data'));
                resetTimer();
                $('.mark-complete').prop('disabled', true);
                $('#mark_complete_btn_' + currentExercise.id).prop('disabled', false);
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
        // StopWatch Code
        var start = document.querySelector(".start"),
            stop = document.querySelector(".stop"),
            clock = document.querySelector(".clock"),
            from_time = document.querySelector(".from_time"),
            to_time = document.querySelector(".to_time"),
            seconds = document.querySelector(".outerdot"),
            timerState = "stopped", //Clock is either stopped, paused, or running
            startTime, elapsed, timer;

        function resetTimer() {
            cancelAnimationFrame(timer);
            from_time.innerHTML = "00:00";
            to_time.innerHTML = "00:00";
            timerState = "stopped";
            seconds.classList.remove("paused", "running");
            //this.classList.add("disabled");
            start.innerHTML = '<i class="fas fa-play"></i> Start';
            clock.innerHTML = "00:00";
        }
        // Update Time
        function updateTime() {
            timer = requestAnimationFrame(updateTime);
            elapsed = new Date(Date.now() - startTime);
            // Total elapsed time in milliseconds
            var totalMilliseconds = elapsed.getTime();
            // Calculate minutes and seconds
            var mins = Math.floor(totalMilliseconds / (1000 * 60));
            var secs = Math.floor((totalMilliseconds % (1000 * 60)) / 1000);
            // Add leading zeros if necessary
            mins = mins < 10 ? "0" + mins : mins;
            secs = secs < 10 ? "0" + secs : secs;
            clock.innerHTML = mins + ":" + secs;
        };

        function stopTimer() {
            var fromTime = $('.from_time').text();
            var toDate = new Date();
            var toTime = toDate.getHours() + ":" + toDate.getMinutes() + ":" + toDate.getSeconds();
            if (timerState == "running") {
                cancelAnimationFrame(timer);
                timerState = "paused";
                seconds.classList.add("paused");
                stop.classList.remove("disabled");
                to_time.innerHTML = toTime;
                start.innerHTML = '<i class="fas fa-play"></i> Resume';
            } else {
                to_time.innerHTML = toTime;
            }
        }
        // Start Timer
        if (start) {
            start.addEventListener("click", function() {
                if (timerState == "stopped") {
                    //console.log("===============44=============");
                    startTime = Date.now();
                    var fromDate = new Date();
                    var fromTime = fromDate.getHours() + ":" + fromDate.getMinutes() + ":" + fromDate.getSeconds();
                    from_time.innerHTML = fromTime;
                    timer = requestAnimationFrame(updateTime);
                    timerState = "running";
                    seconds.classList.remove("paused");
                    seconds.classList.add("running");
                    start.innerHTML = '<i class="fas fa-stop"></i> Stop';
                } else if (timerState == "running") {
                    //console.log("===============11=============");
                    stopTimer();
                    // cancelAnimationFrame(timer);
                    // timerState = "paused";
                    // seconds.classList.add("paused");
                    // stop.classList.remove("disabled");
                    // var toDate = new Date();
                    // var toTime = toDate.getHours() + ":" + toDate.getMinutes() + ":" + toDate.getSeconds();
                    // to_time.innerHTML = toTime;
                    // start.innerHTML = '<i class="fas fa-play"></i> Resume';
                    bootbox.confirm('Are you sure you want to log the time ?', function(result) {

                        if (result) {
                            markAsComplete(currentExercise, false);
                        }
                    })
                } else if (timerState == "paused") {
                    console.log("===============22=============");
                    startTime = Date.now() - elapsed;
                    timer = requestAnimationFrame(updateTime);
                    timerState = "running";
                    seconds.classList.remove("paused");
                    seconds.classList.add("running");
                    stop.classList.add("disabled");
                    start.innerHTML = '<i class="fas fa-stop"></i> Stop';
                }
            })
        }
        //Stop Time
        if (stop) {
            stop.addEventListener("click", function() {
                if (!this.classList.contains("disabled")) {
                    console.log("===============55=============");
                    resetTimer();
                }

            });
        }
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        $('#searchFilterForm').on('submit', function(e) {
            $("#searchFilter").toggleClass("open");
            loadTrainingVideo(categoryTag);
        });
        /**
         * Clear search filter.
         */
        $('#clearSearchFilterId').on('click', function(e) {
            $('#searchFilterForm')[0].reset();
            //$('.selectpicker').selectpicker('refresh')
            loadTrainingVideo(categoryTag);
        });

        function loadTrainingVideo(categoryName = '') {
            var category_name = categoryName ? categoryName : $("#searchCategoryName").val();
            const BASE_URL = "{{ url('') }}";
            $("#activityIndicator").html('{{ ajaxListLoader() }}');
            const DEFAULT_THUMBNAIL = "{{ url('assets/images/default-image.png') }}";
            var url = "{{ route('common.loadTrainingVideoForFitness') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    categoryName: category_name,
                    search: $("#searchFiledId").val(),
                },
                success: function(response) {
                    $("#activityIndicator").html('');
                    if (response.success) {
                        var responseData = response.data.results.mostRecent ?? [];
                        var favData = response.data.results.favorites ?? [];
                        appendFavoriteVideo(favData);
                        $("#videoSection").html("");
                        $("#paginationLink").html("");
                        $("#loadVideoList").html("");
                        $("#loadVideoList").append(response.data.html);
                        $('#paginationLink').append(response.data.pagination);
                        if (responseData.length > 0) {
                            var innerHtml =
                                `<span class="category-title">${category_name} Videos</span><ul class="d-flex align-items-center">`;
                            // slice(0, 4) because loop only occur 4 time
                            responseData.slice(0, 4).forEach(function(video, i) {
                                // Check if media and base_url are available, otherwise use a default thumbnail
                                const thumbnailUrl = (video.media && video.media.base_url) ? video.media
                                    .base_url : DEFAULT_THUMBNAIL;
                                innerHtml += `<li class="ms-2">
                                            <a data-lity href="${video.video_url}" title="${video.title}">
                                                <img width="40" height="40" src="${thumbnailUrl}" alt="Video Thumbnail">
                                            </a>
                                        </li>`;
                            });
                            if (responseData.length > 4) {
                                // innerHtml += `<li class="ms-2"><a href="{{ route('common.openLityPopup') }}" data-lity=""  title="View All Videos">View All</a></li>`;
                                innerHtml +=
                                    `<li class="ms-2"><a href="javascript:void(0)"  title="View All Videos" onclick="showVideoModal('${categoryName}')">View All</a></li>`;
                            }
                            innerHtml += '</ul>';
                            $('#videoSection').append(innerHtml);
                        }
                    }
                },
                error: function() {
                    $("#activityIndicator").html('');
                    _toast.error('Somthing went wrong.');
                }
            });
        }

        function appendFavoriteVideo(data) {
            console.log('Favorite Video', data);
            const container = document.querySelector('.popup-video-list');
            container.innerHTML = ''; // Clear existing content
            if (data.length > 0) {
                data.forEach(video => {
                    // Check if media and base_url are available, otherwise use a default thumbnail
                    var thumbnailUrl = (video.media && video.media.base_url) ? video.media.base_url :
                        DEFAULT_THUMBNAIL;
                    const listItem = `
                    <li>
                        <div class="card">
                            <a data-lity="" href="${video.video_url}">
                                <img class="card-img-top" src="${thumbnailUrl}" alt="${video.title}">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a data-lity="" href="${video.video_url}" data-toggle="tooltip" data-placement="top" title="${video.title}">
                                        ${video.title}
                                    </a>
                                </h5>
                            </div>
                        </div>
                    </li>
                `;
                    container.insertAdjacentHTML('beforeend', listItem);
                });
            } else {
                var innerHtml =
                    `<div class="alert alert-danger" role="alert">No favorites selected yet, make sure to add some.</div>`;
                container.insertAdjacentHTML('beforeend', innerHtml);
            }
        }
    </script>
    <style>
        .week-setting-container {
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: #f0f0f0;
        }

        .left-link,
        .right-link {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
    </style>
