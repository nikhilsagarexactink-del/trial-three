@extends('layouts.app')
@section('head')
    <title>Workout | Add</title>
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
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.indexWorkoutExercise', ['user_type' => $userType]) }}">Manage
                                Exercise</a></li>
                        <li class="breadcrumb-item active">Create Exercise</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Create Exercise
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.saveWorkoutExercise') }}">
                @csrf
                <!-- Exercise Form Start -->
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="uploadImageUrl" value="{{ route('common.saveImage') }}">
                        <input type="hidden" id="mediaFor" value="exercise">
                        <input type="hidden" name="type" id="mediaFor" value="exercise" hidden>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Exercise Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Exercise Name">
                                    <span id="name-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Video URL<span class="text-danger">*</span></label>
                                    <input type="text" id="videoUrlAutocomplte" class="form-control"
                                        placeholder="Type Video Title..." name="video_url">
                                    <span id="autocompleteLoader" class="spinner-border spinner-border-sm"
                                        style="display: none;"></span>
                                    <!-- <input type="text" class="form-control" placeholder="Video URL" name="video_url"> -->
                                    <span id="video_url-error" class="help-block error-help-block text-danger"></span>
                                    <div id="video_url_suggestions" class="dropdown-menu" style="display: none;"></div>
                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exercise">No. of reps</label>
                                    <select class="form-control" id="no_of_reps" name="no_of_reps">
                                        <option value="">Select No. of reps</option>
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="difficulty_id">Select Difficulty</label>
                                    <select id="difficulty_id" name="difficulty_id[]"
                                        class="js-states form-control selectpicker" multiple>
                                        <option value="">Select Difficulty</option>
                                        @foreach ($difficulties as $difficult)
                                            @if ($difficult->status == 'active')
                                                <option value="{{ $difficult->id }}">{{ $difficult->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="category_id">Select Category<span class="text-danger">*</span></label>
                                    <select class="js-states form-control selectpicker" id="category_id"
                                        name="category_id[]" multiple>
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
                                <div class="form-group multi-select">
                                    <label for="sport_id">Sport Selection</label>
                                    <select class="js-states form-control selectpicker" id="sport_id" name="sport_id[]"
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
                                    <label for="equipment_id">Select Equipment</label>
                                    <select class="js-states form-control selectpicker" id="equipment_id"
                                        name="equipment_id[]" multiple>
                                        <option value="">Select Equipment</option>
                                        @foreach ($equipments as $equipment)
                                            @if ($equipment->status == 'active')
                                                <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if ($userType == 'admin' || $userType == 'coach')
                                <div class="col-md-6">
                                    <div class="form-group multi-select">
                                        <label for="age_range_id">Age Group</label>
                                        <select class="js-states form-control selectpicker" id="age_range_id"
                                            name="age_range_id[]" multiple>
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
                                    <label for="duration">Duration<span class="text-danger">*</span></label>
                                    <select class="form-control form-select" id="durationFieldId" name="duration">
                                        <option value="">Select Duration</option>
                                        <option value="1">1M</option>
                                        <option value="5">5M</option>
                                        <option value="15">15M</option>
                                        <option value="20">20M</option>
                                        <option value="25">25M</option>
                                        <option value="45">45M</option>
                                        <option value="60">60M</option>
                                        <option value="75">75M</option>
                                        <option value="90">90M</option>
                                        <option value="120">120M</option>
                                        <option value="150">150M</option>
                                    </select>
                                </div>
                                <span id="duration-error" class="help-block error-help-block text-danger"></span>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exerciseDescription" class="form-label">Description</label>
                                    <textarea class="form-control text-editor" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="image-upload">
                                    <div class="form-group mb-3">
                                        <label class="d-block">Exercise Image</label>
                                        <input type="file" class="file-upload-input" onchange="setImage(this)"
                                            name="uploadImages" id="UploadImg">
                                        <a href="javascript:void(0)" class="btn btn-secondary">
                                            <img src="{{ url('assets/images/file-upload.svg') }}"> Exercise Image
                                        </a>
                                    </div>
                                    <div class="uploaded-image-list">
                                        <img style="height:50px;width:50px;" id="imagePreview"
                                            src="{{ url('assets/images/default-image.png') }}">
                                    </div>
                                    <input type="hidden" id="hiddenMediaFileId" name="media_id">
                                </div>
                            </div>

                            <div class="btn_row text-center">
                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120"
                                    id="addBtn" onClick="addExercise()">
                                    Add
                                    <span id="addBtnLoader" class="spinner-border spinner-border-sm"
                                        style="display: none;"></span>
                                </button>
                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                                    href="{{ route('user.indexWorkoutExercise', ['user_type' => $userType]) }}">Cancel</a>
                            </div>
                        </div>
                    </div>
            </form>
        </section>
    </div>
    <!-- Main Content End -->
    @include('layouts.image-cropper-modal')
@endsection

@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\WorkoutExerciseRequest', '#addForm') !!}
    <script src="{{ url('assets/custom/image-cropper.js') }}"></script>
    <script>
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

            $('#equipment_id').select2({
                placeholder: "Select Equipment",
                allowClear: true
            });
        });

        function addExercise() {
            var formData = $("#addForm").serializeArray();
            //if ($('#addForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.saveWorkoutExercise') }}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addForm')[0].reset();
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('user.indexWorkoutExercise', ['user_type' => $userType]) }}";
                        }, 500);
                    } else {
                        _toast.error('Something went wrong. Please try again');
                    }
                },
                error: function(err) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Exercise not created.');
                    }
                },
            });
            // } else {
            //     console.log("Form is not valid"); // Debugging: Print form validation status
            // }
        }

        // Initialize autocomplete once on page load
        $(function() {
            $("#videoUrlAutocomplte").autocomplete({
                minLength: 2,
                delay: 500,
                source: function(request, responseCallback) {
                    $("#autocompleteLoader").show();
                    var url = "{{ route('common.findAllVimeoVideos') }}";
                    $.ajax({
                        type: "GET",
                        url: url,
                        data: {
                            search: request.term
                        },
                        success: function(response) {
                            $("#autocompleteLoader").hide();
                            if (response.success) {
                                var videos = response.data.original || [];
                                var items = videos.map((obj) => ({
                                    label: obj.name,
                                    value: obj.link,
                                    thumbnail: obj.pictures.base_link,
                                    title: obj.name,
                                    link: obj.link
                                }));

                                if (items.length === 0) {
                                    // Show "No videos found" message
                                    responseCallback([{
                                        label: "No videos found",
                                        value: "",
                                        disabled: true
                                    }]);
                                } else {
                                    responseCallback(items);
                                }
                            } else {
                                responseCallback([]);
                            }
                        },
                        error: function() {
                            $("#autocompleteLoader").hide();
                            responseCallback([]);
                            _toast.error('Failed to fetch videos.');
                        }
                    });
                },
                select: function(event, ui) {
                    if (!ui.item.disabled) {
                        $(this).val(ui.item.value);
                    }
                    return false;
                },
                focus: function(event, ui) {
                    return !ui.item.disabled;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                if (item.disabled) {
                    return $("<li>")
                        .append(
                            `<div class="text-muted p-2">
                                ${item.label}
                            </div>`
                        )
                        .appendTo(ul);
                }

                return $("<li>")
                    .append(
                        `<div style="display: flex; align-items: center; padding: 5px;">
                            <img src="${item.thumbnail}" alt="Thumbnail" style="width: 50px; height: 50px; margin-right: 10px; border-radius: 5px;">
                            <div>
                                <strong>${item.title}</strong><br>
                                <small>${item.link}</small>
                            </div>
                        </div>`
                    )
                    .appendTo(ul);
            };
        });

        // Optional: Prevent form submission on Enter key in the autocomplete field
        $("#videoUrlAutocomplte").on("keydown", function(e) {
            if (e.keyCode === 13 && $(".ui-autocomplete").is(":visible")) {
                e.preventDefault(); // Block form submit when menu is open
            }
        });
    </script>
@endsection
<style>
    #video_url_suggestions {
        max-height: 250px;
        overflow: auto;
    }
</style>
