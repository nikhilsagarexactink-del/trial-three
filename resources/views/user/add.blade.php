@extends('layouts.app')
@section('head')
<title>User | Add</title>
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
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.users', ['user_type'=>$userType])}}">Manage Users</a></li>
                    <li class="breadcrumb-item active">Create User</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create User
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addUserForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.addUser')}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="First Name" name="first_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Last Name" name="last_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" placeholder="Email Address" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" placeholder="Password" name="password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group select-arrow">
                                <select class="selectpicker select-custom form-control " name="user_type">
                                    @foreach($result as $role)
                                    @if($role->name!='Admin')
                                    <option value="{{ $role->user_type }}">{{ $role->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- </div>
                    </div> -->
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addrBtn" onClick="addUser()">Add<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.users', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\UserRequest','#addUserForm') !!}

<script>
    /**
     * Add User.
     * @request form fields
     * @response object.
     */
    function addUser() {
        var formData = $("#addUserForm").serializeArray();
        if ($('#addUserForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.addUser')}}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addUserForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{route('user.users', ['user_type'=>$userType])}}";
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
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    }
                },
            });
        }
    };
</script>
@endsection