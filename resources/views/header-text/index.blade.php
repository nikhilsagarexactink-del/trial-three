@extends('layouts.app')
@section('head')
    <title>Settings | Headers Text</title>
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
                        <li class="breadcrumb-item active">Headers Text</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Headers Text
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.saveHeaderText') }}">
                @csrf
                <div class="row">
                    <!-- Looping through each section to create WYSIWYG editors dynamically -->
                    @php
                        $sections = [
                            'getting-started' => 'Getting Started',
                            'motivation' => 'Motivation',
                            'fitness-profile' => 'Fitness Profile',
                            'health-tracker' => 'Health Tracker',
                            'water-tracker' => 'Water Tracker',
                            'training-library' => 'Training Library',
                            'speed' => 'Speed',
                            'journal' => 'Journal',
                            'step-counter' => 'Step Counter',
                            'recipes' => 'Recipes',
                            'activity-tracker' => 'Activity Tracker',
                            'my-rewards' => 'My Rewards',
                            'how-to-earn-rewards' => 'How To Earn Rewards',
                            'sleep-tracker' =>' Sleep Tracker',
                            'dashboard-widget'=>'Dashboard Widget'

                        ];
                    @endphp

                    @foreach ($sections as $key => $label)
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ $label }}</label>
                                        <textarea class="form-control text-editor" placeholder="{{ $label }}" name="{{ $key }}">{{ !empty($headers) ? $headers[$key] : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="btn_row text-center">
                        @if ($userType == 'admin')
                            <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                                onClick="updateHeaders()">Update
                                <span id="updateBtnLoader" class="spinner-border spinner-border-sm"
                                    style="display: none;"></span>
                            </button>
                        @endif
                        <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                            href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Cancel</a>
                    </div>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content End -->
@endsection

@section('js')
    <script>
        let tinyMceOptions = {
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
            plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern image media',
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link image media | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat | code',
            height: 200,
            file_browser_callback_types: 'image',
            file_picker_callback: (cb, value, meta) => {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    let filename = file.name;
                    let extension = filename.replace(/^.*\./, '');
                    extension = extension.toLowerCase();
                    if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension ==
                        'svg') {
                        $('.mceu_175-action').prop('disabled', true);
                        let formData = new FormData();
                        formData.append('file', file);
                        formData.append('mediaFor', 'header-text');
                        formData.append('_token', "{{ csrf_token() }}");
                        $.ajax({
                            type: "POST",
                            url: "{{ route('common.saveMultipartMedia') }}",
                            dataType: 'json',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('.mceu_175-action').prop('disabled', false);
                                if (response.success) {
                                    cb(response.data.fileInfo.base_url, {
                                        title: response.data.fileInfo.name
                                    });
                                    _toast.success(response.message);
                                } else {
                                    _toast.error('Somthing went wrong. please try again');
                                }
                            },
                            error: function(err) {
                                $('.mceu_175-action').prop('disabled', false);
                                let errors = $.parseJSON(err.responseText);
                                _toast.error(errors.message);
                            },
                        });
                    } else {
                        _toast.error('Only jpeg,png,jpg,svg file allowed.');
                    }
                });
                input.click();
            }
        };
        if ("{{ $userType }}" != 'admin') {
            tinyMceOptions.readonly = 1;
        }
        tinymce.init(tinyMceOptions);

        function updateHeaders() {
            var formData = $("#updateForm").serializeArray();
            if ($('#updateForm').valid()) {
                $('#updateBtn').prop('disabled', true);
                $('#updateBtnLoader').show();
                $.ajax({
                    type: "PUT",
                    url: "{{ route('common.saveHeaderText') }}",
                    data: formData,
                    success: function(response) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 500);
                        } else {
                            _toast.error('Something went wrong. Please try again');
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
                            _toast.error('Please try again.');
                        }
                    },
                });
            }
        }
    </script>
@endsection
