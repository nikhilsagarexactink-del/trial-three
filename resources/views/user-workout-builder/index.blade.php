@php $userType = userType(); @endphp
@extends('layouts.app')
<title>Training Videos</title>
@section('content')
    @include('layouts.sidebar')
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                 <!-- Breadcrumb Start -->
                 <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                        <li class="breadcrumb-item view" aria-current="page">My Workouts</li>
                    </ol>
                </nav>
            <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    My Workouts
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
                            <div class="col-md-3">
                            <div class="form-group select-arrow">
                                <select class="selectpicker select-custom form-control " title="Status" data-size="4" name="status" id="statusId">
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>                     
                            <div class="btn_clumn mb-3 position-sticky">
                                <button type="submit" class="btn btn-secondary ripple-effect">Search</button>
                                <button type="button" class="btn btn-outline-danger ripple-effect" onClick="resetFilter()" id="clearSearchFilterId">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <!-- filter section end -->
        <div class="white-bg pt-5">
            <div class="recipe-list-sec">
                <div class="container">
                    <div class="row" id="listId"></div>
                </div>
            </div>
            <!--Pagination-->
            <div class="container">
                <div id="paginationLink" class="pb-5 mt-3"></div>
            </div>
            <!--Pagination-->
        </div>
    </div>
    <!-- Main Content Start -->

@endsection
@section('js')
    <script>
        $(document).ready(function() {
            loadUserWorkouts();
            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
                loadUserWorkouts();
            });
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadUserWorkouts(url) {
            $("#listId").html('{{ ajaxListLoader() }}');
            url = url || "{{ route('common.loadUserWorkouts') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    search: $("#searchFiledId").val(),
                    moduleType: 'users-wrkouts',
                    perPage: 9,
                    status: $("#statusId").val(),
                    type :'my-workouts',
                },
                success: function(response) {
                    if (response.success) {
                        $("#listId").html("");
                        $("#paginationLink").html("");
                        $('#listId').append(response.data.html);
                        $('#paginationLink').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });
        }

        function resetFilter() {
            $('#searchFilterForm')[0].reset();
            loadUserWorkouts();
        }
    </script>
@endsection
