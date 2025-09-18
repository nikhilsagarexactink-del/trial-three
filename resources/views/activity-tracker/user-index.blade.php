@extends('layouts.app')
<title>Activity Tracker</title>
@section('content')
@include('layouts.sidebar')
@php $userType = userType(); @endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <h2 class="page-title text-capitalize mb-3">
                Users
            </h2>
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
        <!--Pagination-->
        <div id="paginationLink"></div>
        <!--Pagination-->
    </div>

</div>
<!-- Main Content End -->
@endsection
@section('js')
<script>
    function loadUserList(url = '') {
        $("#listId").html('{{ajaxTableListLoader()}}');
        url = url || "{{route('common.activityTracker.userList')}}";
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

    $(function() {
        loadUserList();

        /**
         * Reload list.
         */
        $('#searchFilterForm').on('submit', function(e) {
            $("#searchFilter").toggleClass("open");
            loadUserList();
        });

        $('#clearSearchFilterId').on('click', function(e) {
                $('#searchFilterForm')[0].reset();
                //$('.selectpicker').selectpicker('refresh')
                loadUserList();
        });
    });
</script>

@endsection