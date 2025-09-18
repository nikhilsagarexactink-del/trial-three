@extends('layouts.app')

@section('head')
    <title>Workout | Update</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $id = request()->route('id');
        $userType = userType();
        $user = getUser();

        $selectedDifficulties = [];
        if (!empty($result->difficulties)) {
            foreach ($result->difficulties as $difficulty) {
                array_push($selectedDifficulties, $difficulty->id);
            }
        }
        $selectedAthletes = [];
        if (!empty($result->athletes)) {
            foreach ($result->athletes as $athleteData) {
                array_push($selectedAthletes, $athleteData->id);
            }
        }

        $selectedAgeRanges = [];
        if (!empty($result->ageRanges)) {
            foreach ($result->ageRanges as $ageRange) {
                array_push($selectedAgeRanges, $ageRange->id);
            }
        }

        $selectedSports = [];
        if (!empty($result->sports)) {
            foreach ($result->sports as $sport) {
                array_push($selectedSports, $sport->id);
            }
        }

        $selectedCategories = [];
        if (!empty($result->categories)) {
            foreach ($result->categories as $category) {
                array_push($selectedCategories, $category->id);
            }
        }

        $workoutGroups = [];
        if (!empty($result->workoutGroups)) {
            foreach ($result->workoutGroups as $group) {
                array_push($workoutGroups, $group->group_id);
            }
        }
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.indexWorkoutExercise', ['user_type' => $userType]) }}">Manage
                                Workout</a></li>
                        <li class="breadcrumb-item active">Update Workout</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Update Workout
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.updateExerciseWorkout', ['id' => $id]) }}">
                @csrf
                @method('PUT')
                <!-- Workout Edit Form Start -->
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="uploadImageUrl" value="{{ route('common.saveImage') }}">
                        <input type="hidden" id="mediaFor" value="workout">
                        <input type="text" name="type" value="workout" hidden>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Workout Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $result->name ?? '' }}" placeholder="Workout Name">
                                    <span id="name-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label>Visibility (in workout directory)</label>
                                    <select id="visibility" name="visibility" class="form-control">
                                        <option value="">Select Visibility</option>
                                        <option value="hidden" {{ $result->visibility == 'hidden' ? 'selected' : '' }}>
                                            Hidden</option>
                                        <option value="visible" {{ $result->visibility == 'visible' ? 'selected' : '' }}>
                                            Visible</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="difficulty_id">Difficulty<span class="text-danger">*</span></label>
                                    <select class="js-states form-control selectpicker" id="difficulty_id"
                                        name="difficulty_id[]" multiple>
                                        <option value="">Select Difficulty</option>
                                        @foreach ($difficulties as $difficulty)
                                            <option value="{{ $difficulty->id }}"
                                                {{ in_array($difficulty->id, $selectedDifficulties) ? 'selected' : '' }}>
                                                {{ ucfirst($difficulty->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span id="difficulty_id-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            @if($userType == 'admin' || $userType == 'coach')
                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="age_range_id">Age Group</label>
                                    <select class="form-control" id="age_range_id" name="age_range_id[]" multiple>
                                        <option value="">Select Age Group</option>
                                        @foreach ($ageRanges as $ageRange)
                                            <option value="{{ $ageRange->id }}"
                                                {{ in_array($ageRange->id, $selectedAgeRanges) ? 'selected' : '' }}>
                                                {{ $ageRange->min_age_range }} - {{ $ageRange->max_age_range }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="age_range_id-error" class="help-block error-help-block text-danger"></span>
                            </div>
                            @else
                            @foreach ($selectedAgeRanges as $selectedAgeRange)
                                <input type="hidden" name="age_range_id[]" value="{{ $selectedAgeRange }}">
                            @endforeach
                            @endif

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="sport_id">Sport Selection</label>
                                    <select class="js-states form-control selectpicker" id="sport_id" name="sport_id[]"
                                        multiple>
                                        <option value="">Select Sport</option>
                                        @foreach ($sports as $sport)
                                            <option value="{{ $sport->id }}"
                                                {{ in_array($sport->id, $selectedSports) ? 'selected' : '' }}>
                                                {{ $sport->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="category_id">Category<span class="text-danger">*</span></label>
                                    <select class="js-states form-control selectpicker" id="category_id"
                                        name="category_id[]" multiple>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ in_array($category->id, $selectedCategories) ? 'selected' : '' }}>
                                                {{ ucfirst($category->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span id="category_id-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group multi-select {{$userType == 'athlete' ? 'athlete' : ''}}">
                                    <label for="athlete_user_ids">Select Athlete</label>
                                    <select id="athlete_user_ids" name="athlete_user_ids[]"
                                        class="js-states form-control selectpicker" 
                                        multiple
                                        @if($userType != 'admin') disabled @endif>
                                        @if($userType === 'admin')
                                            <option value="">Select Athlete</option>
                                        @endif
                                        @foreach ($athletes as $athlete)
                                            @if($userType == 'admin')
                                                <option value="{{ $athlete->id }}"
                                                    {{ in_array($athlete->id, $selectedAthletes) ? 'selected' : '' }}>
                                                    {{ ucfirst($athlete->first_name) . ' ' . $athlete->last_name }}
                                                </option>
                                            @else
                                            @if($athlete->id == $user->id)
                                                <option value="{{ $athlete->id }}"{{ in_array($athlete->id, $selectedAthletes) ? 'selected' : '' }}>{{ ucfirst($athlete->first_name) . ' ' . $athlete->last_name }}</option>
                                                @if(in_array($athlete->id, $selectedAthletes) && count($selectedAthletes) == 1)
                                                <input type="hidden" name="athlete_user_ids[]" value="{{ $athlete->id }}">
                                                @endif
                                                @endif
                                            @endif
                                        @endforeach
                                        @if(count($selectedAthletes) > 1 && $userType != 'admin')
                                            @foreach ($selectedAthletes as $selectedAthlete)
                                                <input type="hidden" name="athlete_user_ids[]" value="{{$selectedAthlete}}">
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            @if($userType == 'admin' || $userType == 'parent')
                                <div class="col-md-6">
                                    <div class="form-group multi-select">
                                        <label for="group_ids">Select Group</label>
                                        <select id="group_ids" name="group_ids[]"
                                            class="js-states form-control selectpicker" multiple >
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}"
                                                    {{ in_array($group->id, $workoutGroups) ? 'selected' : '' }}>
                                                    {{ ucfirst($group->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label>Days</label>
                                    <select id="days" name="days[]" class="js-states form-control selectpicker"
                                        multiple>
                                        <option value="monday"
                                            {{ strpos($result->days, 'monday') !== false ? 'selected' : '' }}>Monday
                                        </option>
                                        <option value="tuesday"
                                            {{ strpos($result->days, 'tuesday') !== false ? 'selected' : '' }}>Tuesday
                                        </option>
                                        <option value="wednesday"
                                            {{ strpos($result->days, 'wednesday') !== false ? 'selected' : '' }}>Wednesday
                                        </option>
                                        <option value="thursday"
                                            {{ strpos($result->days, 'thursday') !== false ? 'selected' : '' }}>Thursday
                                        </option>
                                        <option value="friday"
                                            {{ strpos($result->days, 'friday') !== false ? 'selected' : '' }}>Friday
                                        </option>
                                        <option value="saturday"
                                            {{ strpos($result->days, 'saturday') !== false ? 'selected' : '' }}>Saturday
                                        </option>
                                        <option value="sunday"
                                            {{ strpos($result->days, 'sunday') !== false ? 'selected' : '' }}>Sunday
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control text-editor" id="description" name="description" rows="3">{{ $result->description ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12" id="howManySetsOfDiv"
                                style="{{ count($result->sets) > 0 ? 'display:none' : '' }}">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">HOW MANY SETS?<span class="text-danger">*</span></label>
                                                    <select id="totalSets" name="total_sets" onChange="createSets()"
                                                        class="js-states form-control selectpicker">
                                                        <option value="">Select Sets</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                    </select>
                                                    <span id="total_sets-error" class="help-block error-help-block text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" id="setListDiv"
                                style="{{ count($result->sets) == 0 ? 'display:none' : '' }}">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">EXERCISES</h5>
                                        <div class="text-right">
                                            <button type="button" onClick="createSets(true)"
                                                class="btn btn-outline-primary">
                                                ADD ANOTHER SET
                                            </button>
                                        </div>
                                        <br />
                                        <div id="setsDiv" class="row">
                                            @foreach ($result->sets as $setKey => $set)
                                                @php $setIndex = $setKey + 1; @endphp
                                                <div class="col-md-6 sets-div" id="set_{{ $setIndex }}">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <input type="hidden" id="set_{{ $setIndex }}_field"
                                                                name="sets[{{ $setIndex }}]"
                                                                value="{{ $setIndex }}">
                                                            <div class="exercises-set-head">
                                                                <h4 id="set_{{ $setIndex }}_title">
                                                                    SET {{ $setIndex }}
                                                                </h4>
                                                                <a href="javascript:void(0)"
                                                                    class="set-close set_{{ $setIndex }}_index"
                                                                    id="{{ $setIndex }}" index="{{ $setIndex }}"
                                                                    onClick="deleteSet(this)"><img src="{{ asset('assets/images/close.svg') }}" alt=""></a>
                                                            </div>
                                                            <ul class="row mt-3 mb-4"
                                                                id="set_{{ $setIndex }}_exercises">
                                                                @if (!empty($set->workoutSetExercises))
                                                                    @foreach ($set->workoutSetExercises as $setExerciseKey => $setExercise)
                                                                        <li class="col-md-6"
                                                                            id="exercise_li_{{ $setIndex }}_{{ $setExerciseKey }}"
                                                                            data-id="{{ $setExerciseKey }}"
                                                                            parent-id="set_{{ $setIndex }}_exercises"
                                                                            data-value="name">
                                                                            <div class="col-md-12 exercise-set-head pb-3 exercise-set-{{ $setIndex }}"
                                                                                id="exercise_div_{{ $setIndex }}_{{ $setExerciseKey }}">
                                                                                <div class="card">
                                                                                    <div class="exercise-set-img">
                                                                                        @if (!empty($setExercise->exercise) && !empty($setExercise->exercise->media) && !empty($result->media->base_url))
                                                                                            <img
                                                                                                src="{{ $result->media->base_url }}">
                                                                                        @else
                                                                                            <img
                                                                                                src="{{ url('assets/images/default-workout-image.jpg') }}">
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <div class="exercise-set-desc">
                                                                                            <h5 class="card-title">
                                                                                                {{ !empty($setExercise->exercise) ? $setExercise->exercise->name : '' }}
                                                                                                <div class="dropdown">
                                                                                                    <button
                                                                                                        class="dropdown-toggle"
                                                                                                        type="button"
                                                                                                        id="dropdownMenuButtonNew{{ $setIndex }}"
                                                                                                        data-bs-toggle="dropdown"
                                                                                                        aria-expanded="false">
                                                                                                        <img
                                                                                                            src="{{ url('assets/images/more.png') }}">
                                                                                                    </button>
                                                                                                    <ul class="dropdown-menu"
                                                                                                        aria-labelledby="dropdownMenuButtonNew{{ $setIndex }}">
                                                                                                        <li>
                                                                                                            <a set-index="{{ $setIndex }}"
                                                                                                                ex-index="{{ $setExerciseKey }}"
                                                                                                                onClick="copySetExercise(this, 'COPY_EXERCISE_TO_NEXT_SET')"
                                                                                                                class="dropdown-item"
                                                                                                                href="javascript:void(0)">
                                                                                                                COPY THIS
                                                                                                                SET TO
                                                                                                                NEXT SET
                                                                                                            </a>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <a set-index="{{ $setIndex }}"
                                                                                                                ex-index="{{ $setExerciseKey }}"
                                                                                                                onClick="copySetExercise(this, 'COPY_EXERCISE_TO_ALL_SET')"
                                                                                                                class="dropdown-item"
                                                                                                                href="javascript:void(0)">
                                                                                                                COPY THIS
                                                                                                                EXERCISE
                                                                                                                TO ALL SET
                                                                                                            </a>
                                                                                                        </li>
                                                                                                    </ul>
                                                                                                </div>
                                                                                            </h5>
                                                                                            <p class="card-text">
                                                                                                {{ !empty($setExercise->exercise) && !empty($setExercise->exercise->description) ? strip_tags($setExercise->exercise->description) : '' }}
                                                                                            </p>
                                                                                        </div>

                                                                                        <div class="exercise-set-select">
                                                                                            <input type="hidden"
                                                                                                name="sets[{{ $setIndex }}][{{ $setExerciseKey }}][exercise]"
                                                                                                value="{{ !empty($setExercise->exercise) ? $setExercise->exercise->id : '' }}">

                                                                                            <label>NUMBER OF REPS </label>
                                                                                            <input type="number"
                                                                                                id="number_of_reps_{{ $setIndex }}_{{ $setExerciseKey }}"
                                                                                                onChange="changeReps(this)"
                                                                                                set-index="{{ $setIndex }}"
                                                                                                ex-index="{{ $setExerciseKey }}"
                                                                                                name="sets[{{ $setIndex }}][{{ $setExerciseKey }}][reps]"
                                                                                                min="1"
                                                                                                max="99"
                                                                                                value="{{ !empty($setExercise->no_of_reps) ? $setExercise->no_of_reps : 0 }}"
                                                                                                class="form-control">

                                                                                        </div>
                                                                                        <div class="exercise-set-icons">
                                                                                            <a href="javascript:void(0)"
                                                                                                class="exercise-set-close"
                                                                                                id="ex_{{ $setIndex }}_{{ $setExerciseKey }}"
                                                                                                set-index="{{ $setIndex }}"
                                                                                                ex-index="{{ $setExerciseKey }}"
                                                                                                onclick="deleteExercise(this)"><img src="{{ asset('assets/images/close.svg') }}" alt=""></a>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                @endif
                                                            </ul>
                                                            <div class="text-center">
                                                                <button id="set_{{ $setIndex }}_add_exercise_btn"
                                                                    index_no="{{ $setIndex }}"
                                                                    onClick="viewExerciseModal(this)" type="button"
                                                                    class="btn btn-primary">ADD EXERCISE</button>
                                                            </div>
                                                            <div>
                                                                <div class="dropdown custom-dropdown">
                                                                    <button class="dropdown-toggle" type="button"
                                                                        id="dropdownMenuButton{{ $setIndex }}"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <img src="{{ url('assets/images/more.png') }}">
                                                                    </button>
                                                                    <ul class="dropdown-menu"
                                                                        aria-labelledby="dropdownMenuButton{{ $setIndex }}">
                                                                        <li><a index="{{ $setIndex }}"
                                                                                onClick="copySet(this, 'COPY_SET_TO_NEXT_SET')"
                                                                                class="dropdown-item set_{{ $setIndex }}_index"
                                                                                href="javascript:void(0)"> COPY THIS SET TO
                                                                                NEXT
                                                                                SET</a></li>
                                                                        <li><a index="{{ $setIndex }}"
                                                                                onClick="copySet(this, 'COPY_SET_TO_ALL_SET')"
                                                                                class="dropdown-item set_{{ $setIndex }}_index"
                                                                                href="javascript:void(0)"> COPY THIS SET TO
                                                                                ALL SET
                                                                            </a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="image-upload">
                                    <div class="form-group mb-3">
                                        <label class="d-block">Workout Image</label>
                                        <input class="file-upload-input" type="file" class=""
                                            onchange="setImage(this)" name="uploadImages" id="UploadImg">
                                        <a href="javascript:void(0)" class="btn btn-secondary"><img class=""
                                                src="{{ url('assets/images/file-upload.svg') }}">Workout Image</a>
                                    </div>
                                    <div class="uploaded-image-list">
                                        @if (!empty($result->media_id) && !empty($result->media->base_url))
                                            <img style="height:50px;width:50px;" id="imagePreview"
                                                src="{{ $result->media->base_url }}">
                                        @else
                                            <img style="height:50px;width:50px;" id="imagePreview"
                                                src="{{ url('assets/images/default-image.png') }}">
                                        @endif
                                    </div>
                                    <input type="hidden" id="hiddenMediaFileId" name="media_id"
                                        value="{{ !empty($result->media_id) ? $result->media_id : '' }}">
                                </div>
                            </div>

                            <div class="btn_row text-center fixed-cta">
                                @if ($result->status == 'draft')
                                    <button type="button" class="btn btn-primary ripple-effect-dark btn-120 updateBtn"
                                        onClick="updateWorkout(true)">
                                        Save As Draft
                                        <span id="addBtnLoader" class="spinner-border spinner-border-sm"
                                            style="display: none;"></span>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120 updateBtn"
                                    onclick="updateWorkout()">
                                    @if ($result->status == 'draft')
                                        Save
                                    @else
                                        Update
                                    @endif
                                    <span id="updateBtnLoader" class="spinner-border spinner-border-sm"
                                        style="display: none;"></span>
                                </button>
                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                                    href="{{ route('user.indexWorkoutExercise', ['user_type' => $userType]) }}">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content End -->
    <!-- Modal -->
    <div class="modal fade" id="exerciseListModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">EXERCISES</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onClick="hideExerciseModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="filter_section with-button filter_section_open px-0" id="searchFilterExercise">
                        <div class="filterHead d-flex justify-content-between">
                            <h3 class="h-24 font-semi">Filter</h3>
                            <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i
                                    class="iconmoon-close"></i></a>
                        </div>
                        <div class="flex-row justify-content-between align-items-end">
                            <div class="left">
                                <h5 class="fs-6 label">Search By</h5>
                                <form action="javascript:void(0)" id="searchWorkoutExerciseForm">
                                    <input type="hidden" name="limit" value="1000">
                                    <div class="form_field flex-wrap pr-0">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Search"
                                                name="search" id="searchFiledIdExercise">
                                        </div>
                                        <div class="form-group">
                                            <select id="modal_filter_category_id" name="category_id"
                                                class="js-states form-control selectpicker">
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="modal_filter_difficulty_id" name="difficulty_id"
                                                class="js-states form-control selectpicker">
                                                <option value="">Select Difficulty</option>
                                                @foreach ($difficulties as $difficulty)
                                                    <option value="{{ $difficulty->id }}">
                                                        {{ $difficulty->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="modal_filter_equipment_id" name="equipment_id"
                                                class="js-states form-control selectpicker">
                                                <option value="">Select Equipment</option>
                                                @foreach ($equipments as $equipment)
                                                    <option value="{{ $equipment->id }}">
                                                        {{ $equipment->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="modal_filter_sport_id" name="sport_id"
                                                class="js-states form-control selectpicker">
                                                <option value="">Select Sport</option>
                                                @foreach ($sports as $sport)
                                                    <option value="{{ $sport->id }}">{{ $sport->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="btn_clumn mb-3 position-sticky">
                                            <button type="button" onClick="viewExerciseModal('', true)"
                                                class="btn btn-secondary ripple-effect">Search</button>
                                            <button type="button" class="btn btn-outline-danger ripple-effect"
                                                id="clearSearchFilterIdExercise"
                                                onClick="resetExerciseModalFilter()">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="exerciseListDiv"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onClick="hideExerciseModal()"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.image-cropper-modal')
@endsection

@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\WorkoutExerciseRequest', '#updateForm') !!}
    <script src="{{ url('assets/custom/image-cropper.js') }}"></script>
    <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
    <script>
        let selectedSetIndex = '';
        let setsObj = {};
        let setExercises = @json($result->sets) || [];
        const defaultImage = "{{ url('assets/images/default-workout-image.jpg') }}";
        document.getElementById('totalSets').value = setExercises.length;
        setExercises.forEach((obj, index) => {
            let exercisesArr = [];
            obj.workout_set_exercises.forEach((workoutExObj, wexindex) => {
                //workoutExObj = JSON.parse(JSON.stringify(workoutExObj));
                workoutExObj['exercise']['selected_reps'] = workoutExObj.no_of_reps || '';
                exercisesArr.push(workoutExObj.exercise);
            });
            setsObj[index + 1] = {
                exercises: exercisesArr
            };

            $('#set_' + index + '_exercises').sortable({
                group: 'list',
                animation: 200,
                //ghostClass: 'ghost',
                refreshPositions: true
            });
        });

        tinymce.init({
            theme: "modern",
            mode: "specific_textareas",
            editor_selector: "text-editor",
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            relative_urls: false,
            remove_script_host: true,
            convert_urls: false,

            // Removed 'code' plugin
            plugins: 'preview searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern',

            // Removed 'code' button from toolbar
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat',

            height: 200,
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('updateForm').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });

            $('#days').select2({
                placeholder: "Select Days",
                allowClear: true
            });

            $('#difficulty_id').select2({
                placeholder: "Select Difficulty",
                allowClear: true
            });

            $('#age_range_id').select2({
                placeholder: "Select Age Group",
                allowClear: true
            });

            $('#sport_id').select2({
                placeholder: "Select Sport",
                allowClear: true
            });

            $('#category_id').select2({
                placeholder: "Select Category",
                allowClear: true
            });

            $('#athlete_user_ids').select2({
                placeholder: "Select Athlete",
                allowClear: true
            });

            $('#group_ids').select2({
                placeholder: "Select Group",
                allowClear: true
            });


        });

        function updateWorkout(isDraft) {
            let hasError = false;
            let missingExerciseSets = [];

            for (let setId in setsObj) {
                if (!setsObj[setId].exercises || setsObj[setId].exercises.length === 0) {
                    hasError = true;
                    missingExerciseSets.push(setId);
                }
            }

            if (hasError) {
                _toast.error(`Each set must have at least one exercise. Missing in sets: ${missingExerciseSets.join(', ')}`);
                return; 
            }

            var formData = $("#updateForm").serializeArray();
            if (isDraft) {
                formData.push({
                    name: 'is_draft',
                    value: 1
                });
            }
            //if ($('#updateForm').valid()) {
            $('.updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{ route('common.updateExerciseWorkout', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', "{{ $id }}");
            $.ajax({
                type: "PUT",
                url: url,
                data: formData,
                success: function(response) {
                    $('.updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (response.success) {
                        if (!isDraft) {
                            _toast.success(response.message);
                            localStorage.setItem('activeTab', 'Workout');
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.indexWorkoutExercise', ['user_type' => $userType]) }}";
                            }, 500);
                        } else {
                            _toast.success('Data successfully saved.');
                        }
                    } else {
                        _toast.error('Something went wrong. Please try again');
                    }
                },
                error: function(err) {
                    $('.updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (err.status === 422) {
                        $(".error-help-block").hide();
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            console.log("#" + key + "-error");
                            $("#" + key + "-error").text(val);
                            $("#" + key + "-error").show();
                        });
                    } else {
                        _toast.error('Workout not updated.');
                    }
                },
            });
            // } else {
            //     console.log("Form is not valid");
            // }
        }

        function createSets(addAnotherSet = false) {
            let setHtml = ``;
            $("#howManySetsOfDiv").hide();
            if (addAnotherSet) {
                const setsArr = Object.keys(setsObj);
                const maxIndex = Math.max(...setsArr);
                const i = maxIndex + 1;
                setHtml += `<div class="col-md-6 sets-div" id="set_` + i + `">
                                <div class="card">
                                    <div class="card-body">
                                        <input type="hidden"
                                        id="set_` + i + `_field"
                                        name="sets[` + i + `]"
                                        value="` + i + `">

                                        <div class="exercises-set-head">
                                            <h4 id="set_` + i + `_title">
                                                SET ` + (setsArr.length + 1) + `
                                            </h4>
                                            <a href="javascript:void(0)"
                                                class="set-close set_` + i + `_index"
                                                index="` + i + `"
                                                onClick="deleteSet(this)"><img src="{{ asset('assets/images/close.svg') }}" alt=""></a>
                                        </div>
                                        <ul class="row mt-4 mb-4" id="set_` + i + `_exercises"></ul>
                                        <div class="text-center">
                                            <button id="set_` + i + `_add_exercise_btn"
                                            index_no="` + i + `"
                                            onClick="viewExerciseModal(this)"
                                            type="button" class="btn btn-primary">ADD EXERCISE</button>
                                        </div>

                                        <div>
                                            <div class="dropdown custom-dropdown">
                                                <button class="dropdown-toggle" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <img src="{{ url('assets/images/more.png') }}">
                                                </button>
                                                <ul class="dropdown-menu"
                                                    aria-labelledby="dropdownMenuButton1">
                                                    <li>
                                                        <a index="` + i + `"
                                                            onClick="copySet(this, 'COPY_SET_TO_NEXT_SET')"
                                                            class="dropdown-item set_` + i + `_index"
                                                            href="javascript:void(0)"> COPY THIS SET TO NEXT
                                                            SET
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a index="` + i + `"
                                                            onClick="copySet(this, 'COPY_SET_TO_ALL_SET')"
                                                            class="dropdown-item set_` + i + `_index"
                                                            href="javascript:void(0)"> COPY THIS SET TO ALL SET
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                setsObj[i] = {
                    reps: '',
                    exercises: []
                };
                document.getElementById('totalSets').value = Object.keys(setsObj).length;
            } else {
                let selectedSets = $("#totalSets").val() || 0;
                $("#howManySetsOfDiv").hide();
                $("#setListDiv").show();
                $("#setsDiv").html(``);
                if (selectedSets) {
                    for (let i = 1; i <= selectedSets; i++) {
                        setHtml += `<div class="col-md-6 sets-div" id="set_` + i + `">
                                        <div class="card">
                                            <div class="card-body">
                                                <input type="hidden"
                                                id="set_` + i + `_field"
                                                name="sets[` + i + `]"
                                                value="` + i + `">
                                                <div class="exercises-set-head">
                                                    <h4 id="set_` + i + `_title">
                                                        SET ` + i + `
                                                    </h4>
                                                    <a href="javascript:void(0)"
                                                        class="set-close set_` + i + `_index"
                                                        index="` + i + `"
                                                        onClick="deleteSet(this)"><img src="{{ asset('assets/images/close.svg') }}" alt=""></a>
                                                </div>
                                                <ul class="row mt-4 mb-4" id="set_` + i + `_exercises"></ul>
                                                <div class="text-center">
                                                    <button id="set_` + i + `_add_exercise_btn" index_no="` + i + `" onClick="viewExerciseModal(this)" type="button" class="btn btn-primary">ADD EXERCISE</button>
                                                </div>
                                                <div>
                                                    <div class="dropdown custom-dropdown">
                                                        <button class="dropdown-toggle" type="button"
                                                            id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <img src="{{ url('assets/images/more.png') }}">
                                                        </button>
                                                        <ul class="dropdown-menu"
                                                            aria-labelledby="dropdownMenuButton1">
                                                            <li>
                                                                <a index="` + i + `"
                                                                    onClick="copySet(this, 'COPY_SET_TO_NEXT_SET')"
                                                                    class="dropdown-item set_` + i + `_index"
                                                                    href="javascript:void(0)"> COPY THIS SET TO NEXT
                                                                    SET
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a index="` + i + `"
                                                                    onClick="copySet(this, 'COPY_SET_TO_ALL_SET')"
                                                                    class="dropdown-item set_` + i + `_index"
                                                                    href="javascript:void(0)"> COPY THIS SET TO ALL SET
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                        setsObj[i] = {
                            reps: '',
                            exercises: []
                        };
                    }
                } else {
                    $("#setListDiv").hide();
                }
            }
            $("#setsDiv").append(setHtml);
        }

        function deleteSet(evnt) {
            const setIndex = $(evnt).attr("index");
            $("#set_" + setIndex).remove();
            const availableSets = $('.sets-div').length;
            if (!availableSets) {
                $("#setListDiv").hide();
                $("#setsDiv").append(``);
                $("#totalSets").val('');
                $("#howManySetsOfDiv").show();
            }
            $('.sets-div').each(function(i, obj) {
                let indexVal = i + 1;
                $("#" + obj.id + "_title").text("SET " + (indexVal));
            })
            delete setsObj[setIndex];
        }



        function viewExerciseModal(evnt, isSearch = false) {
            let url = "{{ route('common.workout.loadExerciseList') }}";
            let formData = $('#searchWorkoutExerciseForm').serialize();
            let setIndex = evnt ? $(evnt).attr("index_no") : '';
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $("#exerciseListDiv").html("");
                        $('#exerciseListDiv').append(response.data.html);
                        if (!isSearch) {
                            selectedSetIndex = setIndex;
                            $("#exerciseListModal").modal("show");
                        }
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });
        }

        function hideExerciseModal() {
            $("#exerciseListModal").modal("hide");
        }

        function addToSet(data, setIndex = '') {
            let setExcHtml = ``;
            selectedSetIndex = setIndex != '' ? setIndex : selectedSetIndex;
            if (selectedSetIndex != '') {
                let exerciseSetIndex = $('.exercise-set-' + selectedSetIndex).length;
                setExcHtml += ` <li class="col-md-6" id="exercise_li_` + selectedSetIndex + `_` + exerciseSetIndex + `"
                                    data-id="` + exerciseSetIndex + `"
                                    parent-id="set_` + selectedSetIndex + `_exercises"
                                    data-value="name">
                                <div class=" exercise-set-head pb-3 exercise-set-` + selectedSetIndex + `"
                                id="exercise_div_` + selectedSetIndex + `_` + exerciseSetIndex + `">
                                <div class="card">
                                    <div class="exercise-set-img">
                                        <img src="` + (data.media ? data.media.base_url : defaultImage) + `">
                                    </div>
                                     <div class="card-body">
                                        <div class="exercise-set-desc">
                                            <h5 class="card-title">` + data.name + `
                                                <div class="dropdown">
                                                <button class="dropdown-toggle"
                                                    type="button"
                                                    id="dropdownMenuButton1"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <img src="{{ url('assets/images/more.png') }}">
                                                </button>
                                                <ul class="dropdown-menu"
                                                    aria-labelledby="dropdownMenuButton1">
                                                    <li>
                                                        <a  set-index="` + selectedSetIndex + `"
                                                            ex-index="` + exerciseSetIndex + `"
                                                            onClick="copySetExercise(this, 'COPY_EXERCISE_TO_NEXT_SET')"
                                                            class="dropdown-item"
                                                            href="javascript:void(0)">
                                                            COPY THIS EXERCISE TO NEXT SET
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a  set-index="` + selectedSetIndex + `"
                                                            ex-index="` + exerciseSetIndex + `"
                                                            onClick="copySetExercise(this, 'COPY_EXERCISE_TO_ALL_SET')"
                                                            class="dropdown-item"
                                                            href="javascript:void(0)">
                                                            COPY THIS EXERCISE TO ALL SET
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div></h5>
                                            <p class="card-text">` + data.description + `</P>
                                        </div>
                                        <div class="exercise-set-select">
                                            <input type="hidden"
                                            name="sets[` + selectedSetIndex + `][` + exerciseSetIndex + `][exercise]"
                                            value="` + data.id + `">
                                            <label>NUMBER OF REPS </label>
                                                <input type="number" id="number_of_reps_` + selectedSetIndex + `"
                                                    onChange="changeReps(this)"
                                                    set-index="` + selectedSetIndex + `"
                                                    ex-index="` + exerciseSetIndex + `"
                                                    name="sets[` + selectedSetIndex + `][` + exerciseSetIndex + `][reps]"
                                                    min="1" max="99"
                                                    value="` + (data.selected_reps ? data.selected_reps : 0) + `"
                                                    class="form-control">
                                        </div>
                                        <div class="exercise-set-icons">
                                            <a href="javascript:void(0)"
                                            class="exercise-set-close"
                                            set-index="` + selectedSetIndex + `"
                                            ex-index="` + exerciseSetIndex + `"
                                            onClick="deleteExercise(this)"><img src="{{ asset('assets/images/close.svg') }}" alt=""></a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            </li>`;
                $('#set_' + selectedSetIndex + `_exercises`).append(setExcHtml);
                if (setsObj[selectedSetIndex]) {
                    setsObj[selectedSetIndex].exercises.push(data);
                };

                $('#set_' + selectedSetIndex + '_exercises').sortable({
                    group: 'list',
                    animation: 200,
                    //ghostClass: 'ghost',
                    refreshPositions: true
                });
            }

        }

        function resetExerciseModalFilter() {
            $('#searchWorkoutExerciseForm')[0].reset();
            viewExerciseModal('', true);
        }

        function deleteExercise(evnt) {
            const setIndex = $(evnt).attr("set-index");
            const setExIndex = $(evnt).attr("ex-index");
            $("#exercise_div_" + setIndex + `_` + setExIndex).remove();
            if (setsObj[setIndex]) {
                if (setsObj[setIndex].exercises.length == 1) {
                    setsObj[setIndex].exercises = [];
                    console.log("====11====");
                } else {
                    setsObj[setIndex].exercises.splice(setExIndex, 1);
                    console.log("====22====");
                }
            }
        }

        function copySet(evnt, type) {
            const setIndex = $(evnt).attr("index");
            //const nextIndex = parseInt(setIndex) + 1;
            if (type == 'COPY_SET_TO_NEXT_SET') {
                // console.log("====copy setIndex====", setIndex, "====next nextIndex", nextIndex);
                // console.log("====setsObj====", setsObj);
                const indexArr = Object.keys(setsObj);
                const currentIndex = indexArr.indexOf(setIndex);
                const nextIndex = currentIndex != -1 && indexArr[(parseInt(currentIndex) + 1)] ? indexArr[(parseInt(
                    currentIndex) + 1)] : '';
                if (setIndex >= 0 && setsObj[setIndex] && setsObj[nextIndex]) {
                    let exercises = setsObj[setIndex].exercises || [];
                    exercises.forEach((obj) => {
                        addToSet(obj, nextIndex);
                    })
                }
            } else if (type == 'COPY_SET_TO_ALL_SET') {
                let exercises = setsObj[setIndex].exercises || [];
                Object.keys(setsObj).forEach((index) => {
                    if (setIndex != index) {
                        console.log(setsObj[index]);
                        exercises.forEach((obj) => {
                            addToSet(obj, index);
                        })
                    }
                })
            }
        }

        function copySetExercise(evnt, type) {
            const setIndex = $(evnt).attr("set-index");
            const setExIndex = $(evnt).attr("ex-index");

            if (type == 'COPY_EXERCISE_TO_NEXT_SET') {
                const indexArr = Object.keys(setsObj);
                const currentIndex = indexArr.indexOf(setIndex);
                const nextIndex = currentIndex != -1 && indexArr[(parseInt(currentIndex) + 1)] ? indexArr[(parseInt(
                    currentIndex) + 1)] : '';
                const exerciseObj = setsObj[setIndex].exercises[setExIndex];
                // console.log("====indexArr====", indexArr, "====copy setIndex====", setIndex, "====next nextIndex",
                //     nextIndex);
                // console.log("====setsObj====", setsObj);
                if (nextIndex >= 0) {
                    addToSet(exerciseObj, nextIndex);
                }

            } else if (type == 'COPY_EXERCISE_TO_ALL_SET') {
                let exercise = setsObj[setIndex].exercises && setsObj[setIndex].exercises[setExIndex] ? setsObj[setIndex]
                    .exercises[setExIndex] : [];
                Object.keys(setsObj).forEach((index) => {
                    if (setIndex != index) {
                        console.log(setsObj[index]);
                        addToSet(exercise, index);
                    }
                })
            }
        }

        function changeReps(evnt) {
            const setIndex = $(evnt).attr("set-index");
            const setExIndex = $(evnt).attr("ex-index");

            if (setsObj[setIndex] && setsObj[setIndex].exercises[setExIndex]) {
                setsObj[setIndex].exercises[setExIndex]['selected_reps'] = evnt.value;
            }
        }
    </script>
@endsection
