@extends('layouts.app')
<title>Fitness Challenges</title>
@section('content')
@include('layouts.sidebar')
@php
$userType = userType();
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <section>
        <!-- Workout Tab Content -->
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Fitness Challenges</li>
                    </ol>
                </nav>
                <h2 class="page-title text-capitalize mb-0">Fitness Challenges</h2>
            </div>
            <div class="right-side mt-2 mt-md-0">
                <a href="{{ route('user.addChallenge', ['user_type' => $userType]) }}"
                    class="btn btn-secondary ripple-effect-dark text-white">Create</a>
            </div>
        </div>
        <div class="filter_section challange-filter with-button filter_section_open" id="searchFilterWorkout">
            <div class="filterHead d-flex justify-content-between">
                <h3 class="h-24 font-semi">Filter</h3>
                <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i
                        class="iconmoon-close"></i></a>
            </div>
            <div class="flex-row justify-content-between align-items-end">
                <div class="left">
                    <h5 class="fs-6 label">Search By</h5>
                    <form action="javascript:void(0)" id="searchFilterFormWorkout">
                        <input type="hidden" name="sort_by" id="sortByFieldIdWorkout" value="created_at">
                        <input type="hidden" name="sort_order" id="sortOrderFieldIdWorkout" value="DESC">
                        <div class="form_field flex-wrap pr-0">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search" name="search"
                                    id="searchFiledIdWorkout">
                            </div>
                            @if($userType == 'admin' || $userType == 'parent')
                            <div class="form-group select-arrow">
                                <select class="selectpicker select-custom form-control" title="Status" data-size="4"
                                    name="status" id="statusIdWorkout">
                                    <option value="">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="user_role_id" name="user_role_id"
                                    class="js-states form-control selectpicker">
                                    <option value="">Select User Role</option>
                                    @foreach($userRoles as $user_role)
                                    <option value="{{$user_role->id}}">{{$user_role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="workout_id" name="workout_id" class="js-states form-control selectpicker">
                                    <option value="">Select Workout</option>
                                    @foreach($workouts as $workout)
                                    <option value="{{$workout->id}}">{{$workout->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="type" name="type" class="js-states form-control selectpicker">
                                    <option value="">Select Type</option>
                                    <option value="Workouts">Workouts</option>
                                    <option value="Food Tracker">Food Tracker</option>
                                    <option value="Step Counter">Step Counter</option>
                                    <option value="Water Intake">Water Intake</option>
                                    <option value="Sleep Tracker">Sleep Tracker</option>
                                </select>
                            </div>
                            @else
                            <input type="hidden" name="status" id="statusIdWorkout" value="active">
                            @endif
                            <div class="btn_clumn mb-3 position-sticky">
                                <button type="submit" class="btn btn-secondary ripple-effect">Search</button>
                                <button type="button" class="btn btn-outline-danger ripple-effect"
                                    id="clearSearchFilterIdWorkout">Reset</button>
                            </div>
                        </div>
                        <input type="hidden" id="perPage" name="perPage" value='10'>
                    </form>
                </div>
            </div>
        </div>
        <!-- Admin Section -->
        @if($userType == 'admin')
        <div class="common-table white-bg">
            <div class="mCustomScrollbar" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th><span class="sorting">Title</span></th>
                            <!-- <th><span class="sorting">Exercise(No. of reps)</span></th> -->
                            <th><span class="sorting">Description</span></th>
                            <th><span class="sorting">Go Live Date</span></th>
                            <th><span class="sorting">Days</span></th>
                            <th><span class="sorting">Type</span></th>
                            <th><span class="sorting" sort-by="status">Status</span></th>
                            <th class="w_130">Action</th>
                        </tr>
                    </thead>
                    <tbody id="challengeList"></tbody>
                </table>
            </div>
        </div>
        @else

        <div class="white-bg pt-5">
            <div class="recipe-list-sec">
                <div class="container">
                    <div class="row" id="challengeList"></div>
                </div>
            </div>
        </div>
        @endif
        <!--Pagination-->
        <div id="paginationLinkWorkout"></div>
        <!--Pagination-->
</div>
</section>
</div>
@endsection

@section('js')
<script>
var orderBy = {
    field: 'created_at',
    order: 'DESC',
};

$(document).ready(function() {
    // Load initial data for both tabs
    loadChallengeList();

    // Reload Workout list on form submit
    $('#searchFilterFormWorkout').on('submit', function(e) {
        e.preventDefault();
        $("#searchFilterWorkout").toggleClass("open");
        loadChallengeList();
    });

    // Clear search filter for Workout tab
    $('#clearSearchFilterIdWorkout').on('click', function(e) {
        e.preventDefault();
        $('#searchFilterFormWorkout')[0].reset();
        loadChallengeList();
    });

    // Sorting event listener
    $('.sorting').on('click', function(e) {
        var sortBy = $(this).attr('sort-by');
        var sortOrder = (orderBy['order'] === 'DESC') ? 'ASC' : 'DESC';
        orderBy['order'] = sortOrder;

        $("#sortByFieldIdWorkout").val(sortBy);
        $("#sortOrderFieldIdWorkout").val(sortOrder);
        loadChallengeList(false);
    });
});

// Function to load Workout list via AJAX
function loadChallengeList(url = '') {
    url = url || "{{ route('common.loadChallengeList') }}";
    var formData = $('#searchFilterFormWorkout').serialize();

    $.ajax({
        type: "GET",
        url: url,
        data: formData,
        success: function(response) {
            if (response.success) {
                $("#challengeList").html("");
                $("#paginationLinkWorkout").html("");
                $('#challengeList').append(response.data.html);
                $('#paginationLinkWorkout').append(response.data.pagination);
            }
        },
        error: function() {
            _toast.error('Something went wrong.');
        }
    });
}


/**
 * Change status.
 * @request id
 * @response object.
 */
function changeChallengeStatus(id, status) {
    var statusType = (status == 'deleted') ? 'delete' : status;
    bootbox.confirm('Are you sure you want to ' + statusType + ' this Challenge ?', function(result) {
        if (result) {
            var url = "{{ route('common.changeChallengeStatus', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', id);
            $.ajax({
                type: "PUT",
                url: url,
                data: {
                    '_token': "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        loadChallengeList();
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

function deleteChallenge(id) {
    var statusType = (status == 'deleted') ? 'delete' : status;
    bootbox.confirm('Are you sure you want to delete this Challenge ?', function(result) {
        if (result) {
            var url = "{{ route('common.deleteChallenge', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', id);
            $.ajax({
                type: "DElETE",
                url: url,
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {
                        loadChallengeList();
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