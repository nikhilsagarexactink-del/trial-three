@extends('layouts.app')
@section('head')
<title>Profile Picture</title>
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
                    <li class="breadcrumb-item active">Profile Picture</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Default Profile Picture
            </h2>
            <!-- Page Title End -->
        </div>
        <div class="right-side mt-2 mt-md-0">
            <input type="hidden" id="uploadImageUrl" value="{{route('common.saveImage')}}">
            <input type="hidden" id="mediaFor" value="default-profile-pictures">
            <input type="file" id="UploadImg" onchange="setImage(this)" class="btn btn-secondary ripple-effect-dark text-white" name="profile_picture">
        </div>
    </div>
    <!-- filter section start -->
    <div class="filter_section with-button filter_section_open" id="searchFilter">
        <div class="filterHead d-flex justify-content-between">
            <h3 class="h-24 font-semi">Filter</h3>
            <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i class="iconmoon-close"></i></a>
        </div>
        <div class="flex-row justify-content-between align-items-end">
            <div class="left">
                <h5 class="fs-6 label">Search By</h5>
                <form action="javascript:void(0)" id="searchFilterForm">
                    <input type="hidden" name="sort_by" id="sortByFieldId" value="plans.created_at">
                    <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                    <div class="form_field flex-wrap pr-0">
                        <div class="form-group select-arrow">
                            <select class="selectpicker select-custom form-control " title="Status" data-size="4" name="status" id="statusId">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="btn_clumn mb-3 position-sticky">
                            <button type="submit" class="btn btn-secondary ripple-effect">Search</button>
                            <button type="button" class="btn btn-outline-danger ripple-effect" id="clearSearchFilterId">Reset</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <!-- filter section end -->
    <div class="white-bg">
        <div class="row">
            <div id="listId"></div>
        </div>
        <!--Pagination-->
        <div id="paginationLink"></div>
        <!--Pagination-->
    </div>
</div>
<!-- Main Content Start -->
<!-- Image crop modal -->
@include('layouts.image-cropper-modal')
<!-- Image crop modal -->

@endsection

@section('js')
<script src="{{url('assets/custom/image-cropper.js')}}"></script>

<script>
    /**
     * Load list.
     * @request search, status
     * @response object.
     */
    function loadImageList(url) {
        $("#listId").html('{{ajaxListLoader()}}');
        url = url || "{{route('common.loadProfilePictureList')}}";
        var formData = $('#searchFilterForm').serialize();
        $.ajax({
            type: "GET",
            url: url,
            data: formData,
            success: function(response) {
                if (response.success) {
                    $("#listId").html("");
                    $("#paginationLink").html("");
                    $('#listId').append(response.data.html);
                    $('#paginationLink').append(response.data.pagination);
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }
    /**
     * Save profile image
     * @request form fields
     * @response object.
     */
    function saveImage(data) {
        $('#updateBtn').prop('disabled', true);
        $('#updateBtnLoader').show();
        $.ajax({
            type: "POST",
            url: "{{route('common.saveDefaultProfilePicture')}}",
            data: {
                _token: "{{csrf_token()}}",
                media_id: data.id
            },
            success: function(response) {
                $('#updateBtn').prop('disabled', false);
                $('#updateBtnLoader').hide();
                if (response.success) {
                    _toast.success(response.message);
                    loadImageList();
                    resetFileInput();
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
                    _toast.error('Please try again.');
                }
            },
        });
    };

    /**
     * Delete Image
     * @request id
     * @response object.
     */
    function changeStatus(id, status) {
        var statusType = (status == 'deleted') ? 'delete' : status;
        bootbox.confirm('Are you sure you want to ' + statusType + ' this image ?', function(result) {
            if (result) {
                var url = "{{route('common.defaultProfilePictureChangeStatus', ['id'=>'%recordId%'])}}";
                url = url.replace('%recordId%', id);
                $.ajax({
                    type: "DELETE",
                    url: url,
                    data: {
                        '_token': "{{csrf_token()}}",
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            loadImageList();
                            _toast.success(response.message);
                        } else {
                            _toast.error(response.message);
                        }
                    },
                    error: function(err) {
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                        }

                    }
                });
            }
        })
    }

    // reset file input after close cropper model
    function resetFileInput() {
        $('#UploadImg').val("");
    }
    // reset file input after close cropper model
    function successCallback(data) {
        saveImage(data);
    }
    $(document).ready(function() {
        /**
         * Reload list.
         */
        $('#searchFilterForm').on('submit', function(e) {
            $("#searchFilter").toggleClass("open");
            loadImageList();
        });
        /**
         * Clear search filter.
         */
        $('#clearSearchFilterId').on('click', function(e) {
            $('#searchFilterForm')[0].reset();
            //$('.selectpicker').selectpicker('refresh')
            loadImageList();
        });
        loadImageList();
    });
</script>
@endsection
<style>

</style>