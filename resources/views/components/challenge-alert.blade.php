@if(!empty($challenges) && count($challenges))
<div id="challengeCarousel" class="carousel slide  custom-dash-slider" data-bs-ride="carousel">
    <!-- Indicators -->
    <div class="carousel-indicators">
        @foreach($challenges as $index => $challenge)
            <button 
                type="button" 
                data-bs-target="#challengeCarousel" 
                data-bs-slide-to="{{ $index }}" 
                class="{{ $index == 0 ? 'active' : '' }}" 
                aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                aria-label="Slide {{ $index + 1 }}">
            </button>
        @endforeach
    </div>
    <!-- Slides -->
    <div class="carousel-inner">
        @foreach($challenges as $index => $challenge)
        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
            <div class="carousel-box fade show mb-0" role="alert">
                <div class="dashboard-alert-desc">
                    <h5>{{ $challenge->title }}</h5>
                </div>
                {!! $challenge->teaser_description !!}
                <form method="POST" id="challengeSignup-{{ $index }}">
                    @csrf
                    <input type="hidden" name="challenge_id" value="{{$challenge->id}}" />
                    <input type="hidden" name="challenge_type" value="{{$challenge->type}}" />
                    <button type="button" onclick='challengeSignup({{ $index }}, @json($challenge))' class="btn btn-secondary btn-md">Sign Up</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
<script>
const signupChallengeUrl = "{{ route('common.signupChallenge') }}";
function challengeSignup(index, challenge) {
    bootbox.dialog({
        size: 'large',
        title: challenge.title,
        centerVertical: true,
        message: challenge.description,
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary',
            },
            confirm: {
                label: 'Got It',
                className: 'btn-primary',
                callback: function() {
                    let formData = $(`#challengeSignup-${index}`).serializeArray();
                    $.ajax({
                        type: "POST",
                        url: signupChallengeUrl,
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                _toast.success(response.message);
                                setTimeout(() => window.location.reload(), 500);
                            } else {
                                _toast.error(response.message || 'Something went wrong.');
                            }
                        },
                        error: function(err) {
                            _toast.error((err.responseJSON?.message || err.responseText || 'Something went wrong.'));
                        },
                    });
                    return false; // Prevent modal from closing immediately
                }
            }
        }
    });
}

</script>
