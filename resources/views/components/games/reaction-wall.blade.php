@php
    $userData = getUser();
    $uniqueId = 'reactionWallGame_' . $userData->id . '_' . $rewardDetail->id;
@endphp

<div class="reaction-wall-game px-3" id="{{ $uniqueId }}">
    <div class="align-items-center mb-3">
        @if (!empty($game->title))
            <h5 class="mb-0">{{ $game->title }}</h5>
        @endif

        @if (!empty($game->description))
            <div class="description text-muted small mt-2">{{ $game->description }}</div>
        @endif
    </div>

    <div class="info d-flex justify-content-between mb-3" style="max-width: 400px; margin: auto;">
        <div class="time">Time: <span id="timeLeft_{{ $uniqueId }}">{{ $rewardDetail->reward_game->duration }}</span>s</div>
        <div class="score">Score: <span id="scoreCount_{{ $uniqueId }}">0</span></div>
    </div>

    <div class="wall position-relative">
        @for ($i = 1; $i <= 100; $i++)
            <div class="dot" id="{{ $uniqueId }}_dot-{{ $i }}"></div>
        @endfor
    </div>

    <div class="start text-center mt-3">
        <button class="btn btn-danger" id="startGameBtn_{{ $uniqueId }}">Start game!</button>
    </div>

    <div class="end mt-3">
        <h4 class="mb-2">Game Over!</h4>
        <div>Your Score: <span id="finalScore_{{ $uniqueId }}">0</span></div>
    </div>
</div>

<style scoped>
    #{{ $uniqueId }} .dot {
        display: none;
        width: 30px;
        height: 30px;
        background-color: #dcdcdc;
        border-radius: 50%;
        cursor: pointer;
    }

    #{{ $uniqueId }} .wall {
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        gap: 5px;
        max-width: 400px;
        margin: 20px auto;
    }

    #{{ $uniqueId }} .dot.active {
        background-color: #ff5722;
    }

    #{{ $uniqueId }} .end {
        display: none;
    }

    #{{ $uniqueId }} .description {
        max-width: 500px;
        margin: 0 auto;
    }
</style>

<script>
    (() => {
        const uniqueId = "{{ $uniqueId }}";
        const wrapper = document.getElementById(uniqueId);
        if (!wrapper) return;

        let currentDot = null;
        let score = 0;
        let timeLeft = @json($rewardDetail->reward_game->duration);
        let timerInterval;

        const scoreCount = wrapper.querySelector("#scoreCount_" + uniqueId);
        const timeLeftEl = wrapper.querySelector("#timeLeft_" + uniqueId);
        const finalScore = wrapper.querySelector("#finalScore_" + uniqueId);
        const startButton = wrapper.querySelector("#startGameBtn_" + uniqueId);
        const description = wrapper.querySelector(".description");
        const infoSection = wrapper.querySelector(".info");
        const endSection = wrapper.querySelector(".end");
        const dots = wrapper.querySelectorAll(".dot");
        const modalId = "{{ $modalId }}"; 

        startButton.addEventListener("click", function startGame() {
            startButton.parentElement.style.display = 'none';
            endSection.style.display = 'none';
            if (description) description.style.display = 'none';

            dots.forEach(dot => dot.style.display = 'block');

            score = 0;
            timeLeft = @json($rewardDetail->reward_game->duration);
            scoreCount.textContent = score;
            timeLeftEl.textContent = timeLeft;

            activateRandomDot();

            timerInterval = setInterval(() => {
                timeLeft--;
                timeLeftEl.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    endGame();
                }
            }, 1000);
        });

        function endGame() {
            if (currentDot) {
                currentDot.classList.remove('active');
                currentDot.removeEventListener('click', handleDotClick);
            }

            dots.forEach(dot => dot.style.display = 'none');
            infoSection.style.display = 'none';
            endSection.style.display = 'block';
            finalScore.textContent = score;
            if (description) description.style.display = 'block';

            if (score) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveRewardPoint') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        feature_key: @json($rewardDetail->feature_key),
                        module_id: @json($moduleId ?? null),
                        game_key: 'reaction-wall',
                        module_key: @json($module),
                        video_id: @json($videoId ?? null),
                        score: score,
                        athlete_id: @json($athleteId ?? null),
                    },
                    success: function (response) {
                        $('#reviewPointsBtn').prop('disabled', false);
                        if (response.success) {
                             document.getElementById('closeBtn_' + modalId).style.display = 'none';
                            _toast.customSuccess(response.message);
                            setTimeout(function () {
                                window.location.reload();
                            }, 3000);
                        } else {
                            _toast.error('Something went wrong. Please try again');
                        }
                    },
                    error: function () {
                        $('#reviewPointsBtn').prop('disabled', false);
                        _toast.error('Please try again.');
                    },
                });
            }
        }

        function activateRandomDot() {
            if (currentDot) {
                currentDot.classList.remove('active');
                currentDot.removeEventListener('click', handleDotClick);
            }

            const index = Math.floor(Math.random() * dots.length);
            currentDot = dots[index];
            currentDot.classList.add('active');
            currentDot.addEventListener('click', handleDotClick);
        }

        function handleDotClick() {
            score++;
            scoreCount.textContent = score;
            activateRandomDot();
        }
    })();
</script>
