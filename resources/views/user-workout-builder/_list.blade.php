@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $workout)
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('user.viewWorkout', ['id' => $workout->id, 'user_type' => $userType]) }}">
                    <!-- data-lity -->
                    @if (!empty($workout->media) && !empty($workout->media->base_url))
                        <img class="card-img-top" src="{{ $workout->media->base_url }}" alt="{{ $workout->name }}">
                    @else
                        <img class="card-img-top" src="{{ url('assets/images/default-workout-image.jpg') }}"
                            alt="{{ $workout->name }}">
                    @endif
                </a>
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{{ route('user.viewWorkout', ['id' => $workout->id, 'user_type' => $userType]) }}"
                            data-toggle="tooltip" data-placement="top" title="{{ $workout->name }}">
                            {{ ucfirst($workout->name) }}
                        </a>
                    </h5>
                    {{truncateWords($workout->description, 20)}}
                </div>
            </div>
        </div>
        @php $i++; @endphp
    @endforeach
@else
    <div class="alert alert-danger" role="alert">
        Oops! You don't have any workouts!
    </div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadUserWorkouts(pageLink);
            }
        });
    });
    // function playVideo(url){
    //     var lightbox = lity();
    //     lightbox(url);
    // }
</script>
