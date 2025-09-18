@extends('layouts.app')
<title>Activity Tracker</title>
@section('content')
@include('layouts.sidebar')
@php $userType = userType(); @endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <h2 class="page-title text-capitalize mb-3">
                Activity Tracker Permissions
            </h2>
        </div>
        <div class="right-side mt-2 mt-md-0">
            <a href="javascript:void(0);" onClick="showPermissionModal()" class="btn btn-secondary ripple-effect-dark text-white">
                Permission
            </a>
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
                    <input type="hidden" name="sort_by" id="sortByFieldId" value="created_at">
                    <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                    <div class="form_field flex-wrap pr-0">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search" name="search" id="searchFiledId">
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

    <div class="common-table white-bg">
        <div class="mCustomScrollbar" data-mcs-axis='x'>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>                        
                        <th><span class="sorting"> User Name</span></th>
                        <th><span class="sorting"> Email</span></th>
                        <th><span class="sorting"> Action</span></th>
                    </tr>
                </thead>
                <tbody id="listId"></tbody>
            </table>
        </div>
    </div>
    <!--Pagination-->
    <div id="paginationLink"></div>
    <!--Pagination-->
    <!--User activity tracker permission modal-->
    <div class="modal fade" id="userPermissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Permission</h5>
                <button type="button" onClick="hidePermissionModal()" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userPermissionForm" class="form-head" method="POST" novalidate autocomplete="false">
                    @csrf
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Select Athlete:</label>
                        <select type="text" class="js-example-basic-multiple form-control form-select" id="userId" name="user_id">
                            <option value="">Select Athlete</option>
                            @foreach($athletes as $athlete)
                            <option value="{{$athlete->id}}">{{ucfirst($athlete->first_name.' '.$athlete->last_name)}}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" onClick="hidePermissionModal()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addrBtn" onClick="savePermission()">Save<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
            </div>
            </div>
        </div>
    </div>
    <!--User activity tracker permission modal-->
</div>
<!-- Main Content End -->
@endsection
@section('js')
<script>
    function loadUserPermissionList(url = '') {
       $("#listId").html('{{ajaxTableListLoader()}}');
        url = url || "{{route('common.activityTracker.permissionList')}}";
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
     * Save Permission.
     * @request form fields
     * @response object.
     */
    function savePermission() {
        var formData = $("#userPermissionForm").serializeArray();
        //console.log($('#userId').val());return;
        if ($('#userId').val()) {
            $('#addrBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.activityTracker.saveUserPermission')}}",
                data: formData,
                success: function(response) {
                    $('#addrBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        hidePermissionModal();
                        loadUserPermissionList();
                    } else {
                        _toast.error('Something went wrong. Please try again.');
                    }
                },
                error: function(err) {
                    $('#addrBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    var errors = $.parseJSON(err.responseText);
                    if (err.status === 422) {                       
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                        _toast.error(errors.message);
                    } else {
                        _toast.error(errors.message ? errors.message : 'User not created.');
                    }
                },
            });
        } else {
            _toast.error('Please select the user.');
        }
    }

     /**
     * Delete user permission
     * @request id
     * @response object.
     */
    function deleteUserPermission(id) {
        bootbox.confirm('Are you sure you want to remove this user permission ?', function(result) {
            if (result) {
                var url = "{{route('common.activityTracker.deleteUserPermission', ['id'=>'%recordId%'])}}";
                url = url.replace('%recordId%', id);
                $.ajax({
                    type: "DELETE",
                    url: url,
                    data: {
                        '_token': "{{csrf_token()}}"
                    },
                    success: function(response) {
                        if (response.success) {
                            loadUserPermissionList();
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

    function showPermissionModal(){
        $('#userPermissionForm')[0].reset();
        $('#userPermissionModal').modal('show');
    }

    function hidePermissionModal(){
        $('#userPermissionForm')[0].reset();
        $('#userPermissionModal').modal('hide');
    }

    $(function() {
        loadUserPermissionList();

        /**
         * Reload list.
         */
        $('#searchFilterForm').on('submit', function(e) {
            $("#searchFilter").toggleClass("open");
            loadUserPermissionList();
        });
    });
</script>

@endsection
