@extends('layouts.app')
<title>Training</title>
@section('content')
    @include('layouts.sidebar')

    @php
        $userType = userType();
        $userData = getUser();
        $rating = !empty($video->avg_ratings) ? $video->avg_ratings : 0;
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.trainingVideo', ['user_type' => $userType]) }}">Training Videos</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('user.trainingVideo', ['category' => !empty($video->category) ? $video->category->id : '', 'user_type' => $userType]) }}">{{ !empty($video->category) ? ucfirst($video->category->name) : 'Detail' }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($video->title) }}</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title End -->
            </div>
        </div>
        <div class="white-bg">
            <div class="container">
            <div>
            <h2>{{ ucfirst($video->title) }}</h2>
            <div class="recipe-detail-img recipe-detail-video">
                @if (!empty($video->video_url))
                    <?php
                        $embed_url = $video->video_url;
                        // Check if URL is from YouTube
                        if (strpos($video->video_url, 'youtube.com') !== false) {
                            $embed_url = str_replace('watch?v=', 'embed/', $video->video_url);
                        } 
                        // Check if URL is from Vimeo
                        elseif (strpos($video->video_url, 'vimeo.com') !== false) {
                            $embed_url = str_replace('vimeo.com/', 'player.vimeo.com/video/', $video->video_url);
                        }
                    ?>
                    <iframe id="videoIframe" src="{{ $embed_url }}" width="100%" height="400" frameborder="0" allowfullscreen></iframe>
                        @elseif(!empty($video->media) && !empty($video->media->base_url))
                            <img class="card-img-top" src="{{ $video->media->base_url }}" alt="{{ $video->title }}">
                        @else
                            <img class="card-img-top" style="width:200px;height:200px" src="{{ url('assets/images/default-image.png') }}" alt="{{ $video->title }}">
                        @endif
            </div>
            @if(!empty($video) && $video->provider_type == 'vimeo')
                <span class="video-progress">You watched <span id="watchedProgress"></span>  % of this video.</span>
            @endif
        </div>
        <div class="mb-3">
            <h2 class="mb-0"><b>Reviews</b></h2>
            <div class="rating-star">
                <div class="ratings">
                    <i class=" fa-star {{ $rating > 0 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                        value="1"></i>
                    <i class=" fa-star {{ $rating > 1 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                        value="2"></i>
                    <i class=" fa-star {{ $rating > 2 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                        value="3"></i>
                    <i class=" fa-star {{ $rating > 3 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                        value="4"></i>
                    <i class=" fa-star {{ $rating > 4 && $rating <= 5 ? 'fas' : 'far' }}" aria-hidden="true"
                        value="5"></i>
                </div>
                <span id="totalRatings{{ $video->id }}">{{ $video->ratings_count }} Ratings</span>
            </div>
        </div>
        <div class="card review-card mw-100">
            <div class="card-body">
                <form id="reviewForm" class="form-head" method="POST" novalidate autocomplete="false"
                    action="{{ route('common.saveTrainingVideoRating', ['id' => $video->id]) }}">
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
                        <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="reviewBtn"
                            onClick="saveReview()">Submit<span id="reviewBtnLoader"
                                class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                        <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                            href="{{ route('user.trainingVideo', ['user_type' => $userType]) }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        <div>
            <ul class="list-group cm-review-list mw-100" id="reviewList">
                <ul>
        </div>
        <section class="content">
            <h2 class="mb-0"><b>More Like This</b></h2>
            <!-- <div class="card"> -->
            <div class="card-body  recipe-list-sec px-0">
                <div class="row" id="listId"></div>
            </div>
            <!-- </div> -->
        </section>
        </div>
        </div>
        @if(!empty($videoReward) && !empty($videoReward->reward_game) && $videoReward->is_gamification == 1)
            <x-game-modal :rewardDetail="$videoReward" :video-id="$video->id" :module="'trainning-library'" />
        @endif                  
        @if(!empty($ratingReward) && !empty($ratingReward->reward_game) && $ratingReward->is_gamification == 1)
            <x-game-modal :rewardDetail="$ratingReward" :video-id="$video->id" :module="'trainning-library'" />
        @endif
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\UserRecipeReviewRequest', '#reviewForm') !!}
    <script src="https://player.vimeo.com/api/player.js"></script>
    <script>
        const iframe = document.querySelector('iframe[src*="vimeo"]');
        const player = new Vimeo.Player(iframe);

        let watchedSegments = [];
        let totalWatchedTime = 0;
        let videoDuration = 0;
        let fromSkipTime = 0;

        // Fetch video duration
        player.getDuration().then(duration => {
            videoDuration = duration;
        });
        player.on('play', function () {
        });

        player.on('timeupdate', function (data) {
            const currentTime = data.seconds;
            totalWatchedTime = data.seconds;
        });

        player.on('seeked', function (data) {
            fromSkipTime = data.seconds;
        });

        // Final check when video ends
        player.on('ended', function () {
            const finalWatchedTime = videoDuration - fromSkipTime;
            const watchedPercentage = (finalWatchedTime / videoDuration) * 100;
            saveVideoProgress(watchedPercentage.toFixed(2));
        });

        /**
         * Save Video Progress
         * @request form fields
         * @response object.
        */
        function saveVideoProgress(completionPercentage) {
            const rewardData = @json($videoReward ?? null);
            var training_video_id = <?php echo (!empty($video)) ? $video->id : '';?>;
            $.ajax({
                url: "{{route('common.saveVideoProgress')}}",
                type: 'POST',
                data: {
                    videoId: training_video_id,
                    completionPercentage: completionPercentage
                },
                success: function(response) {
                    if (response.success && response.message != "") {
                        var data = response.data.data;
                        $("#watchedProgress").text(completionPercentage);
                        _toast.customSuccess(response.message);
                        console.log("getting completion percentage",parseInt(completionPercentage));
                        setTimeout(function() {
                            if(rewardData && rewardData.is_gamification == 1 && rewardData.reward_game &&  parseInt(completionPercentage) > 95 && data.is_redeem == 0){
                                console.log("getting completion percentage",data);
                                const userId = @json($userData->id);
                                const rewardId = rewardData.id;
                                const modalId = '#gameModal_' + userId + '_' + rewardId;
                                $(modalId).modal('show'); // updated here
                            }
                            else{
                                window.location.reload();
                            }
                        }, 500);
                    }
                },
                error: function() {
                }
            });
        }

        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadSameVideoList(url, categoryId = "") {
            $("#listId").html('{{ ajaxListLoader() }}');
            url = url || "{{ route('common.loadTrainingVideoListForUser') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    categoryIds: "{{ $video->training_video_category_id }}",
                    perPage: 3
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
            url = url || "{{ route('common.loadTrainingReviewList', ['id' => $video->id]) }}";
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
                    _toast.error('Somthing went wrong.');
                }
            });
        }
        /**
         * Save Review
         * @request form fields
         * @response object.
         */
        function saveReview() {
            const rewardData = @json($ratingReward ?? null);
            var formData = $("#reviewForm").serializeArray();
            if ($('#reviewForm').valid()) {
                $('#reviewBtn').prop('disabled', true);
                $('#reviewBtnLoader').show();
                let url = "{{ route('common.saveTrainingVideoRating', ['id' => $video->id]) }}";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#reviewBtn').prop('disabled', false);
                        $('#reviewBtnLoader').hide();
                        if (response.success) {
                            let data = response.data;
                            _toast.success(response.message);
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
                            }, 500);

                        } else {
                            _toast.error('Somthing went wrong. please try again');
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
                    cosole.log(err);
                },
            });
        };
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
                    $("#st" + starVal).addClass('fas');
                    $("#st" + starVal).removeClass('far');
                }

            });
        });
        
        // $("#videoIframe").on("finish", function(){
        //     console.log( "Video is done playing" );
        // });
        // $("#videoIframe").on("play", function(){
        //     console.log( "Video is playing" );
        // });
        // $("#videoIframe").on("pause", function(evnt, data){
        //     console.log( "Video is paused", data);
        //     console.log(evnt);
        // });
        // $("#videoIframe").on("loadProgress", function(event, data) {
        //     console.log("===========Load Progress===============", data);
        // });
        $(document).ready(function() {
            var videoStats = <?php echo !empty($videoProgress) ? json_encode($videoProgress['completion_percentage']) : 0; ?>;
            $("#watchedProgress").text(videoStats);
            loadReviewList();
            loadSameVideoList();
        });
    </script>
@endsection
