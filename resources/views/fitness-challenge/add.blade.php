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
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.fitnessChallenge', ['user_type' => $userType]) }}">Fitness Challenges</a></li>
                        <li class="breadcrumb-item active">Create Fitness Challenge</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Create Fitness Challenge
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="">
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
                                    <label for="name" class="form-label">Title <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" id="name" type="text" name="name"
                                        placeholder="Title">
                                    <span id="name-error" class="help-block error-help-block text-danger"></span>
                                </div>
                          </div>

                         <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Type" class="form-label">Type<span class="text-danger">*</span></label>
                                    <select id="Type" onChange="changeChallengeType(event)" class="form-control form-select" name="type">
                                        <option value="">Select Challenge Type</option>
                                        <option value="workouts">Workouts</option>
                                        <option value="sleep-tracker">Sleep Tracker</option>
                                        <option value="step-counter">Step Counter</option>
                                        <option value="food-tracker">Food Tracker</option>
                                        <option value="water-intake">Water Intake</option>
                                    </select>
                                    <span id="type-error" class="help-block error-help-block text-danger"></span>
                                </div>
                          </div>
                          <div class="col-md-6" id="workoutIdCol">
                                <div class="form-group">
                                    <label for="workoutId"  class="form-label">Select Workout<span class="text-danger">*</span></label>
                                    <select id="workoutId"  class="form-control form-select"  name="workout_id">
                                        <option value="">Select Workout</option>
                                        @foreach ($workouts as $workout)
                                        <option value="{{$workout->id}}">{{ ucfirst($workout->name)}}</option>
                                        @endforeach
                                    </select>
                                     <span id="workout_id-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date" class="form-label">Go Live Date<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" id="date" type="date" name="live_date"
                                        placeholder="Date">
                                    <span id="live_date-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group multi-select {{$userType == 'athlete' ? 'athlete' : ''}}">
                                    <label for="user_role_ids">Available to <span
                                            class="text-danger">*</span></label>
                                    <select id="user_role_ids" name="user_role_ids[]"
                                    class="js-states form-control selectpicker" multiple>
                                        <option value="">Select User Roles</option>
                                        @foreach ($userRoles as $user_role)
                                            <option value="{{ $user_role->id }}" data-title="{{ $user_role->name }}">{{ ucfirst($user_role->name)}}</option>
                                        @endforeach
                                    </select>
                                        <span id="user_role_ids-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6" id="planIdCol">
                                <div class="form-group multi-select {{$userType == 'athlete' ? 'athlete' : ''}}">
                                    <label for="user_plan_ids">Plans<span class="text-danger">*</span></label>
                                    <select id="user_plan_ids" name="user_plan_ids[]" class="js-states form-control selectpicker" multiple>
                                        <option value="">Select Plan</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ ucfirst($plan->name)}}</option>
                                        @endforeach
                                    </select>
                                    <span id="user_role_ids-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label>Days <span class="text-danger">*</span></label>
                                    <input name="days" type="number" class="form-control">
                                    <span id="days-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="teaserDescription" class="form-label">Teaser Description</label>
                                    <textarea class="form-control text-editor" id="teaserDescription" name="teaser_description" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control text-editor" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group custom-radio">
                                    <label class="form-label" for="leaderboard">Leaderboard: &nbsp;</label>
                                    <label class="form-check">
                                        <input type="radio" class="schedule-time" value="1" name="leaderboard" >
                                        <span>Yes</span>
                                    </label>
                                    <label class="form-check">
                                        <input type="radio" class="schedule-time" value="0" name="leaderboard">
                                        <span>No</span>
                                    </label>
                                    <div>
                                        <span id="leaderboard-error" class="help-block error-help-block text-danger"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="btn_row text-center fixed-cta">
                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120"
                                    onClick="addChallenge()" id="addBtn">
                                    Add
                                    <span id="addBtnLoader" class="spinner-border spinner-border-sm"
                                        style="display: none;"></span>
                                </button>
                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{ route('user.fitnessChallenge', ['user_type' => $userType]) }}">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\FitnessChallengeRequest', '#addForm') !!}
    <script>
        let challengeType = 'workouts';
        let workouts = @json($workouts);
        $('#workoutIdCol').hide();
        $('#planIdCol').hide();

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
         
            $('#user_role_ids').select2({
                placeholder: "Select User Roles",
                allowClear: true
            });
            $('#user_plan_ids').select2({
                placeholder: "Select Plan",
                allowClear: true
            });

            $("#user_role_ids").on('change', function () {
                let selectedOptions = $(this).find('option:selected');
                let hasAthlete = false;
                selectedOptions.each(function () {
                    if ($(this).data('title')?.toLowerCase() === 'athlete') {
                        hasAthlete = true;
                        return false; // break loop
                    }
                });

                if (hasAthlete) {
                    $('#planIdCol').show();
                } else {
                    $('#planIdCol').hide();
                }
            });
        });

        function changeChallengeType(event) {
            const challengeType = event.target.value;
            if (challengeType === 'workouts') {
                $('#workoutIdCol').show();
            } else {
                $('#workoutIdCol').hide();
            }
        }

        function addChallenge() {
            let formData = $("#addForm").serializeArray();
            $.ajax({
                type: "POST",
                url: "{{ route('common.addChallenge') }}",
                data: formData,
                beforeSend: function () {
                    $("#addBtn").prop('disabled', true);
                    $("#addBtnLoader").show();
                },
                success: function(response) {
                    // $('#addBtn').prop('disabled', false);
                    // $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success('Challenge successfully saved.');
                        setTimeout(function() {
                            window.location.href = "{{ route('user.fitnessChallenge', ['user_type' => $userType]) }}";
                        }, 500);
                    } else {
                        _toast.error('Something went wrong. Please try again');
                    }
                },
                error: function(err) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (err.status === 422) {
                        $(".error-help-block").hide();
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                            $("#" + key + "-error").show();
                        });
                    } else {
                        _toast.error('Challenge not created.');
                    }
                },
            });
        }
    </script>
