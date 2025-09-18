@extends('layouts.app')
@section('head')
    <title>Journal | Update</title>
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
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.journal', ['user_type' => $userType]) }}">My
                                Journal</a></li>
                        <li class="breadcrumb-item active">My Journal</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    My Journal
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false"
                action="{{ route('common.updateJournal', ['id' => $id]) }}">
                @csrf

                <div class="right-side mt-2 mt-md-0">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120"
                        onClick="changeStatus('{{ $result->id }}','deleted')" href="javascript:void(0);"
                        class="btn btn-secondary ripple-effect-dark text-white">
                        Delete
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            {{-- <div class="col-md-8 form-group">
                            <label>Title<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Title" name="title">
                            <span id="title-error" class="help-block error-help-block text-danger"></span>
                        </div> --}}
                            <div class="col-md-4 form-group">
                                <label>Date<span class="text-danger">*</span></label>
                                <input type="text" id="datepicker" class="form-control" placeholder="Date" name="date"
                                    value="{{ $result->date }}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea class="form-control text-editor" placeholder="Description" name="description">{{ $result->description }}</textarea>
                                <span id="description-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                        onClick="updateJournal()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.journal', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    {{-- {!! JsValidator::formRequest('App\Http\Requests\JournalRequest','#updateForm') !!} --}}
    <script>
        tinymce.init({
            selector: "textarea.text-editor",
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            height: 500,
            plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern textcolor',
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
            content_style: `
            body {
                font-family: Arial, sans-serif;
                line-height: 1.5;
                background: #fff;
                background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 1px, transparent 1px);
                background-size: 100% 24px;
            }
            p {
                margin: 0;
                padding: 0;
            }
        `
        });
        /**
         * Add Sport.
         * @request form fields
         * @response object.
         */

        function updateJournal() {
            var formData = $("#updateForm").serializeArray();
            if ($('#updateForm').valid()) {
                $('#updateBtn').prop('disabled', true);
                $('#updateBtnLoader').show();
                var url = "{{ route('common.updateJournal', ['id' => '%recordId%']) }}";
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
                            // $('#updateForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.journal', ['user_type' => $userType]) }}";
                            }, 500);
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
                            _toast.error('Journale not created.');
                        }
                    },
                });
            }
        };

        $(function() {
            $("#datepicker").datepicker({
                dateFormat: 'mm-dd-yy'
            });
        });

        /**
         * Change status.
         * @request id
         * @response object.
         */
        function changeStatus(id, status) {
            var statusType = (status == 'deleted') ? 'delete' : status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this journal ?', function(result) {
                if (result) {
                    var url =
                        "{{ route('common.changeJournalStatus', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                setTimeout(function() {
                                    window.location.href =
                                        "{{ route('user.journal', ['user_type' => $userType]) }}";
                                }, 500);
                                _toast.success(response.message);
                            } else {
                                _toast.error(response.message);
                            }
                        },
                        error: function(err) {
                            var errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                            if (err.status === 422) {
                                var errors = $.parseJSON(err.responseText);
                                _toast.error(errors.message);
                            }
                        }
                    });
                }
            })
        }
    </script>
@endsection
