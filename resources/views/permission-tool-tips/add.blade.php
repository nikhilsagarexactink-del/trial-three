@extends('layouts.app')

@section('head')
<title>Permissions Tool Tip | Add</title>
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
                            href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.permissionToolTip', ['user_type'=>$userType])}}">
                            Permissions Module Tool Tips Text</a></li>
                    <li class="breadcrumb-item active">Create Tool Tip</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create Tool Tip
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addPermissionToolTipForm" class="form-head" method="POST" novalidate autocomplete="false"
            action="{{route('common.addPermissionToolTip')}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Module Name<span class="text-danger">*</span></label>
                        <select type="text" class="form-control" id="moduleId" name="module_id">
                            <option value="">Select Module</option>
                            @if(!empty($modules))
                                @foreach ($modules as $module)
                                    @if (!array_key_exists('type', $module) || $module['type'] === 'parent' || $module['type'] === 'child')
                                        <option value="{{ $module['id'] }}"
                                            data-is-parent-module="{{ $module['is_parent_module'] == 1 ? 1 : 0 }}"
                                            class="{{ $module['is_parent_module'] == 1 ? 'fw-bold' : '' }}">
                                            {{ ucfirst($module['name']) }}
                                            {{ $module['is_parent_module'] == 1 ?  "(Parent)" : "" }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" name="is_parent_module" id="isParentModule" value="">
                        <span id="module_id-error" class="help-block error-help-block text-danger"></span>
                    </div>

                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Tool Tip Text <span class="text-danger">*</span></label>
                        <textarea class="form-control" placeholder="Tool Tip Text" name="tool_tip_text"
                            rows="6" cols="30"></textarea>
                    </div>
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addToolTipBtn"
                    onClick="addPermissionToolTip()">Add<span id="addToolTipBtnLoader"
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
{!! JsValidator::formRequest('App\Http\Requests\PermissionToolTipRequest','#addPermissionToolTipForm') !!}

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
 * Add Plan.
 * @request form fields
 * @response object.
 */
function addPermissionToolTip() {
    var formData = $("#addPermissionToolTipForm").serializeArray();
    if ($('#addPermissionToolTipForm').valid()) {
        $('#addToolTipBtn').prop('disabled', true);
        $('#addToolTipBtnLoader').show();
        $.ajax({
            type: "POST",
            url: "{{route('common.addPermissionToolTip')}}",
            data: formData,
            success: function(response) {
                $('#addToolTipBtn').prop('disabled', false);
                $('#addToolTipBtnLoader').hide();
                if (response.success) {
                    _toast.success(response.message);
                    $('#addPermissionToolTipForm')[0].reset();
                    setTimeout(function() {
                        window.location.href =
                            "{{route('user.permissionToolTip', ['user_type'=>$userType])}}";
                    }, 500)
                } else {
                    _toast.error('Somthing went wrong. please try again');
                }
            },
            error: function(err) {
                $('#addToolTipBtn').prop('disabled', false);
                $('#addToolTipBtnLoader').hide();
                if (err.status === 422) {
                    var errors = $.parseJSON(err.responseText);
                    $.each(errors.errors, function(key, val) {
                        $("#" + key + "-error").text(val);
                    });
                }
                if (err.status === 400 && err.responseJSON && err.responseJSON.message) {
                    _toast.error(err.responseJSON.message); // or show in your UI
                } else {
                    _toast.error('Permission tool tip not created.');
                }
            },
        });
    }
};
</script>
@endsection