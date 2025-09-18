@extends('layouts.app')
@section('head')
<title>Permission Tool Tip | Update</title>
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
                            href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.permissionToolTip', ['user_type'=>$userType])}}">
                            Permissions Module Tool Tips Text</a></li>
                    <li class="breadcrumb-item active">Update Tool Tips</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Tool Tips
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updatePermissionToolTipForm" class="form-head" method="PUT" novalidate autocomplete="false"
            action="{{route('common.updatePermissionToolTip',['id'=>$id])}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Module Name<span class="text-danger">*</span></label>
                        <select class="form-control" id="moduleId" name="module_id">
                            <option value="">Select Module</option>
                            @foreach ($modules as $module)
                            @php
                            $selected = false;

                            if ($result->is_parent_module == 1 && $module['is_parent_module'] == 1 &&
                            $result->module_id == $module['id']) {
                            $selected = true;
                            }

                            if ($result->is_parent_module == 0 && $module['is_parent_module'] == 0 &&
                            $result->module_id == $module['id']) {
                            $selected = true;
                            }
                            @endphp
                            @if (!array_key_exists('type', $module) || $module['type'] === 'parent' || $module['type'] === 'child')
                            <option value="{{ $module['id'] }}"
                                data-is-parent-module="{{ $module['is_parent_module'] }}"
                                {{ $selected ? 'selected' : '' }}
                                class="{{ $module['is_parent_module'] == 1 ? 'fw-bold' : '' }}">
                                {{ ucfirst($module['name']) }}
                                {{ $module['is_parent_module'] == 1 ?  "(Parent)" : "" }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                        <span id="module_id-error" class="help-block error-help-block text-danger"></span>
                        <input type="hidden" name="is_parent_module" id="isParentModule"
                            value="{{ $result->is_parent_module }}">
                        <input type="hidden" value="{{$result->id}}" name="id">
                    </div>

                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Tool Tip Text <span class="text-danger">*</span></label>
                        <textarea class="form-control" placeholder="Tool Tip Text" name="tool_tip_text"
                            rows="6" cols="30">{{$result->tool_tip_text}}</textarea>
                    </div>
                </div>
            </div>



            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                    onClick="updatePermissionToolTip()">Update<span id="updateBtnLoader"
                        class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                    href="{{route('user.permissionToolTip', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\PermissionToolTipRequest','#updatePermissionToolTipForm') !!}

<script>
$(document).ready(function() {
    $('#moduleId').on('change', function() {
        let isParent = $('option:selected', this).data('is-parent-module');
        $('#isParentModule').val(isParent);
    });
});

tinymce.init({
    theme: "modern",
    mode: "specific_textareas",
    editor_selector: "text-editor",
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    },
    relative_urls: false,
    remove_script_host: true,
    convert_urls: false,

    // 👉 Important
    forced_root_block: 'p', // or false if you want no <p> wrapping
    force_br_newlines: false,
    force_p_newlines: true,
    cleanup: true,
    valid_elements: "p,br,strong,em,u,ul,ol,li,a[href|target],span", // allow all elements (or customize)
    extended_valid_elements: "",
    invalid_styles: {
        '*': 'margin,padding,text-align,font-family,font-size,line-height'
    },

    plugins: 'preview searchreplace autolink directionality charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern',
    toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat',
    height: 200,
});


/**
 * Update Record.
 * @request form fields
 * @response object.
 */
function updatePermissionToolTip() {
    var formData = $("#updatePermissionToolTipForm").serializeArray();
    if ($('#updatePermissionToolTipForm').valid()) {
        $('#updateBtn').prop('disabled', true);
        $('#updateBtnLoader').show();
        var url = "{{route('common.updatePermissionToolTip', ['id'=>'%recordId%'])}}";
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
                        window.location.href =
                            "{{route('user.permissionToolTip', ['user_type'=>$userType])}}";
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
                }
                if (err.status === 400 && err.responseJSON && err.responseJSON.message) {
                    _toast.error(err.responseJSON.message); // or show in your UI
                } else {
                    _toast.error('Permission tool tip not updated.');
                }

            },
        });
    }
};
</script>
@endsection