@extends('layouts.app')
@section('head')
    <title>User Rewards | Update</title>
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
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.userRewards.index', ['user_type' => $userType]) }}"> User Reward</a>
                        </li>
                        <li class="breadcrumb-item active">Update Points</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Update Points
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.updateUserReward', ['id' => $id] ) }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <!-- <div class="card card-primary">
                                <div class="card-body"> -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Point<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" value="{{ $result->point }}"
                                        placeholder="Point" name="point">
                                </div>
                            </div>
                        </div>

                        <!-- </div>
                            </div> -->
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                        onClick="updateReward()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.userRewards.index', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\UserRewardRequest', '#updateForm') !!}

    <script>
        /**
         * Update User Point.
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
                var url = "{{ route('common.updateUserReward', ['id' => '%recordId%']) }}";
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
                                    "{{ route('user.userRewards.index', ['user_type' => $userType]) }}";
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
                            _toast.error('Reward points not updated.');
                        }

                    },
                });
            }
        };
    </script>
@endsection
