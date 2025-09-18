@extends('layouts.app')
<title>Training Videos</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $categoryId = request()->query('category');
        $categoryName = '';
        foreach ($categories as $category) {
            if ($categoryId == $category['id']) {
                $categoryName = $category['name'];
            }
        }
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
            <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Training Videos</li>
                        </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Training Videos @if (!empty($categoryName))
                        ({{ $categoryName }})
                    @endif
                </h2>
                <!-- Page Title End -->
            </div>
            <!-- <div class="right-side">
                                                            <h3 class="h-24 font-semi">"PEAK PERFORMANCE STARTS WITH A PROPER DIET"</h3>
                                                        </div> -->
        </div>
        <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm"></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!-- Header text End -->

        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="filterHead d-flex justify-content-between">
                <!-- <h3 class="h-24 font-semi">What would you like to see ?</h3> -->
            </div>
            <div class="flex-row justify-content-between align-items-end">
                <div class="left  recipe-filter">
                    <!-- <h5 class="fs-6 label">Search By</h5> -->
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form_field flex-wrap pr-0">
                            <div class="w-100">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center ms-auto">
                                        <label class="recipe-head mb-0 me-4" for="">What would you like to
                                            learn?</label>
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" placeholder="Search" name="search"
                                                id="searchFiledId">
                                            <button type="submit" class="btn btn-secondary ripple-effect"><i
                                                    class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                    <div class=" ms-auto ps-3 filter-btn">
                                        <button type="button" id="isFavourite"
                                            class="btn btn-secondary ripple-effect-dark ">
                                            <img class="" src="{{ url('assets/images/heart-fill.svg') }}">
                                        </button>
                                        <button type="button" onClick="resetFilter()"
                                            class="btn btn-primary ripple-effect">Reset</button>
                                    </div>
                                </div>
                            </div>

                            <div class="recipe-categories w-100 mt-5">
                                @if (!empty($categories))
                                    <!-- <label>Categories</label> -->
                                    <div class="recipe-cta justify-content-center">
                                        @foreach ($categories as $category)
                                            <a href="javascript:void(0)" class="btn btn-outline categories"
                                                id="cat_{{ $category->id }}" dataId="{{ $category->id }}"> <i
                                                    class="fas fa-search"></i> {{ ucfirst($category['name']) }}</a>
                                        @endforeach
                                        <a href="javascript:void(0)" class="btn btn-outline" style="display:none"
                                            id="view_all_category" onClick="resetCategoryFilter()">View All</a>
                                    </div>
                                @endif
                            </div>
                            <!-- <div class="recipe-categories w-100 mt-4">
                                                                            @if (!empty($ageRanges))
    <label>Age Ranges</label>
                                                                            <div class="recipe-cta justify-content-center">
                                                                                @foreach ($ageRanges as $ageRange)
    <a href="javascript:void(0)" class="btn btn-outline ageRanges" id="age_range_{{ $ageRange->id }}" dataId="{{ $ageRange->id }}"> <img class="" src="{{ url('assets/images/calendar.svg') }}"> {{ $ageRange['min_age_range'] . '-' . $ageRange['max_age_range'] }}</a>
    @endforeach
                                                                            </div>
    @endif
                                                                        </div> -->
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div class="white-bg pt-5">
            <!-- filter section end -->
            <div class="recipe-list-sec">
                <div class="list-filter">
                    @if (!empty($ageRanges))
                        <div class="dropdown">
                            <button class="btn btn-outline btn-popular dropdown-toggle" type="button"
                                id="ageRangedropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                Age Ranges
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="ageRangedropdownMenuButton1">
                                <li><a href="javascript:void(0)" class="dropdown-item ageRanges" id="age_range_reset"
                                        dataId="">Reset Age Range</a></li>
                                @foreach ($ageRanges as $ageRange)
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item ageRanges"
                                            id="age_range_{{ $ageRange->id }}" dataId="{{ $ageRange->id }}"> <img
                                                class="" src="{{ url('assets/images/calendar.svg') }}">
                                            {{ $ageRange['min_age_range'] . '-' . $ageRange['max_age_range'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-outline btn-popular dropdown-toggle" type="button"
                            id="sortingDropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Sort By
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortingDropdownMenuButton1">
                            <li><a class="dropdown-item sorting" href="javascript:void(0)">Newest</a></li>
                            <li><a class="dropdown-item sorting" href="javascript:void(0)">Age Group</a></li>
                            <li><a class="dropdown-item sorting" href="javascript:void(0)">Highest Rated</a></li>
                        </ul>
                    </div>
                    <!-- <a href="javascript:void(0)" class="btn btn-outline btn-popular">popular</a> -->
                </div>
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
        <div id="inline" style="background:#fff" class="lity-hide">
            Inline content
        </div>
    </div>
    <!-- Main Content Start -->

@endsection
@section('js')
    <script>
        loadHeaderText('training-library');
        let categoryId = '';
        let ageRangId = '';
        let isFavourite = false;
        let orderBy = {
            field: '', //'training_videos.created_at',
            order: 'DESC',
        };
        $(document).ready(function() {
            categoryId = "{{ $categoryId }}";
            if (categoryId) {
                $('#cat_' + categoryId).addClass("active");
            }
            loadTrainingList();
            /**
             * Reload list.
             */
            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
                loadTrainingList();
            });


            $('.sorting').on('click', function(e) {
                $('#sortingDropdownMenuButton1').text(this.text);
                if (this.text == 'Highest Rated') {
                    orderBy.field = 'avg_ratings';
                } else if (this.text == 'Age Group') {
                    orderBy.field = 'max_age_range';
                } else {
                    orderBy.field = 'training_videos.date';
                }

                loadTrainingList();
            });
            $('#isFavourite').on('click', function(e) {
                $('#isFavourite').toggleClass('active');
                isFavourite = true;
                loadTrainingList();
            });
            $('.categories').on('click', function(e) {
                if ($('#' + this.id).hasClass('active')) {
                    $('.categories').removeClass('active');
                    categoryId = "";
                } else {
                    $('.categories').removeClass('active');
                    $('#' + this.id).addClass('active');
                    categoryId = $(this).attr("dataId");
                }
                let isCatActive = $('a.categories').hasClass('active');
                $('#view_all_category').hide();
                if (isCatActive) {
                    $('#view_all_category').show();
                }
                loadTrainingList();
            });
            $('.ageRanges').on('click', function(e) {
                $('.ageRanges').removeClass('active');
                $('#' + this.id).addClass('active');
                ageRangId = $(this).attr("dataId");
                if (ageRangId) {
                    $('#ageRangedropdownMenuButton1').text(this.text);
                } else {
                    $('#ageRangedropdownMenuButton1').text('Age Ranges');
                }

                loadTrainingList();
            });
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadTrainingList(url) {
            $("#listId").html('{{ ajaxListLoader() }}');
            url = url || "{{ route('common.loadTrainingVideoListForUser') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    sort_by: orderBy.field,
                    sort_order: orderBy.order,
                    search: $("#searchFiledId").val(),
                    categoryIds: categoryId,
                    ageRangId: ageRangId,
                    isFavourite: isFavourite,
                    perPage: 9
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
            categoryId = '';
            ageRangId = '';
            isFavourite = false;
            orderBy = {
                field: 'training_videos.created_at',
                order: 'DESC',
            };
            $('#sortingDropdownMenuButton1').text('Sort By');
            $('#ageRangedropdownMenuButton1').text('Age Ranges');
            $('.categories').removeClass('active');
            $('.ageRanges').removeClass('active');
            $('#isFavourite').removeClass('active');
            $('#searchFilterForm')[0].reset();
            loadTrainingList();
        }

        function resetCategoryFilter() {
            categoryId = '';
            $('.categories').removeClass('active');
            $('#view_all_category').hide();
            loadTrainingList();
        }
        /**
         * Save Rating
         * @request form fields
         * @response object.
         */
        function saveRating(rating, id) {
            var url = "{{ route('common.saveTrainingVideoRating', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', id);
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                    rating: rating
                },
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        $("#totalRatings" + id).text(data.total_ratings + ' Ratings');
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    cosole.log(err);
                },
            });
        };

        /**
         * Save Favourite
         * @request form fields
         * @response object.
         */
        function addToFavourite(favourite, id) {
            var url = "{{ route('common.saveTrainingVideoFavourite', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', id);
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                    favourite: favourite
                },
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        if (data.is_favourite) {
                            $("#isUnFavourite" + id).show();
                            $("#isFavourite" + id).hide();
                        } else {
                            $("#isUnFavourite" + id).hide();
                            $("#isFavourite" + id).show();
                        }
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    console.log(err);
                },
            });
        };
    </script>
@endsection
