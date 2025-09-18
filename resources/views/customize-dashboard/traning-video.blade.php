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
        <h4 class="dash-recipe-title">Latest training video</h4>
        @if (!empty($trainings)&& count($trainings) >= 0)
            @foreach ($trainings as $video)
                @php $rating = !empty($video['avg_ratings']) ? $video['avg_ratings'] : 0; @endphp
                <div class="card equal-height">
                    <!-- <div class="recipe-like">
                        <span id="isVideoUnFavourite{{ $video['id'] }}"
                            style="{{ $video['is_my_favourite'] == 1 ? '' : 'display:none' }}">
                            <a class="fill-icon" href="javascript:void(0)"
                                onClick="addVideoToFavourite(0, {{ $video['id'] }})" data-toggle="tooltip"
                                data-placement="top" title="Remove from Favorite video">
                                <img class="card-img-top float-right" src="{{ url('assets/images/heart-fill.svg') }}">
                            </a>
                        </span>
                        <span id="isVideoFavourite{{ $video['id'] }}"
                            style="{{ $video['is_my_favourite'] == 0 ? '' : 'display:none' }}">
                            <a class="outline-icon" href="javascript:void(0)"
                                onClick="addVideoToFavourite(1, {{ $video['id'] }})" data-toggle="tooltip"
                                data-placement="top" title="Make this Video a Favorite">
                                <img class="card-img-top float-right" src="{{ url('assets/images/heart.svg') }}">
                            </a>
                        </span>
                    </div> -->
                    <a
                        href="{{ route('user.loadTrainingDetailList', ['id' => $video['id'], 'user_type' => $userType]) }}">
                        <!-- data-lity -->
                        @if (!empty($video['media']) && !empty($video['media']->base_url))
                            <img class="card-img-top" src="{{ $video['media']->base_url }}" alt="{{ $video['title'] }}">
                        @else
                            <img class="card-img-top" src="{{ url('assets/images/default-image.png') }}"
                                alt="{{ $video['title'] }}">
                        @endif
                    </a>

                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.loadTrainingDetailList', ['user_type' => $userType]) }}"
                            
                                data-toggle="tooltip" data-placement="top" title="{{ $video['title'] }}">
                                {{ ucfirst($video['title']) }}
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
                                <div id="totalRatings{{ $video['id'] }}" class="rating-count">
                                    {{ $video['ratings_count'] }} <span>Ratings</span></div>
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
@section('js')
@endsection