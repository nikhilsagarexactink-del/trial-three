@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    <ul class="popup-video-list">
        @foreach ($data as $video)
            <li>
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
                                <!-- <img class="card-img-top float-right" src="{{ url('assets/images/heart.svg') }}"> -->
                            </a>
                        </span>
                    </div>
                    <a data-lity href="{{$video->video_url}}">
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
                            <a  data-lity href="{{$video->video_url}}" data-toggle="tooltip" data-placement="top" title="{{ $video->title }}">
                                {{ ucfirst($video->title) }}
                            </a>
                        </h5>
                    </div>
                </div>
            </li>
            @php $i++; @endphp
        @endforeach
    </ul>
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
                loadTrainingVideo(pageLink);
            }
        });
    });
</script>

