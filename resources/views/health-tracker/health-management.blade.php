@extends('layouts.app')
@section('head')
    <title>Health Management</title>
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
                        <li class="breadcrumb-item active">Health Management</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Health Management
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.healthManagement.save') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="text-align:center;">
                                    <label style="font-size: larger;">Male</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="text-align:center;">
                                    <label style="font-size: larger;">Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Shoulders</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Shoulders" name="male_shoulders">{{ !empty($data['male_shoulders']) ? $data['male_shoulders'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Shoulders</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female Shoulders" name="female_shoulders">{{ !empty($data['female_shoulders']) ? $data['female_shoulders'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Left Bicep</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Left Bicep" name="male_left_bicep">{{ !empty($data['male_left_bicep']) ? $data['male_left_bicep'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Left Bicep</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female Left Bicep" name="female_left_bicep">{{ !empty($data['female_left_bicep']) ? $data['female_left_bicep'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Right Bicep</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Right Bicep" name="male_right_bicep">{{ !empty($data['male_right_bicep']) ? $data['male_right_bicep'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Right Bicep</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female Right Bicep" name="female_right_bicep">{{ !empty($data['female_right_bicep']) ? $data['female_right_bicep'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chest</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Chest" name="male_chest">{{ !empty($data['male_chest']) ? $data['male_chest'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chest</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female Chest" name="female_chest">{{ !empty($data['female_chest']) ? $data['female_chest'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label> Waist</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Waist" name="male_waist">{{ !empty($data['male_waist']) ? $data['male_waist'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label> Waist</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female Waist" name="female_waist">{{ !empty($data['female_waist']) ? $data['female_waist'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Left Thigh</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Left Thigh" name="male_left_thigh">{{ !empty($data['male_left_thigh']) ? $data['male_left_thigh'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Left Thigh</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female Left Thigh" name="female_left_thigh">{{ !empty($data['female_left_thigh']) ? $data['female_left_thigh'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Right Thigh</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Right Thigh" name="male_right_thigh">{{ !empty($data['male_right_thigh']) ? $data['male_right_thigh'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Right Thigh</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female Right Thigh" name="female_right_thigh">{{ !empty($data['female_right_thigh']) ? $data['female_right_thigh'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Left Calf</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Left Calf" name="male_left_calf">{{ !empty($data['male_left_calf']) ? $data['male_left_calf'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Left Calf</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female Left Calf" name="female_left_calf">{{ !empty($data['female_left_calf']) ? $data['female_left_calf'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Right Calf</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Male Right Calf" name="male_right_calf">{{ !empty($data['male_right_calf']) ? $data['male_right_calf'] : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Right Calf</label>
                                    <textarea type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }} class="form-control"
                                        placeholder="Female LefRightt Calf" name="female_right_calf">{{ !empty($data['female_right_calf']) ? $data['female_right_calf'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="btn_row text-center">
                    @if ($userType == 'admin')
                        <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn"
                            onClick="saveHealthMeasurementValues()">Submit<span id="addBtnLoader"
                                class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    @endif
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Cancel</a>
                </div>

            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <script>
        /**
         * Add Health measurement values.
         * @request form fields
         * @response object.
         */
        function saveHealthMeasurementValues() {
            var formData = $("#addForm").serializeArray();
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.healthManagement.save') }}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 500)
                    } else {
                        _toast.error('Somthing went wrong. please try again');
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
                        _toast.error(errors.message);
                    } else {
                        _toast.error('Please try again.');
                    }
                },
            });
        };
    </script>
@endsection
