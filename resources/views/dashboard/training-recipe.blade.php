@php $userType = userType();
@endphp
<div class="col-md-6 {{!empty($allowedWidgets) && in_array('my-workouts',$allowedWidgets)?'':'hide_widget'}}">
    <div>
        <h4 class="dash-recipe-title">Latest training video</h4>
        @if (!empty($trainings) && count($trainings) > 0)
            @foreach ($trainings as $video)
                @php $rating = !empty($video->avg_ratings) ? $video->avg_ratings : 0; @endphp
                <div class="card">
                    <div class="recipe-like">
                        <span id="isVideoUnFavourite{{ $video->id }}"
                            style="{{ $video->is_my_favourite == 1 ? '' : 'display:none' }}">
                            <a class="fill-icon" href="javascript:void(0)"
                                onClick="addVideoToFavourite(0, {{ $video->id }})" data-toggle="tooltip"
                                data-placement="top" title="Remove from Favorite video">
                                <img class="card-img-top float-right" src="{{ url('assets/images/heart-fill.svg') }}">
                            </a>
                        </span>
                        <span id="isVideoFavourite{{ $video->id }}"
                            style="{{ $video->is_my_favourite == 0 ? '' : 'display:none' }}">
                            <a class="outline-icon" href="javascript:void(0)"
                                onClick="addVideoToFavourite(1, {{ $video->id }})" data-toggle="tooltip"
                                data-placement="top" title="Make this Video a Favorite">
                                <img class="card-img-top float-right" src="{{ url('assets/images/heart.svg') }}">
                            </a>
                        </span>
                    </div>
                    <a
                        href="{{ route('user.loadTrainingDetailList', ['id' => $video->id, 'user_type' => $userType]) }}">
                        <!-- data-lity -->
                        @if (!empty($video->media) && !empty($video->media->base_url))
                            <img class="card-img-top" src="{{ $video->media->base_url }}" alt="{{ $video->title }}">
                        @else
                            <img class="card-img-top" src="{{ url('assets/images/default-image.png') }}"
                                alt="{{ $video->title }}">
                        @endif
                    </a>

                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('user.loadTrainingDetailList', ['id' => $video->id, 'user_type' => $userType]) }}"
                                data-toggle="tooltip" data-placement="top" title="{{ $video->title }}">
                                {{ ucfirst($video->title) }}
                            </a>
                        </h5>
                        <div class="rating-box justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="ratings">
                                    <i class="far fa-star stars {{ $rating > 0 && $rating <= 5 ? 'fas' : '' }}"
                                        aria-hidden="true" id="st1" value="1"></i>
                                    <i class="far fa-star stars {{ $rating > 1 && $rating <= 5 ? 'fas' : '' }}"
                                        aria-hidden="true" id="st2" value="2"></i>
                                    <i class="far fa-star stars {{ $rating > 2 && $rating <= 5 ? 'fas' : '' }}"
                                        aria-hidden="true" id="st3" value="3"></i>
                                    <i class="far fa-star stars {{ $rating > 3 && $rating <= 5 ? 'fas' : '' }}"
                                        aria-hidden="true" id="st4" value="4"></i>
                                    <i class="far fa-star stars {{ $rating > 4 && $rating <= 5 ? 'fas' : '' }}"
                                        aria-hidden="true" id="st5" value="5"></i>
                                </div>
                                <div id="totalRatings{{ $video->id }}" class="rating-count">
                                    {{ $video->ratings_count }} <span>Ratings</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-danger" role="alert">
                Oops. No Videos Found. Try again!
            </div>
        @endif
    </div>
</div>
<div class="col-md-6 {{ !empty($allowedWidgets) && in_array('new-recipes',$allowedWidgets) ? '' :'hide_widget'}}">
    <div class="">
        <h4 class="dash-recipe-title">Latest Recipe</h4>
        @if (!empty($recipes) && count($recipes) > 0)
            @foreach ($recipes as $recipe)
                @php $rating = !empty($recipe->avg_ratings) ? $recipe->avg_ratings : 0; @endphp
                <div class="card  equal-height">
                    <div class="recipe-like">
                        <span id="isRecipeUnFavourite{{ $recipe->id }}"
                            style="{{ $recipe->is_my_favourite == 1 ? '' : 'display:none' }}">
                            <a class="fill-icon" href="javascript:void(0)"
                                onClick="addRecipeToFavourite(0, {{ $recipe->id }})" data-toggle="tooltip"
                                data-placement="top" title="Remove from Favorite">
                                <img class="card-img-top float-right" src="{{ url('assets/images/heart-fill.svg') }}">
                            </a>
                        </span>
                        <span id="isRecipeFavourite{{ $recipe->id }}"
                            style="{{ $recipe->is_my_favourite == 0 ? '' : 'display:none' }}">
                            <a class="outline-icon" href="javascript:void(0)"
                                onClick="addRecipeToFavourite(1, {{ $recipe->id }})" data-toggle="tooltip"
                                data-placement="top" title="Add to Favorite">
                                <img class="card-img-top float-right" src="{{ url('assets/images/heart.svg') }}">
                            </a>
                        </span>
                    </div>
                    <a href="{{ route('user.recipeDetail', ['id' => $recipe->id, 'user_type' => $userType]) }}">
                        @if (!empty($recipe->image) && !empty($recipe->image->media->base_url))
                            <img class="card-img-top" src="{{ $recipe->image->media->base_url }}"
                                alt="{{ $recipe->title }}">
                        @else
                            <img class="card-img-top" src="{{ url('assets/images/default-image.png') }}"
                                alt="{{ $recipe->title }}">
                        @endif
                    </a>
                    <div class="card-body">
                        <!-- <span class="eyebrow-text">Eyebrow Text</span> -->
                        <h5 class="card-title" data-toggle="tooltip" data-placement="top"
                            title="{{ ucfirst($recipe->title) }}">
                            <a
                                href="{{ route('user.recipeDetail', ['id' => $recipe->id, 'user_type' => $userType]) }}">{{ ucfirst($recipe->title) }}</a>
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
                                <div id="totalRatings{{ $recipe->id }}" class="rating-count">
                                    {{ $recipe->ratings_count }} <span>Ratings</span></div>
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
</div>
