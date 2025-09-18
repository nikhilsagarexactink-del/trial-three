@extends('layouts.app')

@section('head')
<title>Group | Add</title>
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
                    <li class="breadcrumb-item"><a href="{{route('user.groups', ['user_type'=>$userType])}}">Groups</a></li>
                    <li class="breadcrumb-item active">Create Group</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create Group
            </h2>
            <!-- Page Title End -->
        </div>
    </div>
    <section class="content white-bg">
        <form id="addGroupForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.saveGroup')}}">
            @csrf
            <div class="row">
              <div class="col-md-6">
                    <div class="form-group">
                        <label>Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Name" name="name">
                    </div>
             </div>
             <div class="col-md-6 ">
                <div class="form-group multi-select">
                    <label for="athlete_user_ids">Select Athlete <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="athlete_user_ids" name="athlete_user_ids[]" class="js-states form-control selectpicker" multiple>
                            @if (!empty($athletes) && $athletes->count())
                                @foreach ($athletes as $athlete)
                                    <option value="{{ $athlete->id }}">
                                        {{ ucfirst($athlete->first_name) . ' ' . ucfirst($athlete->last_name) }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled>No athlete found</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                        <div class="image-upload">
                            <label>Group Logo</label>
                            <div class="d-flex align-items-center">
                                    <div class="form-group">
                                        <input type="hidden" id="groupLogoImgId" name="group_image" value="">
                                        <input type="file" id="groupLogoFieldId"
                                            onchange="uploadImage('groupLogoFieldId')"
                                            class="btn btn-secondary ripple-effect-dark text-white upload-image upload-image-field"
                                            name="file">
                                        <a href="javascript:void(0)" class="btn btn-secondary"><img class=""
                                                src="{{ url('assets/images/file-upload.svg') }}">File upload </a>
                                    </div>
                                <img class="group-logo" src="" id="groupLogoImg" alt="Group Logo"style="{{ empty($result->media['base_url']) ? 'display: none;' : '' }}">
                            </div>
                        </div>
                    </div>
           </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addGroupBtn" onClick="addGroup()">Add<span id="addGroupBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.groups', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\GroupRequest','#addGroupForm') !!}

<script>

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('addGroupForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
        $('#athlete_user_ids').select2({
            placeholder: "Select Athlete",
            allowClear: true
        });
    });

    $('#groupLogoImg').hide();

    /**
     * Add Group.
     * @request form fields
     * @response object.
     */
    function addGroup() {
        var formData = $("#addGroupForm").serializeArray();
        if ($('#addGroupForm').valid()) {
            $('#addGroupBtn').prop('disabled', true);
            $('#addGroupBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.saveGroup')}}",
                data: formData,
                success: function(response) {
                    $('#addGroupBtn').prop('disabled', false);
                    $('#addGroupBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addGroupForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{route('user.groups', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addGroupBtn').prop('disabled', false);
                    $('#addGroupBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Group not created.');
                    }
                },
            });
        }
    };


    function uploadImage(fieldId) {
            var filename = $("#" + fieldId).val();
            var extension = filename.replace(/^.*\./, '');
            extension = extension.toLowerCase();
            if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension == 'svg' || extension ==
                'mpeg') {
                var fileObj = document.getElementById(fieldId).files[0];
                $('#' + fieldId).prop('disabled', true);
                $('#updateBtn').prop('disabled', true);
                var formData = new FormData();
                formData.append('file', fileObj);
                formData.append('mediaFor', 'group-logo');
                formData.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveMultipartMedia') }}",
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#' + fieldId).prop('disabled', false);
                        $('#updateBtn').prop('disabled', false);
                        if (response.success) {
                            $('#' + fieldId).val('');
                            $('#groupLogoImgId').val(response.data.id);
                            $('#groupLogoImg').attr("src", response.data.fileInfo.base_url).show();
                            _toast.success(response.message);
                        } else {
                            console.log('my data is', response);
                            $('#' + fieldId).val('');
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#' + fieldId).val('');
                        $('#' + fieldId).prop('disabled', false);
                        $('#updateBtn').prop('disabled', false);
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    },
                });
            } else {
                $('#' + fieldId).val('');
                _toast.error('Only jpeg,png,jpg,svg file allowed.');
            }
        };
</script>
@endsection