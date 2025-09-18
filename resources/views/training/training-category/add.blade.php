@extends('layouts.app')
@section('head')
<title>Trainng Video Category | Add</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php $userType = userType();@endphp

<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.trainingVideoCategories',['user_type'=>$userType])}}">Training Video Category</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create Training Video Category
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.addTrainingVideoCategory')}}">
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
                                <input type="hidden" name="type" value="training-library">
                            </div>
                        </div>
                    </div>

                    <!-- </div>
                    </div> -->
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn" onClick="addCategoryLevel()">Add<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.trainingVideoCategories',['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\TrainingVideoCategoryRequest','#addForm') !!}

<script>
    /**
     * Add Training Category.
     * @request form fields
     * @response object.
     */

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('addForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    function addCategoryLevel() {
        var formData = $("#addForm").serializeArray();
        if ($('#addForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.addTrainingVideoCategory')}}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{route('user.trainingVideoCategories',['user_type'=>$userType])}}";
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
                    } else {
                        _toast.error('Training category level not created.');
                    }
                },
            });
        }
    };

    $("#addForm input").keypress(function(event) {
        // Check if Enter key is pressed
        if (event.which === 13) {
            // Prevent default form submission
            event.preventDefault();
            // Submit form
            addCategoryLevel();
        }
    });
</script>
@endsection