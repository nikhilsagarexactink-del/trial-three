@extends('layouts.app')
<title>Workout & Exercise</title>
@section('content')
    @include('layouts.sidebar')
    @php $userType = userType(); @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content">
            <!-- Workout Tab Content -->
            <div >
                <div>
                    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
                        <div class="left-side">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Advanced Workouts</li>
                                </ol>
                            </nav>
                            <h2 class="page-title text-capitalize mb-0">Advanced Workouts</h2>
                        </div>
                        <div class="right-side mt-2 mt-md-0">
                            <a href="{{ route('user.addFormWorkout', ['user_type' => $userType]) }}"
                                class="btn btn-secondary ripple-effect-dark text-white">Create</a>
                        </div>
                    </div>
                    <div class="filter_section with-button filter_section_open px-0" id="searchFilterWorkout">
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
                                            <input type="text" class="form-control" placeholder="Search"
                                                name="search" id="searchFiledIdWorkout">
                                        </div>
                                        <div class="form-group select-arrow">
                                            <select class="selectpicker select-custom form-control" title="Status"
                                                data-size="4" name="status" id="statusIdWorkout">
                                                <option value="">All</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="category_id" name="category_id"
                                                class="js-states form-control selectpicker">
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="difficulty_id" name="difficulty_id"
                                                class="js-states form-control selectpicker">
                                                <option value="">Select Difficulty</option>
                                                @foreach ($difficulties as $difficulty)
                                                    <option value="{{ $difficulty->id }}">{{ $difficulty->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="age_range_id" name="age_range_id"
                                                class="js-states form-control selectpicker">
                                                <option value="">Select Age Range</option>
                                                @foreach ($ageRanges as $ageRange)
                                                    <option value="{{ $ageRange->id }}">{{ $ageRange->min_age_range }} -
                                                        {{ $ageRange->max_age_range }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="btn_clumn mb-3 position-sticky">
                                            <button type="submit" class="btn btn-secondary ripple-effect">Search</button>
                                            <button type="button" class="btn btn-outline-danger ripple-effect"
                                                id="clearSearchFilterIdWorkout">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th><span class="sorting" >Name</span></th>
                                <th><span class="sorting">Exercise(No. of reps)</span></th>
                                <th><span class="sorting">Description</span></th>
                                <th><span class="sorting">Days</span></th>
                                <th><span class="sorting" sort-by="status">Status</span></th>
                                <th class="w_130">Action</th>
                            </tr>
                        </thead>
                        <tbody id="workoutId"></tbody>
                    </table>
                    <!--Pagination-->
                    <div id="paginationLinkWorkout"></div>
                    <!--Pagination-->
                </div>
            </div>
        </section>
        <!-- Workout Detail Model Start-->
        <div class="modal fade" id="workoutDetailModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Workout Detail</h5>
                        <button type="button" onClick="closeWorkoutDetail()" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <section >
                            <div class="container">
                                <div class="card payment-profile-card">
                                    <div class="card-body">
                                        <div class="payment-profile-disc">
                                            <ul>
                                                <li>
                                                    <h5>Workout Name:</h5>
                                                    <p name="workoutName">-</p>
                                                </li>
                                                <li>
                                                    <h5>Exercise (No. of reps):</h5>
                                                    <p name="exerciseReps">-</p>
                                                </li>
                                                <li>
                                                    <h5>Category:</h5>
                                                    <p name="category">-</p>
                                                </li>
                                                <li>
                                                    <h5>Difficulty:</h5>
                                                    <p name="difficulty">-</p>
                                                </li>
                                                <li>
                                                    <h5>Age Group:</h5>
                                                    <p name="ageGroup">-</p>
                                                </li>
                                                <li>
                                                    <h5>Sport:</h5>
                                                    <p name="sport">-</p>
                                                </li>
                                                <li>
                                                    <h5>Days:</h5>
                                                    <p name="days" style="text-transform: capitalize;">-</p>
                                                </li>
                                                <li>
                                                    <h5>Description:</h5>
                                                    <p name="description">-</p>
                                                </li>
                                                <li>
                                                    <h5>Workout Image:</h5>
                                                    <img style="height:50px;width:50px;" id="imagePreview" src="{{ url('assets/images/default-image.png') }}">
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <div class="btn_row text-end">
                            <button class="btn btn-secondary ripple-effect-dark btn-120" id="submit-button">Add to My Profile</button>
                            <a href="javascript:void(0)" onClick="closeWorkoutDetail()"class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Workout Detail Model End -->
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            loadAdvancedWorkout();
        });

        $("#searchFilterFormWorkout").on("submit", function(e) {
            loadAdvancedWorkout();
        });

        $("#clearSearchFilterIdWorkout").on("click", function(e) {
            $("#searchFilterFormWorkout")[0].reset();
            loadAdvancedWorkout();
        });
        
        function openWorkoutDetail(workout) {
            // Set dynamic data to the modal fields
            $('#workoutDetailModel [name="workoutName"]').text(workout.name || '-');
            $('#workoutDetailModel [name="exerciseReps"]').text(workout.no_of_reps || '0');
            const categories = workout?.categories.map(category => category.name);
            const difficulties = workout?.difficulties.map(difficulty => difficulty.name);
            const ageRanges = workout?.age_ranges.map(ageRange => ageRange.min_age_range + '-' + ageRange.max_age_range);
            const sports = workout?.sports.map(sport => sport.name);
            $('#workoutDetailModel [name="category"]').text(categories || '-');
            $('#workoutDetailModel [name="difficulty"]').text(difficulties || '-');
            $('#workoutDetailModel [name="ageGroup"]').text( ageRanges|| '-');
            $('#workoutDetailModel [name="sport"]').text(sports || '-');
            $('#workoutDetailModel [name="days"]').text(JSON.parse(workout.days)?.join(', ') || '-');
            $('#workoutDetailModel [name="description"]').html(workout.description || '-');
            
            // Set workout image dynamically
            const imagePath = workout?.media?.base_url || '{{ url("assets/images/default-image.png") }}';
            $('#workoutDetailModel #imagePreview').attr('src', imagePath);

            // Show the modal
            $('#workoutDetailModel').modal('show');
        }
        function closeWorkoutDetail(workout) {
            $('#workoutDetailModel').modal('hide');
        }
        // Function to load Workout list via AJAX
        function loadAdvancedWorkout(url = '') {
            url = url || "{{ route('common.loadAdvancedWorkout') }}";
            var formData = $('#searchFilterFormWorkout').serialize();

            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $("#workoutId").html("");
                        $("#paginationLinkWorkout").html("");
                        $('#workoutId').append(response.data.html);
                        $('#paginationLinkWorkout').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });
        }
    </script>
@endsection
