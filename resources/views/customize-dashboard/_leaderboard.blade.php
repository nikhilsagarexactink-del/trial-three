@php
$userData = getUser();
@endphp
@if(!empty($challenges) && count($challenges) > 0)
<div id="leaderboardCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
    <div class="carousel-indicators mb-2">
        @foreach ($challenges as $index => $challenge)
            <button type="button" data-bs-target="#leaderboardCarousel"
                data-bs-slide-to="{{ $index }}"
                class="{{ $index === 0 ? 'active' : '' }}"
                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                aria-label="Slide {{ $index + 1 }}">
            </button>
        @endforeach
    </div>
    <div class="carousel-inner">
        @foreach ($challenges as $index => $challenge)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <div class="leaderboard-card h-100 shadow mx-auto">
                <div class="leaderboard-header">
                    <img class="leaderboard-image" src={{asset('assets/images/leaderboard.png')}} >
                    <div>
                        <h5 class="challenge-title">{{ $challenge['title'] }}</h5>
                        <div class="small text-muted">Duration: {{ $challenge['leaderboard']['challenge_days'] }} days</div>
                    </div>
                </div>

                <div class="p-3">
                    <table class="table leaderboard-table table-sm table-borderless mb-0">
                    <thead>
                        <tr>
                        <th style="width: 50px;">#</th>
                        <th>Participant</th>
                        <th class="text-end">Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($challenge['leaderboard']['top_users']) > 0)
                            @foreach ($challenge['leaderboard']['top_users'] as $i => $user)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                <td class="text-end">{{ $user->completed_days }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center text-muted">There are no active participants.</td>
                            </tr>
                        @endif
                    </tbody>
                    </table>
                </div>

                @if ($challenge['leaderboard']['active_user'])
                    <div class="active-user-box text-center">
                    You: <strong>{{ $challenge['leaderboard']['active_user']->first_name }}</strong> —
                    {{ $challenge['leaderboard']['active_user']->completed_days }} day(s)
                    </div>
                @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
<div class="no-challenge-text">
    <h1 class="mt-5 text-muted">🚫 No Live Challenges Right Now</h1>
    <p class="text-secondary ms-5">Please check back later for new opportunities to compete!</p>
</div>
@endif
