@extends('layouts.app')
<title>My Challenges</title>
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
                        <li class="breadcrumb-item active" aria-current="page">My Challenges</li>
                    </ol>
                </nav>
                <h2 class="page-title text-capitalize mb-0">My Challenges</h2>
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
                                <div class="form-group select-arrow">
                                    <select class="selectpicker select-custom form-control" title="Status" data-size="4"
                                        name="type" id="statusIdWorkout">
                                        <option value="" disabled >Challenge Type</option>
                                        <option value="">All</option>
                                        <option value="workouts">Workouts</option>
                                        <option value="water-intake">Water Intake</option>
                                        <option value="step-counter">Step Counter</option>
                                        <option value="sleep-tracker">Sleep Tracker</option>
                                        <option value="food-tracker">Food Tracker</option>
                                    </select>
                                </div>
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
        <!-- User Section -->
        <div class="white-bg pt-5">
            <div class="recipe-list-sec">
                <div class="container">
                    <div class="row" id="challengeList"></div>
                </div>
            </div>
        </div>
        <!--Pagination-->
        <div id="paginationLinkWorkout"></div>
        <!--Pagination-->
</div>
</section>
</div>
<div class="modal fade" id="challengeDescriptionModal" tabindex="-1" role="dialog" aria-labelledby="challengeDescriptionModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                {{-- <span class="challengeType"></span> --}}
                <button type="button" onClick="hideModal()" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="reviewListDivId">
                <div class="row">
                    <div class="col-md-12 challengeDescription"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onClick="hideModal()" id="gotItBtn" class="btn btn-secondary"
                    data-dismiss="modal">Got It</button>
            </div>
        </div>
    </div>
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
    loadUserChallenges();

    // Reload Workout list on form submit
    $('#searchFilterFormWorkout').on('submit', function(e) {
        e.preventDefault();
        $("#searchFilterWorkout").toggleClass("open");
        loadUserChallenges();
    });

    // Clear search filter for Workout tab
    $('#clearSearchFilterIdWorkout').on('click', function(e) {
        e.preventDefault();
        $('#searchFilterFormWorkout')[0].reset();
        loadUserChallenges();
    });

    // Sorting event listener
    $('.sorting').on('click', function(e) {
        var sortBy = $(this).attr('sort-by');
        var sortOrder = (orderBy['order'] === 'DESC') ? 'ASC' : 'DESC';
        orderBy['order'] = sortOrder;

        $("#sortByFieldIdWorkout").val(sortBy);
        $("#sortOrderFieldIdWorkout").val(sortOrder);
        loadUserChallenges(false);
    });
});

// Function to load Workout list via AJAX
function loadUserChallenges(url = '') {
    url = url || "{{ route('common.loadUserChallenges') }}";
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
    function showModal(data) {
        $("#exampleModalLabel").html(data.title);
        $(".challengeType").html("(" + data.type.replace('-', ' ') + ")" );
        $(".challengeDescription").html(data.description);
        $("#challengeDescriptionModal").modal('show');
    }

    function hideModal() {
        $("#challengeDescriptionModal").modal('hide');
    }

    function challengeSignup(index) {
        let formData = $(`#challengeSignup-${index}`).serializeArray();
        console.log(formData);
        $.ajax({
            type: "POST",
            url: "{{ route('common.signupChallenge') }}",
            data: formData,
            success: function(response) {
                if (response.success) {
                    _toast.success(response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                } else {
                    _toast.error('Something went wrong. Please try again');
                }
            },
            error: function(err) {
                _toast.error((err.responseJSON?.message || err.responseText || 'Something went wrong.'));
            },
        });
    }
</script>
@endsection