@endsection

<!-- 
        function changeChallengeType(event){
            challengeType = event.target.value;
            if (challengeType === 'Workouts') {
                var selectWorkouts = `<div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Select Workout<span class="text-danger">*</span></label>
                                            <select class="form-control form-select" name="workout_id" id="workout_id">
                                                <option value="">Select Workout</option>
                                                ${
                                                    workouts.map((workout, index) => {
                                                        return `<option value="${workout.id}">${workout.name}</option>`;
                                                    }).join('')
                                                }
                                            </select>
                                            <span id="name-error" class="help-block error-help-block text-danger"></span>
                                        </div>
                                    </div>`;
                $('#inputAccType').html(selectWorkouts);
            }else if(challengeType === 'Food Tracker'){
                var mealInputs = `<div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Calories<span class="text-danger">*</span></label>
                                            <input class="form-control" name="calories" type="number"/>
                                            <span id="calories-error" class="help-block error-help-block text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Proteins<span class="text-danger">*</span></label>
                                            <input class="form-control" name="proteins" type="number"/>
                                            <span id="proteins-error" class="help-block error-help-block text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Carbohydrates<span class="text-danger">*</span></label>
                                            <input class="form-control" name="carbohydrates" type="number"/>
                                            <span id="carbohydrates-error" class="help-block error-help-block text-danger"></span>
                                        </div>
                                    </div>`;
                $('#inputAccType').html(mealInputs);
            }else{
                var mealInputs = `<div class="col-md-6">
                                        <div class="form-group">
                                            <label for="goal" class="form-label">Input Goal<span class="text-danger">*</span></label>
                                            <input class="form-control" name="goal" type="number"/>
                                            <span id="goal-error" class="help-block error-help-block text-danger"></span>
                                        </div>
                                    </div>`;
                $('#inputAccType').html(mealInputs);
            }

        } -->