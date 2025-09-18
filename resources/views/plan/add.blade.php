@extends('layouts.app')

@section('head')
<title>Plan | Add</title>
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
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.plans', ['user_type'=>$userType])}}">Manage Plans</a></li>
                    <li class="breadcrumb-item active">Create Plan</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create Plan
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addPlanForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.addPlan')}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <!-- <div class="card card-primary">
                        <div class="card-body"> -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Name" name="name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plan Key<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Plan Key" name="key">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cost per month <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Cost per month" name="cost_per_month">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cost per year <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Cost per year" name="cost_per_year">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Free trial days <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Free trial days" name="free_trial_days">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Visibility <span class="text-danger">*</span></label>
                                <select class="selectpicker form-control" title="Select Visibility" data-size="4" name="visibility">
                                    <option value="active">Active</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group custom-form-check-head">
                                <div class="custom-form-check">
                                    <label class="form-check">
                                        <span class="fs-bold fs-small">Free Plan</span>
                                        <input class="ms-1 align-middle" type="checkbox" name="is_free_plan" value="1">
                                        <div class="checkbox__checkmark"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group custom-form-check-head">
                                <div class="custom-form-check">
                                    <label class="form-check">
                                        <span class="fs-bold fs-small">Default Free Plan</span>
                                        <input class="ms-1 align-middle" type="checkbox" name="is_default_free_plan" value="1">
                                        <div class="checkbox__checkmark"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" placeholder="Description" name="description"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- </div>
                    </div> -->
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addPlanBtn" onClick="addPlan()">Add<span id="addPlanBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.plans', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\PlanRequest','#addPlanForm') !!}

<script>
    /**
     * Add Plan.
     * @request form fields
     * @response object.
     */
    function addPlan() {
        var formData = $("#addPlanForm").serializeArray();
        if ($('#addPlanForm').valid()) {
            $('#addPlanBtn').prop('disabled', true);
            $('#addPlanBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.addPlan')}}",
                data: formData,
                success: function(response) {
                    $('#addPlanBtn').prop('disabled', false);
                    $('#addPlanBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addPlanForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{route('user.plans', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addPlanBtn').prop('disabled', false);
                    $('#addPlanBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Plan not created.');
                    }
                },
            });
        }
    };
</script>
@endsection