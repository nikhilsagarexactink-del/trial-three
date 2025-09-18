@extends('layouts.app')
<title>Fitness Profile</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $customExerciseIds = [];
        $tabType = request('tab');
        $weekDays = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">

        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side w-100">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.fitnessProfile', ['user_type' => $userType]) }}">Fitness Profile</a>
                        </li>
                        <li class="breadcrumb-item active">Workout Settings</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0 d-flex justify-content-between align-items-center">
                    Fitness Profile Workout Settings
                    @if (!empty($settings['fp-workout-sample-image']) && !empty($settings['fp-workout-sample-image-url']))
                        <!-- <a href="javascript:void(0)" class="plan-link" data-lity data-lity-target="{{ $settings['fp-workout-sample-image-url'] }}">(SEE A SAMPLE WORKOUT PLAN)</a> -->
                        <a href="javascript:void(0)" class="plan-link" data-lity
                            data-lity-target="{{ $settings['fp-workout-sample-image-url'] }}">SEE A SAMPLE WORKOUT
                            PLAN</a>
                    @endif
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <!-- filter section start -->
        <ul class="nav nav-tabs athlete-tab m-0" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $tabType !== 'calendar' ? ' show active' : '' }}" id="settings-tab"
                    data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings"
                    aria-selected="false" onClick="changeTab('settings')">Settings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $tabType == 'calendar' ? ' show active' : '' }}" id="calendarView-tab"
                    data-bs-toggle="tab" data-bs-target="#calendarView" type="button" role="tab"
                    aria-controls="calendarView" aria-selected="false" onClick="changeTab('calendar')">Calendar</button>
            </li>
        </ul>
        <!-- filter section start -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade {{ $tabType !== 'calendar' ? ' show active' : '' }}" id="settings" role="tabpanel"
                aria-labelledby="settings-tab">
                <div class="card">
                    <div class="card-body">
                        <section class="">
                            <h5>WORKOUT SCHEDULE BUILDER</h5>
                            <form id="finessSettingForm" class="day-workout">
                                <div class="row">
                                    @if (!empty($exercises) && count($exercises) > 0)
                                        @foreach ($exercises as $dayKey => $exerciseData)
                                            @php
                                                $customExercises = array_filter($data, function ($item) use ($dayKey) {
                                                    if (
                                                        $item['day'] == $dayKey &&
                                                        ($item['type'] == 'custom' || $item['type'] == 'session')
                                                    ) {
                                                        return true;
                                                    }

                                                    return false;
                                                });
                                                $customExercises = array_values($customExercises);
                                            @endphp
                                            <div class="col-md-12 white-bg mb-4">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <span class="day-text">{{ $dayKey }}</span>
                                                    <div>
                                                        <a class="btn-secondary btn ms-3" href="javascript:void(0)"
                                                            onClick="openCustomExerciseModal('{{ $dayKey }}')">Add New
                                                            Session</a>
                                                    </div>
                                                </div>
                                                <ul class="list-unstyled row" id="{{ $dayKey }}_ul">
                                                    @if (!empty($exerciseData) && count($exerciseData) > 0)
                                                        @foreach ($exerciseData as $key => $exercice)
                                                            @php
                                                                $durations = !empty($exercice['durations'])
                                                                    ? explode(',', $exercice['durations'])
                                                                    : [];
                                                                $chkFieldName = $dayKey . '' . '[static_exercise_id][]';
                                                                $durationFieldName =
                                                                    $dayKey .
                                                                    '' .
                                                                    '[static_duration][' .
                                                                    $exercice['id'] .
                                                                    ']';
                                                                $title = $exercice['title'];
                                                                $exerciseData = array_filter($data, function (
                                                                    $exer,
                                                                ) use ($dayKey, $exercice) {
                                                                    return $exer['day'] == $dayKey &&
                                                                        $exer['fitness_profile_exercise_id'] ==
                                                                            $exercice['id'];
                                                                });
                                                                $filterExercise =
                                                                    !empty($exerciseData) && count($exerciseData) > 0
                                                                        ? array_values($exerciseData)[0]
                                                                        : [];

                                                            @endphp
                                                            <li class="col-md-4">
                                                                <div class="custom-form-check-head">
                                                                    <div class="custom-form-check mb-2">
                                                                        <label class="form-check">
                                                                            <input type="checkbox"
                                                                                day="{{ $dayKey }}"
                                                                                id="{{ $exercice['title'] == 'DAY_OFF' ? strtolower($dayKey) . 'DayOff' : strtolower($dayKey . '_' . $exercice['id']) }}"
                                                                                class="{{ $exercice['title'] != 'DAY_OFF' ? strtolower($dayKey) : 'day_off' }}"
                                                                                name="{{ $chkFieldName }}"
                                                                                value="{{ $exercice['id'] }}"
                                                                                {{ !empty($filterExercise) ? 'checked' : '' }}>
                                                                            <span>{{ str_replace('_', ' ', $exercice['title']) }}</span>
                                                                            <div class="checkbox__checkmark"></div>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                @if (!empty($durations))
                                                                    <div id="{{ $dayKey . '_' . $exercice['id'] . '_DURATION' }}"
                                                                        class="{{ $dayKey . '_DURATION' }}"
                                                                        style="{{ !empty($filterExercise) ? '' : 'display:none' }}">
                                                                        <div class="form-group">
                                                                            <label>Select Duration</label>
                                                                            <select
                                                                                class="form-control {{ $dayKey . '_DURATION_FIELD' }}"
                                                                                id="{{ $dayKey . '_' . $exercice['id'] . '_DURATION_FIELD' }}"
                                                                                name="{{ $durationFieldName }}">
                                                                                <option value="">Select Duration
                                                                                </option>
                                                                                @if (!empty($durations) && count($durations) > 0)
                                                                                    @foreach ($durations as $duration)
                                                                                        <option value="{{ $duration }}"
                                                                                            {{ !empty($filterExercise) && $filterExercise['duration'] == $duration ? 'selected' : '' }}>
                                                                                            {{ $duration }}M</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    @endif

                                                    @if (!empty($customExercises) && count($customExercises) > 0)
                                                        @foreach ($customExercises as $customKey => $customExercise)
                                                            @php
                                                                $customSessionFieldName =
                                                                    $dayKey . '' . '[custom_sessions][]';
                                                                $chkCustomFieldName =
                                                                    $dayKey . '' . '[custom_exercise_id][]';
                                                                $durationCustomFieldName =
                                                                    $dayKey .
                                                                    '' .
                                                                    '[custom_duration][' .
                                                                    $customExercise['workout_exercise_id'] .
                                                                    ']';

                                                                if (!empty($customExercise['custom_exercises'])) {
                                                                    $customExerciseIds[$dayKey][] =
                                                                        (int) $customExercise['custom_exercises']['id'];
                                                                }
                                                            @endphp
                                                            <li class="col-md-4 mainCustomDiv customDiv-{{ $dayKey }}"
                                                                id="id_{{ $customExercise['id'] }}">
                                                                @if ($customExercise['type'] == 'static' || $customExercise['type'] == 'custom')
                                                                    @php $exerciseName = !empty($customExercise['custom_exercises']) ? $customExercise['custom_exercises']['name'] : $customExercise['value']; @endphp
                                                                    <div class="custom-form-check-head">
                                                                        <div class="custom-form-check mb-2">
                                                                            <label class="form-check">
                                                                                <input type="hidden"
                                                                                    name="{{ $dayKey . '' . '[custom_exercise_types][' . $customExercise['workout_exercise_id'] . ']' }}"
                                                                                    value="{{ $customExercise['exercise_type'] }}">
                                                                                <input type="checkbox"
                                                                                    id="{{ $dayKey . '_field_' . $customExercise['id'] }}"
                                                                                    name="{{ $chkCustomFieldName }}"
                                                                                    value="{{ $customExercise['workout_exercise_id'] }}"
                                                                                    {{ !empty($customExercise['custom_exercises']) ? 'checked' : '' }}>
                                                                                <span
                                                                                    id="{{ $dayKey . '_lbl_' . $customExercise['id'] }}">{{ $exerciseName }}</span>

                                                                                <div class="checkbox__checkmark"></div>
                                                                            </label>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-dark"
                                                                                title="Add to additional days"
                                                                                onclick="openAddAddionalDaysModal(event, '{{ $exerciseName }}', {{ $customExercise['workout_exercise_id'] }},{{ $customExercise['id'] }}, '{{ $dayKey }}', '{{ $customExercise['exercise_type'] }}', 1)"><i
                                                                                    class="fas fa-plus"></i></button>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-dark" title="Edit"
                                                                                onclick="editCustomExercise(event,{{ $customExercise['id'] }}, '{{ $dayKey }}', '{{ $customExercise['exercise_type'] }}', 1)"><i
                                                                                    class="fas fa-pencil-alt"></i></button>
                                                                            <!-- <button class="btn btn-sm btn-dark removeCustomBtn" -->
                                                                            <button class="btn btn-sm btn-dark"
                                                                                title="Delete"
                                                                                onclick="removeCustomExercise(event,{{ $customExercise['id'] }}, {{ $customExercise['workout_exercise_id'] }}, '{{ $dayKey }}')">&times;</button>
                                                                        </div>
                                                                    </div>
                                                                @elseif ($customExercise['type'] == 'session')
                                                                    <div class="custom-form-check-head">
                                                                        <div class="custom-form-check mb-2">
                                                                            <input type="hidden"
                                                                                name="{{ $dayKey . '' . '[custom_session_types][' . $customExercise['value'] . ']' }}"
                                                                                value="{{ $customExercise['exercise_type'] }}">
                                                                            <input type="hidden"
                                                                                id="{{ $dayKey . '_field_' . $customExercise['id'] }}"
                                                                                name="{{ $customSessionFieldName }}"
                                                                                value="{{ $customExercise['value'] }}">
                                                                            <span
                                                                                id="{{ $dayKey . '_lbl_' . $customExercise['id'] }}">{{ $customExercise['value'] }}</span>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-dark"
                                                                                title="Add to additional days"
                                                                                onclick="openAddAddionalDaysModal(event, '{{ $customExercise['value'] }}','{{ $customExercise['value'] }}','{{ $customExercise['id'] }}', '{{ $dayKey }}', '{{ $customExercise['exercise_type'] }}', 1)"><i
                                                                                    class="fas fa-plus"></i></button>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-dark" title="Edit"
                                                                                onclick="editCustomExercise(event,{{ $customExercise['id'] }}, '{{ $dayKey }}', '{{ $customExercise['exercise_type'] }}', 1)"><i
                                                                                    class="fas fa-pencil-alt"></i></button>
                                                                            <button class="btn btn-sm btn-dark "
                                                                                title="Delete"
                                                                                onclick="removeCustomExercise(event,{{ $customExercise['id'] }},{{ $customExercise['id'] }}, '{{ $dayKey }}')">&times;</button>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </div>
                                        @endforeach
                                    @endif

                                    <div class="col-md-12 white-bg mb-4">
                                        <span class="day-text">REMINDER NOTIFICATION</span>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Notification Type</label>
                                                <select class="form-control" name="notification_type">
                                                    <option value="">Select</option>
                                                    @if (!empty($notificationTypes) && $notificationTypes->count() > 0)
                                                        @foreach ($notificationTypes as $notificationType)
                                                            <option value="{{ $notificationType->id }}"
                                                                {{ !empty($reminderData) && $reminderData['master_notification_type_id'] == $notificationType->id ? 'selected' : '' }}>
                                                                {{ $notificationType->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <input type="hidden" name="module_id"
                                                    {{ !empty($moduleData) ? 'value=' . $moduleData['id'] : '' }}>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="reminder_time">Reminder Time</label>
                                                <input type="time" id="reminder_time" name="reminder_time"
                                                    class="form-control"
                                                    value="{{ !empty($reminderData) && isset($reminderData['reminder_time']) ? $reminderData['reminder_time'] : '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 white-bg mb-4">
                                        <span class="day-text">REPEAT OPTIONS</span>
                                        <ul class="list-unstyled row">
                                            <li class="col-md-4">
                                                <div class="form-group">
                                                    <label>How many weeks or months do you want to repeat this
                                                        routin?</label>
                                                    <select class="form-control" name="repeat">
                                                        <option value="">Select</option>
                                                        <option value="CURRENT_WEEK"
                                                            {{ !empty($daySettings) && $daySettings['repeat'] == 'CURRENT_WEEK' ? 'selected' : '' }}>
                                                            Just this Week</option>
                                                        <option value="TWO_WEEKS"
                                                            {{ !empty($daySettings) && $daySettings['repeat'] == 'TWO_WEEKS' ? 'selected' : '' }}>
                                                            2 weeks</option>
                                                        <option value="ONE_MONTH"
                                                            {{ !empty($daySettings) && $daySettings['repeat'] == 'ONE_MONTH' ? 'selected' : '' }}>
                                                            1 Month</option>
                                                        <option value="THREE_MONTHS"
                                                            {{ !empty($daySettings) && $daySettings['repeat'] == 'THREE_MONTHS' ? 'selected' : '' }}>
                                                            3 Months</option>
                                                        <option value="SIX_MONTHS"
                                                            {{ !empty($daySettings) && $daySettings['repeat'] == 'SIX_MONTHS' ? 'selected' : '' }}>
                                                            6 Months</option>
                                                        <option value="NINE_MONTHS"
                                                            {{ !empty($daySettings) && $daySettings['repeat'] == 'NINE_MONTHS' ? 'selected' : '' }}>
                                                            9 Months</option>
                                                        <option value="TWELV_MONTHS"
                                                            {{ !empty($daySettings) && $daySettings['repeat'] == 'TWELV_MONTHS' ? 'selected' : '' }}>
                                                            12 Months</option>
                                                    </select>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-12 white-bg mb-4">
                                        <span class="day-text">FIRST DAY OF THE WEEK</span>
                                        <ul class="list-unstyled row">
                                            <li class="col-md-4">
                                                <div class="form-group">
                                                    <label>Set first day of the week</label>
                                                    <select class="form-control" name="week_first_day">
                                                        <option value="">Select</option>
                                                        <option value="SUNDAY"
                                                            {{ !empty($daySettings) && $daySettings['week_first_day'] == 'SUNDAY' ? 'selected' : '' }}>
                                                            SUNDAY</option>
                                                        <option value="MONDAY"
                                                            {{ !empty($daySettings) && $daySettings['week_first_day'] == 'MONDAY' ? 'selected' : '' }}>
                                                            MONDAY</option>
                                                    </select>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="btn_row text-center pb-4">
                                    <button type="button" onClick="saveSettings()" class="btn btn-secondary"
                                        id="settingBtn">SUBMIT<span id="settingBtnLoader"
                                            class="spinner-border spinner-border-sm"
                                            style="display: none;"></span></button>
                                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                                        href="{{ route('user.fitnessProfile', ['user_type' => $userType]) }}">Cancel</a>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade {{ $tabType == 'calendar' ? ' show active' : '' }}" id="calendarView"
                role="tabpanel" aria-labelledby="calendarView-tab">
                <div class="card">
                    <div class="card-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-l" id="customExerciseModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Choose Custom Workout</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onClick="closeCustomExerciseModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="workoutForm">
                            <ul class="health-checkbox">
                                <li class="form-check form-check-inline">
                                    <input class="radioBtn" onClick="selectWorkoutType('available-workouts')"
                                        type="radio" name="inlineRadioOptions" id="availableWorkoutsRadioBtn"
                                        value="available-workouts">
                                    <label class="form-check-label" for="availableWorkoutsRadioBtn">AVAILABLE
                                        WORKOUTS</label>
                                </li>
                                <li class="form-check form-check-inline">
                                    <input class="radioBtn" onClick="selectWorkoutType('my-workouts')" type="radio"
                                        name="inlineRadioOptions" id="myWorkoutRadioBtn" value="my-workouts">
                                    <label class="form-check-label" for="myWorkoutRadioBtn">MY WORKOUTS</label>
                                </li>
                                <li class="form-check form-check-inline">
                                    <input class="radioBtn" onClick="selectWorkoutType('custom-session')" type="radio"
                                        name="inlineRadioOptions" id="customSessionRadioBtn" value="custom-session">
                                    <label class="form-check-label" for="customSessionRadioBtn">CUSTOM SESSION</label>
                                </li>
                            </ul>

                            <div class="">
                                <div class="form-group custom-workouts radio-workout" style="display: none"
                                    id="availableWorkouts">
                                    <select class="form-control" id="availableWorkoutsFieldId" name="workout_id">
                                        <option value="">Select Workout</option>
                                        @if (!empty($availableWorkouts) && $availableWorkouts->count() > 0)
                                            @foreach ($availableWorkouts as $workout)
                                                <option value="{{ $workout->id }}"
                                                    data-thumbnail="{{ !empty($workout->media) ? $workout->media->base_url : asset('/assets/images/default-workout-image.jpg') }}"
                                                    data-title="{{ $workout->name }}"
                                                    data-description="{{ truncateWords($workout->description, 30) }}">
                                                    {{ $workout->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group custom-workouts" style="display: none" id="myWorkouts">
                                    <label for="recipient-name" class="col-form-label">Select Workout:</label>
                                    <select class="js-states form-control selectpicker" name="workout_id"
                                        id="myWorkoutsFieldId">
                                        <option value="">Select Workout</option>
                                        @if (!empty($myWorkouts) && $myWorkouts->count() > 0)
                                            @foreach ($myWorkouts as $workout)
                                                <option value="{{ $workout->id }}"
                                                    data-thumbnail="{{ !empty($workout->media) ? $workout->media->base_url : asset('/assets/images/default-workout-image.jpg') }}"
                                                    data-title="{{ $workout->name }}"
                                                    data-description="{{ truncateWords($workout->description, 30) }}">
                                                    {{ $workout->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group custom-workouts" style="display: none" id="customSession">
                                    <label for="recipient-name" class="col-form-label">Title:</label>
                                    <input class="form-control" name="custom_session" id="customSessionFieldId">
                                    <span id="customSession-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onClick="closeCustomExerciseModal()">Close</button>
                        <button type="button" id="addCustomExercise" class="btn btn-primary add-exercise-btn"
                            onClick="addEditCustomExercise()">Add <span
                                class="spinner-border spinner-border-sm add-exercise-btn-loader"
                                style="display: none;"></span></button>
                        <button type="button" id="updateCustomExercise" class="btn btn-primary add-exercise-btn"
                            onClick="updateCustomExercise()">Update <span
                                class="spinner-border spinner-border-sm add-exercise-btn-loader"
                                style="display: none;"></span></button>
                    </div>
                </div>
            </div>
        </div>

        <!--Add to addional days-->
        <div class="modal fade" id="addAddionalDaysModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add custom workout to addional days</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onClick="closeAddAddionalDaysModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addAddionalWorkoutForm">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label w-100 mb-0">Select Days:</label>
                                <select class="js-states form-control selectpicker" name="addional_days[]"
                                    id="addionalDayFieldId" multiple>
                                    @foreach ($weekDays as $weekDay)
                                        <option value="{{ $weekDay }}">
                                            {{ $weekDay }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onClick="closeAddAddionalDaysModal()">Close</button>
                        <button type="button" id="addAddionalWorkoutBtn" class="btn btn-primary"
                            onClick="copyToAddionalDays()">Add</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Add to addional days-->
        <!-- Modal -->
        <div class="modal fade" id="calendarEventModal" tabindex="-1" aria-labelledby="tableModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="tableModalLabel">Add/Edit Exercise</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onClick="closeCalendarEventModal()">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Top Buttons -->
                        <div class="mb-3 d-flex justify-content-between">
                            <button type="button" class="btn btn-success" id="addButton"
                                onClick="openCustomExerciseModal('', addType='calendar')">Add New Session</button>
                        </div>
                        <form id="addCalendarExerciseForm">
                            <!-- Table -->
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Exercise Name</th>
                                        <th>Action</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody id="calendarActionModalTRListId"></tbody>
                            </table>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onClick="saveCalendarExercise()" id="addCustonExerciseBtn">Save
                            <span class="spinner-border spinner-border-sm" id="addCustonExerciseBtnLoader"
                                style="display: none;"></span>
                        </button>
                        <button type="button" class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                            data-dismiss="modal" onClick="closeCalendarEventModal()">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        let data = @json($data);
        let exercises = @json($exercises);
        let myWorkouts = @json($myWorkouts);
        let availableWorkouts = @json($availableWorkouts);
        let workouts = myWorkouts.concat(availableWorkouts);
        let customExerciseIds = @json($customExerciseIds);
        let daysCustomExercises = customExerciseIds ? customExerciseIds : {};
        let editWorkoutObj = {};
        let copyWorkoutObj = {};
        let activeTab = "settings";

        $("#customSessionFieldId").on('input', function() {
            if (this.value.length > 30) {
                $("#customSession-error").text('Title should be less than 30 characters');
                $("#addCustomExercise").prop('disabled', true);
            } else {
                $("#customSession-error").text('');
                $("#addCustomExercise").prop('disabled', false);
            }
        });


        function saveSettings() {
            bootbox.confirm("Are you sure you want to update the settings?", function(result) {
                if (result) {
                    var formData = $("#finessSettingForm").serializeArray();
                    $('#settingBtn').prop('disabled', true);
                    $('#settingBtnLoader').show();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('common.savSettings') }}",
                        data: formData,
                        success: function(response) {
                            $('#settingBtn').prop('disabled', false);
                            $('#settingBtnLoader').hide();
                            if (response.success) {
                                _toast.success(response.message);
                                setTimeout(function() {
                                    window.location.href =
                                        "{{ route('user.fitnessProfile', ['user_type' => $userType]) }}";
                                }, 500)
                            } else {
                                _toast.error('Somthing went wrong. please try again');
                            }
                        },
                        error: function(err) {
                            $('#settingBtn').prop('disabled', false);
                            $('#settingBtnLoader').hide();
                            if (err.status === 422) {
                                var errors = $.parseJSON(err.responseText);
                                $.each(errors.errors, function(key, val) {
                                    $("#" + key + "-error").text(val);
                                });
                            } else {
                                _toast.error('Settings not updated.');
                            }
                        },
                    });
                }
            })
        }

        $(document).ready(function() {
            $(".day_off").on('click', function(e) {
                let id = $(this).attr('id');
                let day = $(this).attr('day');
                if (id) {
                    $('.' + day).prop('checked', false);
                    $('.' + day + '_DURATION').hide();
                    $('.' + day + '_DURATION_FIELD').val('');
                    $('.customDiv-' + day).remove();
                }
            });
            $(".monday").on('click', function(e) {
                $('#mondayDayOff').prop('checked', false);
                if (this.checked) {
                    $('#MONDAY_' + this.value + '_DURATION').show();
                } else {
                    $('#MONDAY_' + this.value + '_DURATION').hide();
                    $('#MONDAY_' + this.value + '_DURATION_FIELD').val('');
                }
            });

            $(".tuesday").on('click', function(e) {
                $('#tuesdayDayOff').prop('checked', false);
                if (this.checked) {
                    $('#TUESDAY_' + this.value + '_DURATION').show();
                } else {
                    $('#TUESDAY_' + this.value + '_DURATION').hide();
                    $('#TUESDAY_' + this.value + '_DURATION_FIELD').val('');
                }
            });
            $(".wednesday").on('click', function(e) {
                $('#wednesdayDayOff').prop('checked', false);
                if (this.checked) {
                    $('#WEDNESDAY_' + this.value + '_DURATION').show();
                } else {
                    $('#WEDNESDAY_' + this.value + '_DURATION').hide();
                    $('#WEDNESDAY_' + this.value + '_DURATION_FIELD').val('');
                }
            });
            $(".thursday").on('click', function(e) {
                $('#thursdayDayOff').prop('checked', false);
                if (this.checked) {
                    $('#THURSDAY_' + this.value + '_DURATION').show();
                } else {
                    $('#THURSDAY_' + this.value + '_DURATION').hide();
                    $('#THURSDAY_' + this.value + '_DURATION_FIELD').val('');
                }
            });
            $(".friday").on('click', function(e) {
                $('#fridayDayOff').prop('checked', false);
                if (this.checked) {
                    $('#FRIDAY_' + this.value + '_DURATION').show();
                } else {
                    $('#FRIDAY_' + this.value + '_DURATION').hide();
                    $('#FRIDAY_' + this.value + '_DURATION_FIELD').val('');
                }
            });
            $(".saturday").on('click', function(e) {
                $('#saturdayDayOff').prop('checked', false);
                if (this.checked) {
                    $('#SATURDAY_' + this.value + '_DURATION').show();
                } else {
                    $('#SATURDAY_' + this.value + '_DURATION').hide();
                    $('#SATURDAY_' + this.value + '_DURATION_FIELD').val('');
                }
            });
            $(".sunday").on('click', function(e) {
                $('#sundayDayOff').prop('checked', false);
                if (this.checked) {
                    $('#SUNDAY_' + this.value + '_DURATION').show();
                } else {
                    $('#SUNDAY_' + this.value + '_DURATION').hide();
                    $('#SUNDAY_' + this.value + '_DURATION_FIELD').val('');
                }
            });

            $('#availableWorkoutsFieldId, #myWorkoutsFieldId').select2({
                placeholder: 'Select Workout',
                templateResult: formatWorkoutOption,
                templateSelection: formatWorkoutSelection,
                escapeMarkup: function(markup) {
                    return markup;
                }
            });

            function formatWorkoutOption(workout) {
                if (!workout.id) return workout.text;

                const thumbnail = $(workout.element).data('thumbnail');
                const title = $(workout.element).data('title');
                const description = $(workout.element).data('description');

                return `
                    <div style="display: flex; align-items: center;">
                        <img src="${thumbnail}" alt="${title}" style="width: 60px; height: 40px; object-fit: cover; margin-right: 10px;">
                        <div>
                            <div><strong>${title}</strong></div>
                            <p class="text-break">${description}</p>
                        </div>
                    </div>
                `;
            }

            function formatWorkoutSelection(workout) {
                return workout.text;
            }
        });

        let selectedDay = "";

        function openCustomExerciseModal(day = '', addType = '') {
            if (addType == 'calendar') {
                $('#calendarEventModal').modal('hide');
            } else {
                selectedDay = day;
            }
            $(".custom-workouts").hide();
            $('#workoutForm')[0].reset();
            $(".radioBtn").prop("disabled", false);
            $("#addCustomExercise").show();
            $("#updateCustomExercise").hide();
            $('#customExerciseModal').modal('show');
        }

        function closeCustomExerciseModal() {
            $('#customExerciseModal').modal('hide');
        }

        function addEditCustomExercise(action = "", isCopyExercise = false) {
            let workoutId = "";
            let duration = 0;
            const uniqueId = Math.random().toString(16).slice(2);
            const id = "id_" + uniqueId;
            let workoutType = $('.radioBtn:checked').val();
            if (workoutType == "available-workouts") {
                workoutId = $("#availableWorkoutsFieldId").val();
            } else if (workoutType == "my-workouts") {
                workoutId = $("#myWorkoutsFieldId").val();

            } else if (workoutType == "custom-session") {
                workoutId = $("#customSessionFieldId").val();
            }

            //Condition for copy exercise
            if (isCopyExercise) {
                workoutId = copyWorkoutObj.value;
                workoutType = copyWorkoutObj.type;
                selectedDay = copyWorkoutObj.day;
            }

            //Condition for add exercise
            if (activeTab != 'calendar') {

                if (workoutId && workoutType != 'custom-session') {
                    let exercise = workouts.find((obj) => {
                        return obj.id == workoutId
                    });
                    if (isCopyExercise) {
                        exercise = {
                            name: copyWorkoutObj.name
                        };
                    }

                    if (exercise) {
                        if (!daysCustomExercises[selectedDay] || (daysCustomExercises[selectedDay] && daysCustomExercises[
                                selectedDay].indexOf(parseInt(workoutId)) == -1)) {
                            let html = `<li class="mainCustomDiv col-md-4 customDiv-` + selectedDay + `" id="` + id + `">
                                        <div class="custom-form-check-head">
                                            <div class="custom-form-check mb-2">
                                                <label class="form-check">
                                                     <input type="hidden"
                                                    name="` + selectedDay + `[custom_exercise_types][` + workoutId + `]"
                                                    value="` + workoutType + `">
                                                    <input type="checkbox"
                                                    day="` + selectedDay + `"
                                                    id="` + selectedDay + `_field_` + uniqueId + `"
                                                    class="` + selectedDay + `"
                                                    name="` + selectedDay + `[custom_exercise_id][]"
                                                    value="` + workoutId + `" checked>
                                                    <span
                                                    id="` + selectedDay + `_lbl_` + uniqueId + `">` +
                                exercise
                                .name + `</span> &nbsp;

                                                    <div class="checkbox__checkmark"></div>
                                                </label>
                                                <button type="button" title="Add to additional days" class="btn btn-sm btn-dark" onclick="openAddAddionalDaysModal(event, '${exercise.name}', '${workoutId}','${uniqueId}', '${selectedDay}', '${workoutType}', 0)"><i class="fas fa-plus"></i></button>
                                                <button type="button" title="Edit" class="btn btn-sm btn-dark" onclick="editCustomExercise(event,'${uniqueId}', '${selectedDay}', '${workoutType}', 0)"><i class="fas fa-pencil-alt"></i></button>
                                                <button class=" btn btn-sm btn-dark"  title="Delete" onclick="removeCustomExercise(event,'${uniqueId}', ${workoutId}, '${selectedDay}')">&times;</button>
                                            </div>
                                        </div>
                                    </li>`;

                            if (action == "edit") {
                                $('#id_' + editWorkoutObj.id).replaceWith(html);
                                _toast.success('Custom exercise successfully updated');
                            } else {
                                $('#' + selectedDay + '_ul').append(html);
                                _toast.success('Custom exercise successfully added');
                            }


                            if (daysCustomExercises[selectedDay]) {
                                daysCustomExercises[selectedDay].push(parseInt(workoutId));
                            } else {
                                daysCustomExercises[selectedDay] = [parseInt(workoutId)];
                            }

                            $('#customExerciseModal').modal('hide');

                        } else {
                            _toast.error('This workout already added.');
                        }
                    } else {
                        _toast.error('Workout not found.');
                    }
                    //$dayKey . '' . '[static_duration][' . $exercice['id'] . ']';
                } else if (workoutType == 'custom-session') {
                    let html = `<li class="mainCustomDiv col-md-4 customDiv-` + selectedDay + `" id="` + id + `">
                            <div class="custom-form-check-head">
                                <div class="custom-form-check mb-2">
                                        <input type="hidden"
                                        name="` + selectedDay + `[custom_session_types][` + workoutId + `]"
                                        value="` + workoutType + `">
                                        <input type="hidden" day="` + selectedDay + `"
                                        id="` + selectedDay + `_field_` + uniqueId + `"
                                        class="` + selectedDay + `"
                                        name="` + selectedDay + `[custom_sessions][]" value="` + workoutId + `">
                                        <span id="` + selectedDay + `_lbl_` + uniqueId + `">` + workoutId + `</span>
                                    <button type="button" title="Add to additional days" class="btn btn-sm btn-dark" onclick="openAddAddionalDaysModal(event,'${workoutId}','${workoutId}','${uniqueId}', '${selectedDay}', 'custom-session', 0)"><i class="fas fa-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-dark" title="Edit" onclick="editCustomExercise(event,'${uniqueId}', '${selectedDay}', 'custom-session', 0)"><i class="fas fa-pencil-alt"></i></button>
                                    <button type="button" class=" btn btn-sm btn-dark" title="Delete" onclick="removeCustomExercise(event,'${uniqueId}', '${workoutId}','${selectedDay}')">&times;</button>
                                </div>
                            </div>

                        </li>`;

                    if (action == "edit") {
                        $('#id_' + editWorkoutObj.id).replaceWith(html);
                        _toast.success('Custom exercise successfully updated');
                    } else {
                        $('#' + selectedDay + '_ul').append(html);
                        _toast.success('Custom exercise successfully added');
                    }
                    $('#customExerciseModal').modal('hide');
                } else {
                    _toast.error('Please select workout.');
                }
            } else {
                //Add exercise from calendar
                const exerciseObj = {
                    day: selectedDay,
                    value: '',
                    type: '',
                    exercise_type: workoutType,
                    duration: 0,
                    fitness_profile_exercise_id: '',
                    workout_exercise_id: '',
                };
                if (workoutType == 'custom-session') {
                    exerciseObj.value = workoutId;
                    exerciseObj.type = 'session';
                } else {
                    exerciseObj.type = 'custom';
                    let exercise = workouts.find((obj) => {
                        return obj.id == workoutId
                    });
                    if (exercise) {
                        exerciseObj.value = exercise.name;
                        exerciseObj.workout_exercise_id = exercise.id;
                    } else {
                        _toast.error('Workout not found.');
                        return true;
                    }
                }

                $('.add-exercise-btn').prop('disabled', true);
                $('.add-exercise-btn-loader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveSettingCustomExercise') }}",
                    data: exerciseObj,
                    success: function(response) {
                        $('.add-exercise-btn').prop('disabled', false);
                        $('.add-exercise-btn-loader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 500)
                        } else {
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('.add-exercise-btn').prop('disabled', false);
                        $('.add-exercise-btn-loader').hide();
                        _toast.error('Please try again.');
                    },
                });
            }
        }

        function updateCustomExercise() {
            const workoutType = $('.radioBtn:checked').val();
            addEditCustomExercise("edit");
        }

        function removeCustomExercise(event, uniqueId, workoutId, selectedDay) {
            event.preventDefault();
            $('#id_' + uniqueId).remove();
            if (daysCustomExercises[selectedDay]) {
                const idIndex = daysCustomExercises[selectedDay].indexOf(parseInt(workoutId));
                if (idIndex != -1) {
                    daysCustomExercises[selectedDay].splice(idIndex, 1);
                }
            }
        }

        function editCustomExercise(event, id, day, type, isPrimaryIDExist = 0) {
            selectedDay = day;
            editWorkoutObj = {};
            const value = $('#' + day + '_field_' + id).val();
            $(".custom-workouts").hide();
            $('#workoutForm')[0].reset();
            $("#addCustomExercise").hide();
            $("#updateCustomExercise").show();
            $(".radioBtn").prop("disabled", true);
            editWorkoutObj['id'] = id;
            editWorkoutObj['day'] = day;
            editWorkoutObj['type'] = type;
            editWorkoutObj['labelId'] = id;
            if (type == 'available-workouts') {
                $("#availableWorkouts").show();
                $("#availableWorkoutsRadioBtn").prop("checked", true);
                // $('#availableWorkoutsFieldId').val(value);
                $('#availableWorkoutsFieldId').val(value).trigger('change');
            } else if (type == 'my-workouts') {
                $("#myWorkouts").show();
                $("#myWorkoutRadioBtn").prop("checked", true);
                // $('#myWorkoutsFieldId').val(value);
                $('#myWorkoutsFieldId').val(value).trigger('change');
            } else if (type == 'custom-session') {
                $("#customSession").show();
                $("#customSessionRadioBtn").prop("checked", true);
                $('#customSessionFieldId').val(value);
            }
            $('#customExerciseModal').modal('show');
        }

        function openAddAddionalDaysModal(event, name, value, id, day, type) {
            copyWorkoutObj = {
                id: id,
                day: '',
                type: type,
                name: name,
                value: value
            };
            $('#addionalDayFieldId').val([]).trigger('change');
            $('#addAddionalDaysModal').modal('show');
        }

        function closeAddAddionalDaysModal(event, id, day, type) {
            copyWorkoutObj = {};
            $('#addAddionalDaysModal').modal('hide');
        }

        function copyToAddionalDays() {
            const workoutDays = $('#addionalDayFieldId').val();
            workoutDays.forEach((day) => {
                copyWorkoutObj.day = day;
                addEditCustomExercise("add", true);
            })
            $('#addAddionalDaysModal').modal('hide');

        }

        function selectWorkoutType(type) {
            $(".custom-workouts").hide();
            // $("#availableWorkoutsFieldId").val('');
            $("#availableWorkoutsFieldId").val("").trigger('change');
            // $("#myWorkoutsFieldId").val('');
            $("#myWorkoutsFieldId").val("").trigger('change');
            $("#customSessionFieldId").val('');
            $("#customSession-error").text('');
            $("#addCustomExercise").prop('disabled', false);
            if (type == "available-workouts") {
                $("#availableWorkouts").show();
            }
            if (type == "my-workouts") {
                $("#myWorkouts").show();
            }
            if (type == "custom-session") {
                $("#customSession").show();
            }
        }

        $('#addionalDayFieldId').select2({
            placeholder: "Select Days",
            allowClear: true
        });

        let isCalendarDataLoad = false;
        let currentMonthData = {};

        function loadCalendarData(date = "") {
            $.ajax({
                type: "GET",
                url: "{{ route('common.loadFitnessCalendarData') }}",
                data: {
                    date: date
                },
                success: function(response) {
                    if (response.success) {
                        const dateObj = response.data.data;
                        currentMonthData = dateObj;
                        const newEvents = [];
                        Object.keys(dateObj).forEach((dateStr) => {
                            //newEvents = newEvents.concat(dateObj[dateStr]);
                            dateObj[dateStr].forEach((obj) => {
                                const newObj = JSON.parse(JSON.stringify(
                                    obj));
                                obj.title = newObj.value.replace(/_/g, ' ');
                                obj.start = dateStr;
                                newEvents.push(obj);
                            })
                        })
                        // Remove existing events
                        $('#calendar').fullCalendar('removeEvents');
                        // Add new events
                        $('#calendar').fullCalendar('addEventSource', newEvents);
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    _toast.error('Settings not updated.');
                },
            });
        }

        function closeCalendarEventModal() {
            $("#calendarEventModal").modal("hide");
        }

        function loadCalendar(events = []) {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                },
                defaultDate: Date.now(),
                dayClick: function(date, jsEvent, view) {
                    const calendarSelectedDay = date.format('dddd').toUpperCase();
                    const selectedDate = date.format('YYYY-MM-DD');
                    let tableHtml = ``;
                    selectedDay = '';
                    $('#calendarActionModalTRListId').html('');
                    if (calendarSelectedDay) {
                        selectedDay = calendarSelectedDay;
                        if (exercises[calendarSelectedDay].length) {
                            // console.log("====exercises[calendarSelectedDay]====", exercises[
                            //     calendarSelectedDay]);
                            exercises[calendarSelectedDay].forEach((obj) => {
                                if (obj.title != 'DAY_OFF') {
                                    let options = ``;
                                    const durations = obj.durations ? obj.durations.split(",") : [];
                                    const isExerciseExist = data.find((ex) => ex.day ==
                                        calendarSelectedDay && ex.fitness_profile_exercise_id == obj
                                        .id);
                                    const exDuration = isExerciseExist ? isExerciseExist.duration : 0;
                                    console.log("====durations====", durations);
                                    durations.forEach((durationVal) => {
                                        options +=
                                            `<option value='${durationVal}' ${durationVal==exDuration ? 'selected' : ''}>${durationVal}M</option>`;
                                    })
                                    tableHtml += `<tr>
                                                <td>${obj.title.replace("_", " ")}</td>
                                                <td><input type="checkbox"
                                                    value="${obj.id}" ${isExerciseExist ? `checked` : ``}
                                                    name="` + selectedDay + `[exercise][]"/></td>
                                                <td>
                                                    <select class="form-control"
                                                    id="${obj.id}_duration_id"
                                                    name="` + selectedDay + `[duration][${obj.id}]">
                                                    ${options}
                                                    </select>
                                                </td>
                                            </tr>`;
                                }
                            });
                        } else {
                            tableHtml = `<tr><td colspan="2">No Record Found.</td></tr>`;
                        }
                        $('#calendarActionModalTRListId').html(tableHtml);
                    }

                    $("#calendarEventModal").modal("show");
                },
                eventClick: function(event, jsEvent, view) {
                    // You can access more properties:
                    //console.log('Event ====:====', event);
                },
                viewRender: function(view, element) {
                    const date = view.intervalStart.format('YYYY-MM-DD');
                    //console.log('FullCalendar has finished rendering.', view.intervalStart.format('YYYY-MM-DD'));
                    loadCalendarData(date);
                },
                eventRender: function(event, element) {
                    const startDate = event.start;
                    const dayName = startDate.format('dddd'); // e.g., "Monday"
                    const selectedDate = startDate.format('YYYY-MM-DD');
                    // Clear existing text
                    element.find('.fc-title').html('');
                    // Create custom HTML with title + delete icon
                    let deleteBtn = $(
                        '<span class="fc-delete" style="color: red; margin-left: 8px; cursor: pointer;">&times;</span>'
                    );

                    // Append title and delete icon
                    element.find('.fc-content').append(
                        $('<span>').text(event.title)
                    ).append(deleteBtn);

                    // Click handler for delete icon
                    deleteBtn.on('click', function(e) {
                        e.stopPropagation(); // Prevent triggering eventClick
                        bootbox.confirm(
                            `Are you sure you want to delete this exercise from all ${dayName} ?`,
                            function(result) {
                                if (result) {
                                    let url = "{{ route('common.deleteFitnessExercise', ':id') }}";
                                    url = url.replace(':id', event.id);
                                    $.ajax({
                                        type: "DELETE",
                                        url: url,
                                        data: {},
                                        success: function(response) {
                                            if (response.success) {
                                                location.reload();
                                                //loadCalendarData(selectedDate);
                                            } else {
                                                _toast.error(
                                                    'Somthing went wrong. please try again'
                                                );
                                            }
                                        },
                                        error: function(err) {
                                            _toast.error('Settings not updated.');
                                        },
                                    });
                                }
                            })
                    });
                }
            });
        }

        function changeTab(tab = '') {
            activeTab = tab;
        }

        function saveCalendarExercise() {
            const formData = $('#addCalendarExerciseForm').serializeArray();
            formData.push({
                name: 'day',
                value: selectedDay
            });
            console.log("====formData====", formData);
            $('#addCustonExerciseBtn').prop('disabled', true);
            $('#addCustonExerciseBtnloader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.saveSettingStaticExercise') }}",
                data: formData,
                success: function(response) {
                    $('#addCustonExerciseBtn').prop('disabled', false);
                    $('#addCustonExerciseBtnloader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 500)
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addCustonExerciseBtn').prop('disabled', false);
                    $('#addCustonExerciseBtnLoader').hide();
                    _toast.error('Please try again.');
                },
            });
        }
        loadCalendar();
    </script>
@endsection
<style>
    .mainCustomDiv {
        position: relative;
    }

    .removeCustomBtn {
        position: absolute;
        top: 0;
        right: 0;
    }
</style>
