@extends('layouts.app')
<title>Recipe | Detail</title>
@section('content')
    @include('layouts.sidebar')

    @php
        $userType = userType();
        $userData = getUser();
        $categoryIds = [];
        $rating = !empty($recipe->avg_ratings) ? $recipe->avg_ratings : 0;
        if (!empty($recipe->categories)) {
            foreach ($recipe->categories as $category) {
                array_push($categoryIds, $category->category_id);
            }
        }

        function convertToHoursAndMinutes($minutes) {
            $h = floor($minutes / 60);
            $m = $minutes % 60;

            $result = '';
            if ($h > 0) $result .= "{$h} h ";
            if ($m > 0) $result .= "{$m} min";

            return trim($result);
        }

        function totalTime($prepTime, $cookTime = 0, $freezeTime = 0) {
             $totalMinutes = $prepTime + $cookTime + $freezeTime;
            $h = floor($totalMinutes / 60);
            $m = $totalMinutes % 60;

            $totalTime = '';
            if ($h > 0) $totalTime .= "{$h} hr";
            if ($m > 0) $totalTime .= "{$m} min";
            return trim($totalTime);
        }
       
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ route('user.recipeVideo', ['user_type' => $userType]) }}">Recipes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="white-bg">
            <div class="container">
                @if (empty($reward) && !empty($recipeReward))
                    <div class="alert alert-secondary earn-points" role="alert">
                        Try this recipe and earn {{ $recipeReward->point }} points
                    </div>
                @endif
                <h2 class="mb-0"><b>{{ ucfirst($recipe->title) }}</b></h2>
                <div class="row">
                    <div class="col-lg-7">
                        <ul class="recipe-slider">
                            <li>
                                <div class="recipe-detail-img">
                                    @if (!empty($recipe->image) && !empty($recipe->image->media->base_url))
                                        <img class="card-img-top" src="{{ $recipe->image->media->base_url }}"
                                            alt="{{ $recipe->title }}">
                                    @else
                                        <img class="card-img-top" src="{{ url('assets/images/default-recipe.jpg') }}"
                                            alt="{{ $recipe->title }}">
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-5">
                        <div class="recipe-time mt-lg-4">
                            <div class="recipe-time-detail">
                                <h5>Prep Time:</h5>
                                <p>
                                    {{convertToHoursAndMinutes($recipe->prep_time) }}
                                </p>
                            </div>
                            <div class="recipe-time-detail">
                                <h5>Cook Time:</h5>
                                <p>
                                    {{ $recipe->cook_time == 0 ? 'N/A' : convertToHoursAndMinutes($recipe->cook_time) }}
                                </p>
                            </div>
                            <div class="recipe-time-detail">
                                <h5>Freeze Time:</h5>
                                <p>
                                    {{ $recipe->freeze_time == 0 ? 'N/A' :convertToHoursAndMinutes($recipe->freeze_time)}}
                                </p>
                            </div>
                            <div class="recipe-time-detail">
                                <h5>Total Time:</h5>
                                <p>
                                    {{totalTime($recipe->prep_time, $recipe->cook_time, $recipe->freeze_time) }}
                                </p>
                            </div>
                            <div class="recipe-time-detail">
                                <h5>Servings:</h5>
                                <p>
                                    {{ $recipe->servings }}
                                </p>
                            </div>
                            
                            @if ($recipeReward != null)
                            <div >
                                <button {{ !empty($reward) ? 'disabled=true' : '' }} id="reviewPointsBtn"
                                    onClick="saveRewardPoint()" class="btn btn-secondary me-5">I
                                    tried this recipe</button>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ingredients-box">
                            <!-- <h2>Body:</h2> -->
                            <span>{!! $recipe->body !!}</span>
                        </div>
                        <div class="ingredients-box">
                            <h2>Ingredients:</h2>
                            <span>{!! $recipe->ingredients !!}</span>
                        </div>
                        <div class="directions-box">
                            <h2>Directions:</h2>
                            <span>{!! $recipe->directions !!}</span>
                        </div>
                        <div class="nutrition-box">
                            <h2>Nutrition Facts: <span>{!! $recipe->nutrition_facts !!}</span></h2>
                            <ul class="nutrition-list">
                                <li>
                                    <h5>{{ $recipe->calories }}</h5>
                                    <p>
                                        Calories
                                    </p>
                                </li>
                                <li>
                                    <h5>{{ $recipe->fat }}g</h5>
                                    <p>
                                        Fat
                                    </p>
                                </li>
                                <li>
                                    <h5>{{ $recipe->carbs }}g</h5>
                                    <p>
                                        Carbs
                                    </p>
                                </li>
                                <li>
                                    <h5>{{ $recipe->protein }}g</h5>
                                    <p>
                                        Protein
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if (empty($ratingReward) && !empty($rateRecipeReward))
                    <div class="alert alert-secondary earn-points" role="alert">
                        Rate this recipe and earn {{ $rateRecipeReward->point }} point's
                    </div>
                @endif
                <div class="mb-3">
                    <h2 class="mb-0"><b>Reviews</b></h2>
                    <div class="rating-star">
                        <div class="ratings">
                            <i class="fa-star {{ $rating > 0 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                                value="1"></i>
                            <i class="fa-star {{ $rating > 1 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                                value="2"></i>
                            <i class="fa-star {{ $rating > 2 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                                value="3"></i>
                            <i class="fa-star {{ $rating > 3 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                                value="4"></i>
                            <i class="fa-star {{ $rating > 4 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                                value="5"></i>
                        </div>
                        <span id="totalRatings{{ $recipe->id }}">{{ $recipe->ratings_count }} Ratings</span>
                    </div>
                </div>
                <div class="card review-card">
                    <div class="card-body">
                        <form id="reviewForm" class="form-head" method="POST" novalidate autocomplete="false"
                            action="{{ route('common.saveRecipeRating', ['id' => $recipe->id]) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group star-rating">
                                                <label>Your Rating</label>
                                                <div class="ratings">
                                                    <i class="far fa-star stars" aria-hidden="true" id="st1"
                                                        value="1"></i>
                                                    <i class="far fa-star stars" aria-hidden="true" id="st2"
                                                        value="2"></i>
                                                    <i class="far fa-star stars" aria-hidden="true" id="st3"
                                                        value="3"></i>
                                                    <i class="far fa-star stars" aria-hidden="true" id="st4"
                                                        value="4"></i>
                                                    <i class="far fa-star stars" aria-hidden="true" id="st5"
                                                        value="5"></i>
                                                </div>
                                                <input type="hidden" name="rating" id="ratingFieldId" value="0">
                                                <input type="hidden" name="module_id" value="{{ $recipe->id }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Review<span class="text-danger">*</span></label>
                                                <textarea class="form-control" placeholder="Review" name="review"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="btn_row text-center">
                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120"
                                    id="reviewBtn" onClick="saveReview()">Submit<span id="reviewBtnLoader"
                                        class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                                    href="{{ route('user.recipeVideo', ['user_type' => $userType]) }}">Cancel</a>
                            </div>
                        </form>
                        <div>

                        </div>
                    </div>
                </div>
                <ul class="list-group cm-review-list" id="reviewList">
                </ul>

                <section class="content">
                    <h2 class="mb-0"><b>More Like This</b></h2>
                    <!-- <div class="card"> -->
                    <div class="card-body recipe-list-sec px-0">
                        <div class="row" id="listId"></div>
                    </div>
                    <!-- </div> -->
                </section>
            </div>
        </div>
        @if(!empty($recipeReward) && !empty($recipeReward->reward_game) && $recipeReward->is_gamification == 1)
            <x-game-modal :rewardDetail="$recipeReward" :module="'recipes'" :module-id="$recipe->id" />
        @endif
        @if(!empty($rateRecipeReward) && !empty($rateRecipeReward->reward_game) && $rateRecipeReward->is_gamification == 1)
            <x-game-modal :rewardDetail="$rateRecipeReward" :module="'recipes'" :module-id="$recipe->id" />
        @endif

    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\UserRecipeReviewRequest', '#reviewForm') !!}
    <script>
        let categoryIds = @json($categoryIds);
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadSameRecipesList(url, categoryId = "") {
            $("#listId").html('{{ ajaxListLoader() }}');
            url = url || "{{ route('common.loadRecipeListForUser') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    //sort_by: orderBy.field,
                    // sort_order: orderBy.order,
                    //search: $("#searchFiledId").val()
                    perPage: 3,
                    categoryIds: categoryIds.toString()
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
                    _toast.error('Somthing went wrong.');
                }
            });
        }

        /**
         * Load review list.
         * @request search, status
         * @response object.
         */
        function loadReviewList(currentPage = 1, url) {
            // $("#listId").html('{{ ajaxListLoader() }}');
            $('#loadMoreBtn').prop('disabled', true);
            $('#loadMoreBtnLoader').show();
            url = url || "{{ route('common.loadRecipeReviewList', ['id' => $recipe->id]) }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {},
                success: function(response) {
                    $('#loadMorePage' + currentPage).hide();
                    $('#loadMoreBtn').prop('disabled', false);
                    $('#loadMoreBtnLoader').hide();
                    if (response.success) {
                        $('#reviewList').append(response.data.html);
                    }
                },
                error: function() {
                    $('#loadMoreBtn').prop('disabled', false);
                    $('#loadMoreBtnLoader').hide();
                    _toast.error('Something went wrong.');
                }
            });
        }
        /**
         * Save Review
         * @request form fields
         * @response object.
         */
        function saveReview() {
            var formData = $("#reviewForm").serializeArray();
            const rewardData = @json($rateRecipeReward ?? null);
            if ($('#reviewForm').valid()) {
                $('#reviewBtn').prop('disabled', true);
                $('#reviewBtnLoader').show();
                let url = "{{ route('common.saveRecipeRating', ['id' => $recipe->id]) }}";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#reviewBtn').prop('disabled', false);
                        $('#reviewBtnLoader').hide();
                        if (response.success) {
                            let data = response.data;
                            _toast.customSuccess(response.message);
                            $('#reviewForm')[0].reset();
                            setTimeout(function() {
                                if(rewardData && rewardData.is_gamification == 1 && rewardData.reward_game){
                                    const userId = @json($userData->id);
                                    const rewardId = rewardData.id;
                                    const modalId = '#gameModal_' + userId + '_' + rewardId;
                                    $(modalId).modal('show'); // updated here
                                }else{
                                    window.location.reload();
                                }
                            }, 3000);
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#reviewBtn').prop('disabled', false);
                        $('#reviewBtnLoader').hide();
                        _toast.error('Something went wrong. please try again');
                    },
                });
            }
        };

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

        function saveRewardPoint() {
            $('#reviewPointsBtn').prop('disabled', true);
            if(@json($recipeReward)&&@json($recipeReward->is_gamification == 1)){
                const rewardData = @json($recipeReward);
                const userId = @json($userData->id);
                const rewardId = rewardData.id;
                const modalId = '#gameModal_' + userId + '_' + rewardId;
                $(modalId).modal('show'); // updated here

            }else{
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveRewardPoint') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        feature_key: "use-recipe",
                        module_id: "{{ $recipe->id }}"
                    },
                    success: function(response) {
                        $('#reviewPointsBtn').prop('disabled', false);
                        if (response.success) {
                            _toast.customSuccess(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 3000)
                        } else {
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#reviewPointsBtn').prop('disabled', false);
                        _toast.error('Please try again.');
                    },
                });
            }
            
        }
        $(".stars").click(function(evnt) {
            // $(".fa-star").css("color", "black");
            // $(".fa-star").removeClass("fas");
            // $(".fa-star").addClass("far");
            let selectedStarVal = $(this).attr('value');
            $("#ratingFieldId").val(selectedStarVal);
            //console.log($(this).attr('id'));
            $(".stars").each(function() {
                let starVal = $(this).attr('value')
                if (selectedStarVal >= starVal) {
                    //console.log(starVal);
                    // $("#st" + starVal).css("color", "yellow");
                    // $("#st" + starVal).css("color", "#F68922");
                    $("#st" + starVal).addClass('fas');
                    $("#st" + starVal).removeClass('far');
                }

            });
        });
        loadSameRecipesList();
        loadReviewList();
    </script>
@endsection
