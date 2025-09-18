@extends('layouts.app')
@section('head')
<title>Users | Update</title>
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
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.users', ['user_type' => $userType])}}">Users</a></li>
                    <li class="breadcrumb-item active">Update</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Users
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false" action="{{route('common.updateUser',['id'=>$id])}}">
            @csrf
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{$result->first_name}}" placeholder="First Name" name="first_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{$result->last_name}}" placeholder="Last Name" name="last_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" disabled readonly="true" placeholder="Enter Email" value="{{$result->email}}" name="email">
                            </div>
                        </div>
                        @if (count($groups) > 0)
                            <div class="col-md-6">
                                <div class="form-group multi-select">
                                    <label for="group_id">Select Group</label>
                                    <select class="js-states form-control selectpicker" id="group_id" name="group_id[]" multiple>
                                        <option value="">Select Group</option>
                                        @foreach ($groups as $group)
                                            @php
                                                $isSelected = isset($result) && $result->groupUsers && $result->groupUsers->contains('group_id', $group->id);
                                            @endphp
                                            <option value="{{ $group->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role<span class="text-danger">*</span></label>
                                <div class="select-arrow">
                                    <select class="selectpicker select-custom form-control" readOnly="true" disabled name="user_type">
                                        @foreach($data as $role)
                                        @if($role->name!='Admin')
                                        <option value="{{ $role->user_type }}" {{ $result->user_type == $role->user_type ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- </div>
                    </div> -->
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn" onClick="updateUser()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.users', ['user_type' => $userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\UserRequest','#updateForm') !!}

<script>

document.addEventListener('DOMContentLoaded', function() {
            $('#group_id').select2({
                placeholder: "Select Group",
                allowClear: true
            });
        });


    /**
     * Update Record.
     * @request form fields
     * @response object.
     */
    function updateUser() {
        var formData = $("#updateForm").serializeArray();
        if ($('#updateForm').valid()) {
            $('#updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{route('common.updateUser', ['id'=>'%recordId%'])}}";
            url = url.replace('%recordId%', "{{$result['id']}}");
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
                            window.location.href = "{{route('user.users', ['user_type' => $userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
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
                        _toast.error('Category not updated.');
                    }

                },
            });
        }
    };
</script>
@endsection