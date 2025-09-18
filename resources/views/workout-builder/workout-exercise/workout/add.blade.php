@extends('layouts.app')

@section('head')
    <title>Workout | Add</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php 
    $userType = userType(); 
    $user = getUser();
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
                        <li class="breadcrumb-item active">Create Workout</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Create Workout
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.saveWorkoutExercise') }}">
                @csrf
                <!-- Workout Form Start -->
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
                                    <input class="form-control" id="name" type="text" name="name"
                                        placeholder="Workout Name">
                                    <span id="name-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label>Visibility (in workout directory)</label>
                                    <select id="visibility" name="visibility" class="form-control">
                                        <option value="">Select Visibility</option>
                                        <option value="hidden">Hidden</option>
                                        <option value="visible">Visible</option>
                                    </select>
                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_of_reps">Exercise (No. of reps)</label>
                                    <select class="form-control" id="no_of_reps" name="no_of_reps">
                                        <option value="">Select Exercise</option>
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="difficulty_id">Difficulty<span class="text-danger">*</span></label>
                                    <select id="difficulty_id" name="difficulty_id[]"
                                        class="js-states form-control selectpicker" multiple>
                                        <option value="">Select Difficulty</option>
                                        @foreach ($difficulties as $difficult)
                                            @if ($difficult->status == 'active')
                                                <option value="{{ $difficult->id }}">{{ $difficult->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <span id="difficulty_id-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            @if($userType == 'admin' || $userType == 'coach')
                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="age_range_id">Age Group</label>
                                    <select id="age_range_id" name="age_range_id[]"
                                        class="js-states form-control selectpicker" multiple>
                                        <option value="">Select Age Group</option>
                                        @foreach ($ageRanges as $ageRange)
                                            @if ($ageRange->status == 'active')
                                                <option value="{{ $ageRange->id }}">{{ $ageRange->min_age_range }} -
                                                    {{ $ageRange->max_age_range }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <span id="age_range_id-error" class="help-block error-help-block text-danger"></span>
                            </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="sport_id">Sport Selection</label>
                                    <select id="sport_id" name="sport_id[]" class="js-states form-control selectpicker"
                                        multiple>
                                        <option value="">Select Sport</option>
                                        @foreach ($sports as $sport)
                                            @if ($sport->status == 'active')
                                                <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="category_id">Category<span class="text-danger">*</span></label>
                                    <select id="category_id" name="category_id[]"
                                        class="js-states form-control selectpicker" multiple>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            @if ($category->status == 'active')
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <span id="category_id-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group multi-select {{$userType == 'athlete' ? 'athlete' : ''}}">
                                    <label for="athlete_user_ids">Select Athlete</label>
                                    <select id="athlete_user_ids" name="athlete_user_ids[]"
                                        class="js-states form-control selectpicker" multiple
                                        @if($userType != 'admin') disabled @endif>
                                        @if($userType === 'admin')
                                            <option value="">Select Athlete</option>
                                        @endif
                                        @foreach ($athletes as $athlete)
                                            @if($userType == 'admin')
                                                <option value="{{ $athlete->id }}">
                                                    {{ ucfirst($athlete->first_name) . ' ' . $athlete->last_name }}</option>
                                            @else
                                                @if($athlete->id == $user->id)
                                                <option value="{{ $athlete->id }}" selected>
                                                    {{ ucfirst($athlete->first_name) . ' ' . $athlete->last_name }}</option>
                                                <input type="hidden" name="athlete_user_ids[]" value="{{ $athlete->id }}">
                                                @endif
                                            @endif
                                        @endforeach
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
                                                <option value="{{ $group->id }}">
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
                                        <option value="monday">Monday</option>
                                        <option value="tuesday">Tuesday</option>
                                        <option value="wednesday">Wednesday</option>
                                        <option value="thursday">Thursday</option>
                                        <option value="friday">Friday</option>
                                        <option value="saturday">Saturday</option>
                                        <option value="sunday">Sunday</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exerciseDescription" class="form-label">Description</label>
                                    <textarea class="form-control text-editor" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12" id="howManySetsOfDiv">
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

                            <div class="col-md-12   " id="setListDiv" style="display: none">
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
                                        <div id="setsDiv" class="row"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="image-upload">
                                    <div class="form-group mb-3">
                                        <label class="d-block"> Workout Image</label>
                                        <input type="file" class="file-upload-input" onchange="setImage(this)"
                                            name="uploadImages" id="UploadImg">
                                        <a href="javascript:void(0)" class="btn btn-secondary">
                                            <img src="{{ url('assets/images/file-upload.svg') }}"> Workout Image
                                        </a>
                                    </div>
                                    <div class="uploaded-image-list">
                                        <img style="height:50px;width:50px;" id="imagePreview"
                                            src="{{ url('assets/images/default-image.png') }}">
                                    </div>
                                    <input type="hidden" id="hiddenMediaFileId" name="media_id">
                                </div>
                            </div>
                            <div class="btn_row text-center fixed-cta">
                                <button type="button" class="btn btn-primary ripple-effect-dark btn-120 addBtn"
                                    onClick="addWorkouts(true)">
                                    Save As Draft
                                    <span id="addBtnLoader" class="spinner-border spinner-border-sm"
                                        style="display: none;"></span>
                                </button>
                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120 addBtn"
                                    onClick="addWorkouts()">
                                    Add
                                    <span id="addBtnLoader" class="spinner-border spinner-border-sm"
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
    <!-- Game Modal  -->
      @if(!empty($rewardDetail) && !empty($rewardDetail->reward_game) && $rewardDetail->is_gamification == 1)
            <x-game-modal :rewardDetail="$rewardDetail" :module="'workout-builder'" />
        @endif
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
    {!! JsValidator::formRequest('App\Http\Requests\WorkoutExerciseRequest', '#addForm') !!}
    <script src="{{ url('assets/custom/image-cropper.js') }}"></script>
    <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
    <script>
        let selectedSetIndex = '';
        let setsObj = {};
        const defaultImage = "{{ url('assets/images/default-workout-image.jpg') }}";
        
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
            document.getElementById('addForm').addEventListener('keydown', function(event) {
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

        function addWorkouts(isDraft = false) {
            const rewardData = @json($rewardDetail ?? null);
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
            let formData = $("#addForm").serializeArray();
            if (isDraft) {
                formData.push({
                    name: 'is_draft',
                    value: 1
                });
            }
            // console.log(formData);
            // return;
            //if ($('#addForm').valid()) {
            $('.addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.saveWorkoutExercise') }}",
                data: formData,
                success: function(response) {
                    $('.addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        if (!isDraft) {
                            _toast.success(response.message);
                            localStorage.setItem('activeTab', 'Workout');
                            $('#addForm')[0].reset();
                            setTimeout(function() {
                                if(rewardData && rewardData.is_gamification == 1 && rewardData.reward_game){
                                    const userId = @json($user->id ?? null);
                                    const rewardId = rewardData.id;
                                    const modalId = '#gameModal_' + userId + '_' + rewardId;
                                    $(modalId).modal('show'); // updated here
                                }else{
                                    window.location.href =
                                        "{{ route('user.indexWorkoutExercise', ['user_type' => $userType]) }}";
                                }
                            }, 500);
                        } else {
                            _toast.success('Data successfully saved.');
                            setTimeout(function() {
                                console.log(response);
                                let url =
                                    "{{ route('user.editFormWorkout', ['id' => '%recordId%', 'user_type' => $userType]) }}";
                                window.location.href = url.replace('%recordId%', response.data.id);
                            }, 500);
                        }

                    } else {
                        _toast.error('Something went wrong. Please try again');
                    }
                },
                error: function(err) {
                    $('.addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (err.status === 422) {
                        $(".error-help-block").hide();
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                            $("#" + key + "-error").show();
                        });
                    } else {
                        _toast.error('Workout not created.');
                    }
                },
            });
            // } else {
            //     console.log("Form is not valid"); // Debugging: Print form validation status
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
                                                onClick="deleteSet(this)"> <img src="{{ asset('assets/images/close.svg') }}" alt=""> </a>
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
            } else {
                let selectedSets = $("#totalSets").val() || 0;
                $("#howManySetsOfDiv").hide();
                $("#setListDiv").show();
                $("#setsDiv").html(``);
                if (selectedSets) {
                    for (let i = 1; i <= selectedSets; i++) {
                        setHtml += `<div class="col-md-6 sets-div" id="set_` + i + `" data-id="` + i + `">
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
            $('#setsDiv').sortable({
                group: 'list',
                animation: 200,
                //ghostClass: 'ghost',
                refreshPositions: true,
                onSort: sortedData,
            });
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
                        if (response.data.html && response.data.html.trim() !== "") {
                            $('#exerciseListDiv').append(response.data.html);
                        } else {
                            $('#exerciseListDiv').html('<div class="text-center text-muted">No exercises available, please add one under the exercise section first.</div>');
                        }
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
                setExcHtml += ` <li class="col-md-6"
                                    id="exercise_li_` + selectedSetIndex + `_` + exerciseSetIndex + `"
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
                                                </div>
                                            </h5>
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

                $('#set_' + selectedSetIndex + `_exercises`).sortable({
                    group: 'list',
                    animation: 200,
                    //ghostClass: 'ghost',
                    refreshPositions: true,
                    //onSort: sortedData,
                });
                _toast.success("Exercise successfully added.");
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
            //console.log("==setIndex==", setIndex, "==setExIndex==", setExIndex);

            if (setsObj[setIndex] && setsObj[setIndex].exercises[setExIndex]) {
                setsObj[setIndex].exercises[setExIndex]['selected_reps'] = evnt.value;
            }
            console.log(setsObj);
        }

        function sortedData(evnt) {
            // let newSetsObj = {};
            // const sort2 = $('#setsDiv').sortable('toArray');
            // sort2.forEach((index) => {
            //     if (setsObj[index]) {
            //         console.log(index);
            //         newSetsObj[index] = setsObj[index];
            //     }
            // });
            // console.log("==================sort================", sort2);
            // console.log("==================setsObj================", setsObj);
            // console.log("==================newSetsObj================", newSetsObj);
        };
    </script>
@endsection
