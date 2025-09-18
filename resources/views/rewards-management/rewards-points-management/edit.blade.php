@extends('layouts.app')
@section('head')
    <title>Rewards Management | Update</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $id = request()->route('id');
        $userType = userType();
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">

        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.rewardManagement.index', ['user_type' => $userType]) }}"> Reward Point
                                Management</a>
                        </li>
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Update Reward Point Management
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false"
                action="{{ route('common.updateRewardManagement', ['id' => $id]) }}">
                @csrf
                <div class="form-group permission-checkbox">
                    <label class="form-check form-switch max-fit">
                        <input class="form-check-input" {{ !empty($result->reward_game)?'checked':'' }} onchange="enableGamification(this)" type="checkbox" role="switch" name="is_gamification" id="gamification">
                        <div class="checkbox__checkmark"></div>
                        <div class="form-check-label fw-bold">Gamification</div>
                    </label>
                </div>
                <div class="row" >
                    <div class="col-md-12" >
                        <!-- <div class="card card-primary">
                                                                                                        <div class="card-body"> -->
                        <div class="row" id="required-inputs">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Feature<span class="text-danger">*</span></label>
                                    <input type="hidden" value="{{ $result->feature }}" id="featureField" name="feature">
                                    <select class="form-control form-select" onChange="selectFeature()" id="featureKeyField"
                                        name="feature_key">
                                        <option value="">Select Feature</option>
                                        <option value="use-recipe"
                                            {{ $result->feature_key == 'use-recipe' ? 'selected' : '' }}>Use a Recipe
                                        </option>
                                        <option value="rate-recipe"
                                            {{ $result->feature_key == 'rate-recipe' ? 'selected' : '' }}>Rate a Recipe
                                        </option>
                                        <option value="watch-exercise-video"
                                            {{ $result->feature_key == 'watch-exercise-video' ? 'selected' : '' }}>Watch an
                                            exercise video
                                        </option>
                                        <option value="watch-training-video"
                                            {{ $result->feature_key == 'watch-training-video' ? 'selected' : '' }}>Watch an
                                            training video
                                        </option>
                                        <option value="watch-workout-video"
                                            {{ $result->feature_key == 'watch-workout-video' ? 'selected' : '' }}>Watch a
                                            workout video</option>
                                        <option value="rate-video"
                                            {{ $result->feature_key == 'rate-video' ? 'selected' : '' }}>Rate a Video
                                        </option>
                                        <option value="log-health-markers"
                                            {{ $result->feature_key == 'log-health-markers' ? 'selected' : '' }}>Log your
                                            Health Markers</option>
                                        <option value="log-health-measurement"
                                            {{ $result->feature_key == 'log-health-measurement' ? 'selected' : '' }}>Log
                                            your Health Measurement</option>
                                        <option value="log-water-intake"
                                            {{ $result->feature_key == 'log-water-intake' ? 'selected' : '' }}>Log your
                                            Water Intake</option>
                                        <option value="upgrade-your-subscription"
                                            {{ $result->feature_key == 'upgrade-your-subscription' ? 'selected' : '' }}>
                                            Upgrade your Subscription</option>
                                        <option value="build-own-workout"
                                            {{ $result->feature_key == 'build-own-workout' ? 'selected' : '' }}>Build your
                                            own workout</option>
                                        <option value="complete-workout"
                                            {{ $result->feature_key == 'complete-workout' ? 'selected' : '' }}>Complete a
                                            workout</option>
                                        <option value="create-workout-goal"
                                            {{ $result->feature_key == 'create-workout-goal' ? 'selected' : '' }}>Create a
                                            Workout Goal</option>
                                        <option value="achieve-workout-goal"
                                            {{ $result->feature_key == 'achieve-workout-goal' ? 'selected' : '' }}>Achieve
                                            your Workout goal</option>
                                        <option value="participate-group-training-session"
                                            {{ $result->feature_key == 'participate-group-training-session' ? 'selected' : '' }}>
                                            Participate in a Group Training
                                            Session</option>
                                    </select>
                                    <span id="feature_key-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6" id="points">
                                <div class="form-group">
                                    <label>Point<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" value="{{ $result->point }}"
                                        placeholder="Point" name="point">
                                    <span id="point-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="descriptionFieldContainer">
                                <div class="form-group">
                                    <label>Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control text-editor" placeholder="Description" name="description">{{ $result->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                        onClick="updateReward()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.rewardManagement.index', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <!-- {!! JsValidator::formRequest('App\Http\Requests\RewardManagementRequest', '#updateForm') !!} -->
    <script>
        var result = @json($result);
        var gameList = @json($games);

        $(document).ready(() => {
            if(result.reward_game != null || ''){
                 $('#points').hide();
                $('#min_points, #max_points, #score, #duration, #game_type').remove();
                if ($('#gameFieldContainer').length) {
                    $('#gameFieldContainer').hide();
                }
                const inputs = `
                <div class="col-md-6" id="game_type">
                    <div class="form-group">
                        <label>Game Type<span class="text-danger">*</span></label>
                        <input type="hidden" name="type" id="typeField">
                        <select class="form-control form-select" onChange="selectGameType(this)" id="gameTypeField" name="game_type">
                            <option value ="">Select Game Type</option>
                            <option ${result.reward_game.game_type == 'random'?'selected':''} value="random">Random</option>
                            <option ${result.reward_game.game_type == 'specific'?'selected':''} value="specific">Specific</option>
                        </select>
                        <span id="game_type-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6" id="min_points">
                    <div class="form-group">
                        <label>Minimum Points<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" value=${result.reward_game.min_points} placeholder="Insert Minimum Points" name="min_points">
                        <span id="min_points-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6" id="max_points">
                    <div class="form-group">
                        <label>Maximum Points<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" value=${result.reward_game.max_points} placeholder="Insert Maximum Points" name="max_points">
                        <span id="max_points-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6" id="score">
                    <div class="form-group">
                        <label>Maximum Score<span class="text-danger">*</span></label>
                        <input value=${result.reward_game.score} type="number" class="form-control" value="" placeholder="Insert Maximum Score" name="score">
                        <span id="score-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6" id="duration">
                    <div class="form-group">
                        <label>Duration (in seconds)<span class="text-danger">*</span></label>
                        <input value=${result.reward_game.duration} type="number" class="form-control" value="" placeholder="Insert Game Duration in seconds" name="duration">
                        <span id="duration-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                `;
                $('#required-inputs').append(inputs);

                if(result.reward_game.game_type == 'specific'){
                const input = `
                    <div class="col-md-6" id="gameFieldContainer">
                        <div class="form-group">
                            <label>Game<span class="text-danger">*</span></label>
                            <input type="hidden" name="game" id="gameField">
                            <select onChange="selectGame(this)" class="form-control form-select" id="gameKeyField" name="game_key">
                                <option value="">Select Game</option>
                            </select>
                            <span id="game_key-error" class="help-block error-help-block text-danger"></span>
                        </div>
                    </div>
                `;
                $('#min_points').before(input);

                if (gameList && gameList.length > 0) {
                    gameList.forEach((game) => {
                        $('#gameKeyField').append(`<option ${result.reward_game.game_key && result.reward_game.game_key  == game.game_key?'selected':''} value="${game.game_key}">${game.title}</option>`);
                    });
                }
            }
            }else{
                $('#points').show();
                if ($('#gameFieldContainer').length) {
                    $('#gameFieldContainer').remove();
                }

                // Remove gamification-specific fields
                $('#min_points, #max_points, #score, #duration, #game_type').remove();
            } 
        });


        tinymce.init({
            theme: "modern",
            //selector: "textarea",
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
            plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern',
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
            height: 200,
        });

        


        /**
         * Update User Management.
         * @request form fields
         * @response object.
         */
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('updateForm').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        });

        function updateReward() {
            var formData = $("#updateForm").serializeArray();
            if ($('#updateForm').valid()) {
                $('#updateBtn').prop('disabled', true);
                $('#updateBtnLoader').show();
                var url = "{{ route('common.updateRewardManagement', ['id' => '%recordId%']) }}";
                url = url.replace('%recordId%', "{{ $result['id'] }}");
                $.ajax({
                    type: "PUT",
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.rewardManagement.index', ['user_type' => $userType]) }}";
                            }, 500)
                        } else {
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            _toast.error('Reward management  not updated.');
                        }

                    },
                });
            }
        };

        function selectFeature() {
            let feature = $('#featureKeyField').find(":selected").text();
            $('#featureField').val(feature);
        }

        function enableGamification(checkbox){
            if(checkbox.checked){
                console.log("Gamification is enabled");
                $('#points').hide();
                $('#min_points, #max_points, #score, #duration, #game_type').remove();
                if ($('#gameFieldContainer').length) {
                    $('#gameFieldContainer').remove();
                }
                const inputs = `
                <div class="col-md-6" id="game_type">
                    <div class="form-group">
                        <label>Game Type<span class="text-danger">*</span></label>
                        <input type="hidden" name="type" id="typeField">
                        <select class="form-control form-select" onChange="selectGameType(this)" id="gameTypeField" name="game_type">
                            <option value ="">Select Game Type</option>
                            <option value="random">Random</option>
                            <option value="specific">Specific</option>
                        </select>
                        <span id="game_type-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6" id="min_points">
                    <div class="form-group">
                        <label>Minimum Points<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" value="" placeholder="Insert Minimum Points" name="min_points">
                        <span id="min_points-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6" id="max_points">
                    <div class="form-group">
                        <label>Maximum Points<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" value="" placeholder="Insert Maximum Points" name="max_points">
                        <span id="max_points-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6" id="score">
                    <div class="form-group">
                        <label>Maximum Score<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" value="" placeholder="Insert Maximum Score" name="score">
                        <span id="score-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6" id="duration">
                    <div class="form-group">
                        <label>Duration (in seconds)<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" value="" placeholder="Insert Game Duration in seconds" name="duration">
                        <span id="duration-error" class="help-block error-help-block text-danger"></span>
                    </div>
                </div>
                `;
                $('#required-inputs').append(inputs);
            }else{
                $('#points').show();
                if ($('#gameFieldContainer').length) {
                    $('#gameFieldContainer').remove();
                }

                // Remove gamification-specific fields
                $('#min_points, #max_points, #score, #duration, #game_type').remove();
            }
        }

        function selectGameType(selectElement) {
            let gameType = $('#gameTypeField').find(":selected").text();
            $('#typeField').val(gameType);

            const value = selectElement.value;
            if (value === 'specific') {
                // Avoid duplicates
                if ($('#gameKeyField').length === 0) {
                    const input = `
                        <div class="col-md-6" id="gameFieldContainer">
                            <div class="form-group">
                                <label>Game<span class="text-danger">*</span></label>
                                <input type="hidden" name="game" id="gameField">
                                <select onChange="selectGame(this)" class="form-control form-select" id="gameKeyField" name="game_key">
                                    <option value="">Select Game</option>
                                </select>
                                <span id="game_key-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                    `;
                    $('#min_points').before(input);

                    if (gameList && gameList.length > 0) {
                        gameList.forEach((game) => {
                            $('#gameKeyField').append(`<option value="${game.game_key}">${game.title}</option>`);
                        });
                    }
                }
            } else {
                // Remove if random is selected again
               $('#gameFieldContainer').remove();
            }
        }

        function selectGame() {
            let game = $('#gameKeyField').find(":selected").text();
            $('#gameField').val(game);
        }
    </script>
@endsection
