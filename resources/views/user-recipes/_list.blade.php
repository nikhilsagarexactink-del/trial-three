@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $recipe)
        @php
            $rating = !empty($recipe->avg_ratings) ? $recipe->avg_ratings : 0;
        @endphp
        <div class="col-md-4">
            <div class="card">
                <div class="recipe-like">
                    <span id="isUnFavourite{{ $recipe->id }}"
                        style="{{ $recipe->is_my_favourite == 1 ? '' : 'display:none' }}">
                        <a class="fill-icon" href="javascript:void(0)" onClick="addToFavourite(0, {{ $recipe->id }})"
                            data-toggle="tooltip" data-placement="top" title="Remove from Favorite">
                            <img class="card-img-top float-right" src="{{ url('assets/images/heart-fill.svg') }}">
                        </a>
                    </span>
                    <span id="isFavourite{{ $recipe->id }}"
                        style="{{ $recipe->is_my_favourite == 0 ? '' : 'display:none' }}">
                        <a class="outline-icon" href="javascript:void(0)"
                            onClick="addToFavourite(1, {{ $recipe->id }})" data-toggle="tooltip" data-placement="top"
                            title="Add to Favorite">
                            <img class="card-img-top float-right" src="{{ url('assets/images/heart.svg') }}">
                        </a>
                    </span>
                </div>
                <a href="{{ route('user.recipeDetail', ['id' => $recipe->id, 'user_type' => $userType]) }}">
                    @if (!empty($recipe->image) && !empty($recipe->image->media->base_url))
                        <img class="card-img-top" src="{{ $recipe->image->media->base_url }}"
                            alt="{{ $recipe->title }}">
                    @else
                        <img class="card-img-top" src="{{ url('assets/images/default-recipe.jpg') }}"
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
        </div>
        @php $i++; @endphp
    @endforeach
@else
    <div class="alert alert-danger" role="alert">
        Oops. No Recipe Found. Try again!
    </div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadRecipeList(pageLink);
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
