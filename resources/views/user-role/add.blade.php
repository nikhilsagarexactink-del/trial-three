@extends('layouts.app')

@section('head')
<title>User Role Add</title>
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
                    <li class="breadcrumb-item"><a href="{{route($userType.'.userRoles')}}">Manage Roles</a></li>
                    <li class="breadcrumb-item active">Create Roles</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create Roles
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addRoleForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.userRoles')}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Name" name="name">
                            </div>
                        </div>
                    </div>
                </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addRoleBtn" onClick="addRole()">Add<span id="addRoleBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route($userType.'.userRoles')}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{{-- {!! JsValidator::formRequest('App\Http\Requests\UserRoleRequest','#addRoleForm') !!} --}}

<script>
    /**
     * Add User Role.
     * @request form fields
     * @response object.
     */
    function addRole() {
        var formData = $("#addRoleForm").serializeArray();
        if ($('#addRoleForm').valid()) {
            $('#addRoleBtn').prop('disabled', true);
            $('#addRoleBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.addRole')}}",
                data: formData,
                success: function(response) {
                    $('#addRoleBtn').prop('disabled', false);
                    $('#addRoleBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        console.log("success");
                        $('#addRoleForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{route($userType.'.userRoles')}}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addRoleBtn').prop('disabled', false);
                    $('#addRoleBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Role not created.');
                        // console.log("errors",response);
                    }
                },
            });
        }
    };
</script>
@endsection