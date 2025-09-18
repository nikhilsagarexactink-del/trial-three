@extends('layouts.app')
<title>Roles</title>
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
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Roles
            </h2>
            <!-- Page Title End -->
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
        <div class="table-responsive mCustomScrollbar" data-mcs-axis='x'>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th><span class="sorting" >Role</span></th>
                        <th><span class="sorting" sort-by="status">Status</span></th>
                        <th><span>Action</span></th>
                    </tr>
                </thead>
                <tbody id="userRoleListingId"></tbody>
            </table>
        </div>
        <!--Pagination-->
        <div id="paginationLink"></div>
        <!--Pagination-->
    </div>

    <!--Permission Modal-->
    <div class="modal fade" id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="permissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Permissions</h5>
                    <button type="button" onClick="closePermissionModal()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="moduleListDivId">
                    <!-- Permission module list -->
                </div>
                <div class="modal-footer">
                    <button type="button" onClick="closePermissionModal()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="moduleSaveBtn" onClick="savePermissions()" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!--Permission Modal-->
</div>
<!-- Main Content Start -->

@endsection
@section('js')
<script>
    var orderBy = {
        field: 'created_at',
        order: 'DESC',
    };
    var selectedRoleId = '';
    $(document).ready(function() {
        getUserRoleList();

        /**
         * Reload list.
         */
        $('#searchFilterForm').on('submit', function(e) {
            $("#searchFilter").toggleClass("open");
            getUserRoleList();
        });

        /**
         * Clear search filter.
         */
        $('#clearSearchFilterId').on('click', function(e) {
            $('#searchFilterForm')[0].reset();
            // $('.selectpicker').selectpicker('refresh')
            getUserRoleList();
        });
    });
    /**
     * Load user role list.
     * @request search, status
     * @response object.
     */
    function getUserRoleList(url) {
        $("#userRoleListingId").html('{{ajaxTableListLoader()}}');
        url = url || "{{route('common.loadUserRoleList')}}";
        var formData = $('#searchFilterForm').serialize();
        $.ajax({
            type: "GET",
            url: url,
            data: formData,
            success: function(response) {
                if (response.success) {
                    $("#userRoleListingId").html("");
                    $("#paginationLink").html("");
                    $('#userRoleListingId').append(response.data.html);
                    $('#paginationLink').append(response.data.pagination);
                }
            },
            error: function() {
                _toast.error('Something went wrong.');
            }
        });
    }

    /**
     * Load module list.
     * @request search, status
     * @response object.
     */
    function getModuleList(id) {
        var url = "{{route('common.loadModulesList', ['id'=>'%recordId%'])}}";
        url = url.replace('%recordId%', id);
        $('#moduleSaveBtn').prop('disabled', false);
        selectedRoleId = id;
        $.ajax({
            type: "GET",
            url: url,
            data: {},
            success: function(response) {
                if (response.success) {
                    $("#moduleListDivId").html(response.data.html);
                    $("#permissionModal").modal("show");
                    if (response.data.data.user_type == 'admin') {
                        $('#moduleSaveBtn').prop('disabled', true);
                    }
                }
            },
            error: function() {
                _toast.error('Something went wrong.');
            }
        });
    }

    function openSearchFilter() {
        $('#searchFilter').toggleClass('open');
    }


    $('.sorting').on('click', function(e) {
        var sortBy = $(this).attr('sort-by');
        var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
        orderBy['order'] = sortOrder;
        $("#sortByFieldId").val(sortBy);
        $("#sortOrderFieldId").val(sortOrder);
        getUserRoleList(false);
    });

    function savePermissions() {
        var modules = [];
        $('.permission_chk:checked').each(function() {
            modules.push($(this).val());
        });
        var url = "{{route('common.saveMoulePermission', ['id'=>'%recordId%'])}}";
        url = url.replace('%recordId%', selectedRoleId);
        $('#moduleSaveBtn').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: url,
            data: {
                moduleIds: modules
            },
            success: function(response) {
                $('#moduleSaveBtn').prop('disabled', false);
                if (response.success) {
                    _toast.success(response.message);
                    $("#permissionModal").modal("hide");
                }
            },
            error: function() {
                $('#moduleSaveBtn').prop('disabled', false);
                _toast.error('Something went wrong.');
            }
        });
    }

    function closePermissionModal() {
        $("#permissionModal").modal("hide");
    }
    $('#searchFilterForm').on('submit', function(e) {
        $("#searchFilter").toggleClass("open");
        getUserRoleList();
    });

    function changeStatus(id, status) {
        var statusType = (status == 'deleted') ? 'delete' : status;
        bootbox.confirm('Are you sure you want to ' + statusType + ' this role ?', function(result) {
            if (result) {
                var url = "{{route('common.changeRoleStatus', ['id'=>'%recordId%'])}}";
                url = url.replace('%recordId%', id);
                $.ajax({
                    type: "PUT",
                    url: url,
                    data: {
                        '_token': "{{csrf_token()}}",
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            getUserRoleList();
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
</script>
@endsection