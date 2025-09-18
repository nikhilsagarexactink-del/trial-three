@php
 $userType = userType(); 
 
@endphp

<div class="col-md-3 mainWidget_" data-id="{{ $widget_key }}" 
    @if($isCustomize)
        onmouseenter="showRemoveButton({{ json_encode($widget_key) }})"
        onmouseleave="hideRemoveButton({{ json_encode($widget_key) }})"
     @endif>
    @if($isCustomize)
        <button class="remove-widget-btn" id="remove-btn-{{ $widget_key }}" 
                onclick="removeWidget(event, {{ json_encode($widget_key) }})" 
                style="display: none;">&times;
        </button>
    @endif
        <h4 class="dash-recipe-title ">Latest Recipe</h4>
        @if (!empty($recipes) && count($recipes) > 0)
            @foreach ($recipes as $recipe)
                @php $rating = !empty($recipe['avg_ratings']) ? $recipe['avg_ratings'] : 0; @endphp
                <div class="card equal-height">
                    <!-- <div class="recipe-like">
                        <span id="isRecipeUnFavourite{{ $recipe['id'] }}"
                            style="{{ $recipe['is_my_favourite'] == 1 ? '' : 'display:none' }}">
                            <a class="fill-icon" href="javascript:void(0)"
                                onClick="addRecipeToFavourite(0, {{ $recipe['id'] }})" data-toggle="tooltip"
                                data-placement="top" title="Remove from Favorite">
                                <img class="card-img-top float-right" src="{{ url('assets/images/heart-fill.svg') }}">
                            </a>
                        </span>
                        <span id="isRecipeFavourite{{ $recipe['id'] }}"
                            style="{{ $recipe['is_my_favourite'] == 0 ? '' : 'display:none' }}">
                            <a class="outline-icon" href="javascript:void(0)"
                                onClick="addRecipeToFavourite(1, {{ $recipe['id'] }})" data-toggle="tooltip"
                                data-placement="top" title="Add to Favorite">
                                <img class="card-img-top float-right" src="{{ url('assets/images/heart.svg') }}">
                            </a>
                        </span>
                    </div> -->
                    <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.recipeDetail', ['id' => $recipe['id'], 'user_type' => $userType]) }}">
                        @if (!empty($recipe['image']) && !empty($recipe['image']->media->base_url))
                            <img class="card-img-top" src="{{ $recipe['image']->media->base_url }}"
                                alt="{{ $recipe['title'] }}">
                        @else
                            <img class="card-img-top" src="{{ url('assets/images/default-image.png') }}"
                                alt="{{ $recipe['title'] }}">
                        @endif
                    </a>
                    <div class="card-body">
                        <!-- <span class="eyebrow-text">Eyebrow Text</span> -->
                        <h5 class="card-title" data-toggle="tooltip" data-placement="top"
                            title="{{ ucfirst($recipe['title']) }}">
                            <a
                                href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.recipeDetail', ['id' => $recipe['id'], 'user_type' => $userType]) }}">{{ ucfirst($recipe['title']) }}</a>
                        </h5>
                        <div class="rating-box justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="ratings">
                                    <i class=" fa-star stars {{ $rating > 0 && $rating <= 5 ? 'fas' : 'far' }}"
                                        aria-hidden="true" id="st1" value="1"></i>
                                    <i class=" fa-star stars {{ $rating > 1 && $rating <= 5 ? 'fas' : 'far' }}"
                                        aria-hidden="true" id="st2" value="2"></i>
                                    <i class=" fa-star stars {{ $rating > 2 && $rating <= 5 ? 'fas' : 'far' }}"
                                        aria-hidden="true" id="st3" value="3"></i>
                                    <i class=" fa-star stars {{ $rating > 3 && $rating <= 5 ? 'fas' : 'far' }}"
                                        aria-hidden="true" id="st4" value="4"></i>
                                    <i class=" fa-star stars {{ $rating > 4 && $rating <= 5 ? 'fas' : 'far' }}"
                                        aria-hidden="true" id="st5" value="5"></i>
                                </div>
                                <div id="totalRatings{{ $recipe['id'] }}" class="rating-count">
                                    {{ $recipe['ratings_count'] }} <span>Ratings</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-danger" role="alert">
                Oops. No Recipe Found. Try again!
            </div>
        @endif
</div>
@section('js')
    <script>
        function getLatestTrainingRecipe() {
                $("#latestTrainingRecipe").html('<div class="text-center">{{ ajaxListLoader() }}</div>');
                url = "{{ route('common.dashboard.getLatestTrainingRecipe') }}";
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        perPage: 1
                    },
                    success: function(response) {
                        if (response.success) {
                            $("#latestTrainingRecipe").html("");
                            $('#latestTrainingRecipe').append(response.data.html);
                        }
                    },
                    error: function() {
                        _toast.error('Somthing went wrong.');
                    }
                });
            }
              function addRecipeToFavourite(favourite, id) {
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
                                $("#isRecipeUnFavourite" + id).show();
                                $("#isRecipeFavourite" + id).hide();
                            } else {
                                $("#isRecipeUnFavourite" + id).hide();
                                $("#isRecipeFavourite" + id).show();
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

            /**
             *

    </script>

@endsection