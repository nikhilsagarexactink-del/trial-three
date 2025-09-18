@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $video)
        @php
            $rating = !empty($video->avg_ratings) ? $video->avg_ratings : 0;
            if (!empty($video->favourite)) {
                $isFavourite = true;
            } else {
                $isFavourite = false;
            }
        @endphp
        <div class="col-md-4">
            <div class="card">
                <div class="recipe-like">
                    <span id="isUnFavourite{{ $video->id }}"
                        style="{{ $video->is_my_favourite == 1 ? '' : 'display:none' }}">
                        <a class="fill-icon" href="javascript:void(0)" onClick="addToFavourite(0, {{ $video->id }})"
                            data-toggle="tooltip" data-placement="top" title="Remove from Favorite video">
                            <img class="card-img-top float-right" src="{{ url('assets/images/heart-fill.svg') }}">
                        </a>
                    </span>
                    <span id="isFavourite{{ $video->id }}"
                        style="{{ $video->is_my_favourite == 0 ? '' : 'display:none' }}">
                        <a class="outline-icon" href="javascript:void(0)"
                            onClick="addToFavourite(1, {{ $video->id }})" data-toggle="tooltip" data-placement="top"
                            title="Make this Video a Favorite">
                            <img class="card-img-top float-right" src="{{ url('assets/images/heart.svg') }}">
                        </a>
                    </span>
                </div>
                <a href="{{ route('user.loadTrainingDetailList', ['id' => $video->id, 'user_type' => $userType]) }}">
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
                    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
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
                            <div id="totalRatings{{ $video->id }}" class="rating-count">{{ $video->ratings_count }}
                                <span>Ratings</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php $i++; @endphp
    @endforeach
@else
    <div class="alert alert-danger" role="alert">
        Oops. No Videos Found. Try again!
    </div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadTrainingList(pageLink);
            }
        });
        $(".stars").click(function(evnt) {
            $(".fa-star").css("color", "black");
            let selectedStarVal = $(this).attr('value')
            //console.log($(this).attr('id'));
            $(".stars").each(function() {
                let starVal = $(this).attr('value')
                if (selectedStarVal >= starVal) {
                    //console.log(starVal);
                    $("#st" + starVal).css("color", "#F68922");
                }

            });
        });
        $('[data-toggle="tooltip"]').tooltip();

    });
    // function playVideo(url){
    //     var lightbox = lity();
    //     lightbox(url);
    // }
</script>
