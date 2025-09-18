@extends('layouts.app')
<title>Workout & Exercise</title>
@section('content')
    @include('layouts.sidebar')
    @php 
        $userType = userType();
        $workoutLabel = $userType == 'admin' ? "Manage Workout" : "My Custom Workouts";
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="right-side mt-2 mt-md-0 text-right">
        <a href="{{ route('user.indexWorkoutGoal', ['user_type' => $userType]) }}"
        class="btn btn-secondary ripple-effect-dark text-white">Workout Goal</a>
        </div>
        <section class="work-builder-tab">
            <ul class="nav nav-tabs admin-tab" style="margin:0;" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link top-radius font-weight-bold active" id="Exercise-tab" data-bs-toggle="tab"
                        data-bs-target="#Exercise" type="button" role="tab" aria-controls="Exercise"
                        aria-selected="true">Exercise</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link top-radius font-weight-bold" id="Workout-tab" data-bs-toggle="tab"
                        data-bs-target="#Workout" type="button" role="tab" aria-controls="Workout"
                        aria-selected="false">Workout</button>
                </li>
            </ul>
        </section>
        <section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content" id="myTabContent">
            <!-- Exercise Tab Content -->
            <div class="tab-pane fade show active" id="Exercise" role="tabpanel" aria-labelledby="Exercise-tab">
                <div>
                    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
                        <div class="left-side">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Manage Exercise</li>
                                </ol>
                            </nav>
                            <h2 class="page-title text-capitalize mb-0">Manage Exercise</h2>
                        </div>
                        <div class="right-side mt-2 mt-md-0">
                            <a href="{{ route('user.addFormExercise', ['user_type' => $userType]) }}"
                                class="btn btn-secondary ripple-effect-dark text-white">Create</a>
                        </div>
                    </div>
                    <div class="filter_section challange-filter with-button filter_section_open px-0" id="searchFilterExercise">
                        <div class="filterHead d-flex justify-content-between">
                            <h3 class="h-24 font-semi">Filter</h3>
                            <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i
                                    class="iconmoon-close"></i></a>
                        </div>
                        <div class="flex-row justify-content-between align-items-end">
                            <div class="left">
                                <h5 class="fs-6 label">Search By</h5>
                                <form action="javascript:void(0)" id="searchFilterFormExercise">
                                    <input type="hidden" name="sort_by" id="sortByFieldIdExercise" value="created_at">
                                    <input type="hidden" name="sort_order" id="sortOrderFieldIdExercise" value="DESC">
                                    <div class="form_field flex-wrap pr-0">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Search" name="search"
                                                id="searchFiledIdExercise">
                                        </div>
                                        <div class="form-group select-arrow">
                                            <select class="selectpicker select-custom form-control" title="Status"
                                                data-size="4" name="status" id="statusIdExercise">
                                                <option value="">All</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="category_id" name="category_id"
                                                class="js-states form-control form-select selectpicker">
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="difficulty_id" name="difficulty_id"
                                                class="js-states form-control form-select selectpicker">
                                                <option value="">Select Difficulty</option>
                                                @foreach ($difficulties as $difficulty)
                                                    <option value="{{ $difficulty->id }}">{{ $difficulty->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="age_range_id" name="age_range_id"
                                                class="js-states form-control form-select selectpicker">
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
                                                id="clearSearchFilterIdExercise">Reset</button>
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
                                <th><span class="sorting">Name</span></th>
                                {{-- <th><span class="sorting">No. of reps</span></th> --}}
                                <th><span class="sorting">Description</span></th>
                                <th><span class="sorting" sort-by="status">Status</span></th>
                                <th class="w_130">Action</th>
                            </tr>
                        </thead>
                        <tbody id="exerciseId"></tbody>
                    </table>
                    <!--Pagination-->
                    <div id="paginationLinkExercise"></div>
                    <!--Pagination-->
                </div>
            </div>
            <!-- Workout Tab Content -->
            <div class="tab-pane fade" id="Workout" role="tabpanel" aria-labelledby="Workout-tab">
                <div>
                    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
                        <div class="left-side">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">{{$workoutLabel}}</li>
                                </ol>
                            </nav>
                            <h2 class="page-title text-capitalize mb-0">{{$workoutLabel}}</h2>
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
                                        <div class="form-group">
                                            <select id="category_id" name="category_id"
                                                class="js-states form-control selectpicker">
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if($userType == 'admin' || $userType == 'parent')
                                            <div class="form-group select-arrow">
                                                <select class="selectpicker select-custom form-control" title="Status"
                                                    data-size="4" name="status" id="statusIdWorkout">
                                                    <option value="">All</option>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select id="workout_difficulty_id" name="difficulty_id"
                                                    class="js-states form-control selectpicker">
                                                    <option value="">Select Difficulty</option>
                                                    @foreach ($difficulties as $difficulty)
                                                        <option value="{{ $difficulty->id }}">{{ $difficulty->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select id="workout_age_range_id" name="age_range_id"
                                                    class="js-states form-control selectpicker">
                                                    <option value="">Select Age Range</option>
                                                    @foreach ($ageRanges as $ageRange)
                                                        <option value="{{ $ageRange->id }}">{{ $ageRange->min_age_range }} -
                                                            {{ $ageRange->max_age_range }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select id="athlete_id" name="athlete_id"
                                                    class="js-states form-control selectpicker">
                                                    <option value="">Select Athlete</option>
                                                    @foreach ($athletes as $athlete)
                                                        <option value="{{ $athlete->id }}">
                                                            {{ ucfirst($athlete->first_name) . ' ' . $athlete->last_name }}
                                                        </option>
                                                    @endforeach
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
                                    <input type="hidden" id="perPage" name="perPage" value= '10'>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Admin Section -->
                    @if($userType == 'admin')
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th><span class="sorting">Name</span></th>
                                    <!-- <th><span class="sorting">Exercise(No. of reps)</span></th> -->
                                    <th><span class="sorting">Description</span></th>
                                    <th><span class="sorting">Days</span></th>
                                    <th><span class="sorting" sort-by="status">Status</span></th>
                                    <th class="w_130">Action</th>
                                </tr>
                            </thead>
                            <tbody id="workoutId"></tbody>
                        </table>
                    @else
                    <section class="work-builder-tab">
                        <ul class="nav nav-tabs admin-tab" style="margin:0;" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link top-radius font-weight-bold active" id="Active-tab" data-bs-toggle="tab"
                                     type="button" role="tab" aria-controls="Exercise"
                                    aria-selected="true">Workouts In Use</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link top-radius font-weight-bold" id="InActive-tab" data-bs-toggle="tab" type="button" role="tab" aria-controls="Workout"
                                    aria-selected="false">Workouts To Use</button>
                            </li>
                        </ul>
                    </section>
                    <div class="white-bg pt-5">
                        <div class="recipe-list-sec">
                            <div class="container">
                                <div class="row" id="workoutId"></div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!--Pagination-->
                    <div id="paginationLinkWorkout"></div>
                    <!--Pagination-->
                </div>
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
            loadExerciseList();
            loadWorkoutList();

            // Handle initial tab showing
            if (localStorage.getItem('activeTab') === 'Workout') {
                $('#Workout-tab').tab('show');
                localStorage.removeItem('activeTab');
            } else {
                $('#Exercise-tab').tab('show'); // Default to Exercise tab
            }

            // Reload Exercise list on form submit
            $('#searchFilterFormExercise').on('submit', function(e) {
                e.preventDefault();
                $("#searchFilterExercise").toggleClass("open");
                loadExerciseList();
            });

            // Reload Workout list on form submit
            $('#searchFilterFormWorkout').on('submit', function(e) {
                e.preventDefault();
                $("#searchFilterWorkout").toggleClass("open");
                loadWorkoutList();
            });

            // Clear search filter for Exercise tab
            $('#clearSearchFilterIdExercise').on('click', function(e) {
                e.preventDefault();
                $('#searchFilterFormExercise')[0].reset();
                loadExerciseList();
            });

            // Clear search filter for Workout tab
            $('#clearSearchFilterIdWorkout').on('click', function(e) {
                e.preventDefault();
                $('#searchFilterFormWorkout')[0].reset();
                loadWorkoutList();
            });

            // Tab event listeners
            const exerciseTab = document.getElementById('Exercise-tab');
            const workoutTab = document.getElementById('Workout-tab');
            const activeTab = document.getElementById('Active-tab');
            const inActiveTab = document.getElementById('InActive-tab');

            exerciseTab.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem('activeTab', 'Exercise');
                loadExerciseList();
            });

            workoutTab.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem('activeTab', 'Workout');
                loadWorkoutList();
            });

            activeTab.addEventListener('shown.bs.tab', function(event) {
                // Set value of existing status input
                $("#statusIdWorkout").val('active');
                loadWorkoutList();
            });
            inActiveTab.addEventListener('shown.bs.tab', function(event) {
                // Set value of existing status input
                $("#statusIdWorkout").val('inactive');
                loadWorkoutList();
            });

            // Sorting event listener
            $('.sorting').on('click', function(e) {
                var sortBy = $(this).attr('sort-by');
                var sortOrder = (orderBy['order'] === 'DESC') ? 'ASC' : 'DESC';
                orderBy['order'] = sortOrder;

                $("#sortByFieldIdExercise").val(sortBy);
                $("#sortOrderFieldIdExercise").val(sortOrder);
                loadExerciseList(false);

                $("#sortByFieldIdWorkout").val(sortBy);
                $("#sortOrderFieldIdWorkout").val(sortOrder);
                loadWorkoutList(false);
            });
        });

        // Function to load Exercise list via AJAX
        function loadExerciseList(url = '') {
            url = url || "{{ route('common.loadExerciseList') }}";
            var formData = $('#searchFilterFormExercise').serialize();

            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $("#exerciseId").html("");
                        $("#paginationLinkExercise").html("");
                        $('#exerciseId').append(response.data.html);
                        $('#paginationLinkExercise').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });
        }

        // Function to load Workout list via AJAX
        function loadWorkoutList(url = '') {
            url = url || "{{ route('common.loadWorkoutList') }}";
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


        /**
         * Change status.
         * @request id
         * @response object.
         */
        function changeWorkoutStatus(id, status) {
            var statusType = (status == 'deleted') ? 'delete' : status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this workout ?', function(result) {
                if (result) {
                    var url = "{{ route('common.changeWorkoutExerciseStatus', ['id' => '%recordId%']) }}";
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
                                loadWorkoutList();
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


        /**
         * Change status.
         * @request id
         * @response object.
         */
        function changeExerciseStatus(id, status) {
            let statusType = (status == 'deleted') ? 'delete' : status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this exercise ?', function(result) {
                if (result) {
                    let url = "{{ route('common.changeWorkoutExerciseStatus', ['id' => '%recordId%']) }}";
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
                                loadExerciseList();
                                _toast.success(response.message);
                            } else {
                                _toast.error(response.message);
                            }
                        },
                        error: function(err) {
                            let errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                            if (err.status === 422) {
                                let errors = $.parseJSON(err.responseText);
                                _toast.error(errors.message);
                            }
                        }
                    });
                }
            })
        }

        /**
         * Clone.
         * @request id
         * @response object.
         */
        function cloneWorkout(id) {
            bootbox.confirm('Are you sure you want to clone this workout ?', function(result) {
                if (result) {
                    let url = "{{ route('common.cloneWorkout', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                loadWorkoutList();
                                _toast.success(response.message);
                            } else {
                                _toast.error(response.message);
                            }
                        },
                        error: function(err) {
                            let errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                            if (err.status === 422) {
                                let errors = $.parseJSON(err.responseText);
                                _toast.error(errors.message);
                            }
                        }
                    });
                }
            })
        }
    </script>
@endsection
