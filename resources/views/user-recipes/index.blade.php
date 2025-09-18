@extends('layouts.app')
<title>Recipes</title>
@section('content')
    @include('layouts.sidebar')
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Recipes
                </h2>
                <!-- Page Title End -->
            </div>

            <div class="right-side">
                <h4 class="mb-0">"PEAK PERFORMANCE STARTS WITH A PROPER DIET"</h4>
            </div>
        </div>
        <!--Header Text start-->
        <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm" ></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!-- Header text End -->

        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <!-- <div class="filterHead d-flex justify-content-between">
                    <h3 class="h-24 font-semi">What would you like to cook ?</h3>
                </div> -->
            <div class="flex-row justify-content-between align-items-end">
                <div class="left recipe-filter">
                    <!-- <h5 class="fs-6 label">Search By</h5> -->
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form_field flex-wrap pr-0">
                            <div class="w-100">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center ms-auto">
                                        <label class="recipe-head mb-0 me-4" for="">What would you like to cook
                                            ?</label>
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" placeholder="Search" name="search"
                                                id="searchFiledId">
                                            <button type="submit" class="btn btn-secondary ripple-effect"><i
                                                    class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                    <div class="  ms-auto ps-3 filter-btn">
                                        <button type="button" id="isFavourite" class="btn btn-secondary ripple-effect">
                                            <img class="" src="{{ url('assets/images/heart-fill.svg') }}">
                                        </button>
                                        <button type="button" onClick="resetFilter()"
                                            class="btn btn-primary ripple-effect">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="recipe-categories w-100 mt-5">
                            @if (!empty($categories))
                                <!-- <label>Recipe Categories</label> -->
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
                    </form>
                </div>

            </div>
        </div>
        <div class="white-bg pt-5">
            <!-- filter section end -->
            <div class="recipe-list-sec">
                <div class="list-filter">
                    <div class="dropdown">
                        <button class="btn btn-outline btn-popular dropdown-toggle" type="button"
                            id="sortingDropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Sort By
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item sorting" href="javascript:void(0)">Newest</a></li>
                            <li><a class="dropdown-item sorting" href="javascript:void(0)">Highest Rated</a></li>
                        </ul>
                    </div>
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

    </div>
    <!-- Main Content Start -->

@endsection
@section('js')
    <script>
        var orderBy = {
            field: '', //'date',
            order: 'DESC',
        };
        let categoryId = '';
        let isFavourite = false;
        $(document).ready(function() {
            loadRecipeList();
            loadHeaderText('recipes');

            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
                loadRecipeList();
            });

            $('#isFavourite').on('click', function(e) {
                $('#isFavourite').toggleClass('active');
                isFavourite = true;
                loadRecipeList();
            });

            $('.sorting').on('click', function(e) {
                $('#sortingDropdownMenuButton1').text(this.text);
                if (this.text == 'Highest Rated') {
                    orderBy.field = 'avg_ratings';
                } else {
                    orderBy.field = 'recipes.date';
                }

                loadRecipeList();
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
                loadRecipeList();
            });
        });

        function resetCategoryFilter() {
            categoryId = '';
            $('.categories').removeClass('active');
            $('#view_all_category').hide();
            loadRecipeList();
        }

        function resetFilter() {
            categoryId = '';
            isFavourite = false;
            $('#sortingDropdownMenuButton1').text('Sort By');
            $('.categories').removeClass('active');
            $('#isFavourite').removeClass('active');
            $('#searchFilterForm')[0].reset();
            loadRecipeList();
        }
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadRecipeList(url) {
            $("#listId").html('{{ ajaxListLoader() }}');
            url = url || "{{ route('common.loadRecipeListForUser') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    sort_by: orderBy.field,
                    sort_order: orderBy.order,
                    search: $("#searchFiledId").val(),
                    categoryIds: categoryId,
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

        /**
         * Save Favourite
         * @request form fields
         * @response object.
         */
        function addToFavourite(favourite, id) {
            var url = "{{ route('common.saveRecipeFavourite', ['id' => '%recordId%']) }}";
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
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    cosole.log(err);
                },
            });
        };
    </script>
@endsection
