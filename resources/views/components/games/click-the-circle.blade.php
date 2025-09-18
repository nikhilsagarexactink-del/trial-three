@php
    $userData = getUser();
    $uniqueId = 'clickCircleGame_' . $userData->id . '_' . $rewardDetail->id;
@endphp

<div class="click-the-circle-game-component" id="{{ $uniqueId }}">
    <div class="align-items-center mb-2">
        <h5 class="mb-0">{{ $game->title ?? '' }}</h5>
        <div class="mb-2 description">
            {{ $game->description ?? '' }}
        </div>

        <div class="d-flex justify-content-between mb-2">
            <div>Score: <strong id="clickGameScore_{{ $uniqueId }}">0</strong></div>
            <div>Time Left: <strong id="clickGameTimer_{{ $uniqueId }}">{{ $rewardDetail->reward_game ? $rewardDetail->reward_game->duration : 0 }}</strong>s</div>
        </div>

        <div class="text-center mb-3">
            <button id="startClickGame_{{ $uniqueId }}" class="btn btn-secondary">Start Game</button>
        </div>
    </div>

    <div class="click-the-circle-area border rounded position-relative">
        <div class="click-the-circle-target" id="clickTheCircleTarget_{{ $uniqueId }}"></div>
    </div>

    <div class="mt-3 text-center fw-bold text-secondary" id="clickGameResult_{{ $uniqueId }}"></div>

    <style>
        .click-the-circle-game-component .click-the-circle-area {
            display: none;
            background-color: #f8f9fa;
            overflow: hidden;
            height: 300px;
            width: 420px;
            max-width: 450px;
        }

        .click-the-circle-game-component .click-the-circle-target {
            background-color: #ff5722;
            border-radius: 50%;
            position: absolute;
            cursor: pointer;
            transition: top 0.2s, left 0.2s, width 0.2s, height 0.2s;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const wrapper = document.getElementById("{{ $uniqueId }}");

            const area = wrapper.querySelector(".click-the-circle-area");
            const target = wrapper.querySelector("#clickTheCircleTarget_{{ $uniqueId }}");
            const scoreDisplay = wrapper.querySelector("#clickGameScore_{{ $uniqueId }}");
            const timerDisplay = wrapper.querySelector("#clickGameTimer_{{ $uniqueId }}");
            const resultDisplay = wrapper.querySelector("#clickGameResult_{{ $uniqueId }}");
            const startButton = wrapper.querySelector("#startClickGame_{{ $uniqueId }}");

            let score = 0;
            let timeLeft = parseInt(timerDisplay.textContent) || 0;
            let timer;
            let moveDelay;
            let isPlaying = false;
            const modalId = "{{ $modalId }}"; 

            function randomPositionAndSize() {
                const size = Math.floor(Math.random() * 20) + 40; // 40px to 60px
                const maxLeft = area.clientWidth - size;
                const maxTop = area.clientHeight - size;

                return {
                    left: Math.floor(Math.random() * maxLeft),
                    top: Math.floor(Math.random() * maxTop),
                    size: size
                };
            }

            function showTarget() {
                const pos = randomPositionAndSize();
                target.style.left = `${pos.left}px`;
                target.style.top = `${pos.top}px`;
                target.style.width = `${pos.size}px`;
                target.style.height = `${pos.size}px`;
                target.style.display = "block";
            }

            function startGame() {
                area.style.display = 'block';
                wrapper.querySelector(".description").style.display = 'none';
                startButton.style.display = 'none';

                if (isPlaying) return;

                isPlaying = true;
                score = 0;
                timeLeft = parseInt(timerDisplay.textContent) || 0;
                scoreDisplay.textContent = score;
                timerDisplay.textContent = timeLeft;
                resultDisplay.textContent = "";
                startButton.disabled = true;

                showTarget();

                timer = setInterval(() => {
                    timeLeft--;
                    timerDisplay.textContent = timeLeft;
                    if (timeLeft <= 0) {
                        endGame();
                    }
                }, 1000);
            }

            function endGame() {
                area.style.display = 'none';
                wrapper.querySelector(".description").style.display = 'block';

                isPlaying = false;
                clearInterval(timer);
                clearTimeout(moveDelay);
                target.style.display = "none";
                startButton.disabled = false;
                resultDisplay.textContent = `Game Over! You scored ${score} points.`;

                if (score) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('common.saveRewardPoint') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            feature_key: @json($rewardDetail->feature_key),
                            module_key: @json($module),
                            module_id: @json($moduleId ?? null),
                            video_id: @json($videoId ?? null),
                            game_key: 'click-the-circle',
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
                                _toast.error('Something went wrong. Please try again.');
                            }
                        },
                        error: function (err) {
                            $('#reviewPointsBtn').prop('disabled', false);
                            _toast.error('Please try again.');
                        },
                    });
                }
            }

            target.addEventListener("click", () => {
                if (!isPlaying) return;
                score++;
                scoreDisplay.textContent = score;
                target.style.display = "none";
                const delay = Math.floor(Math.random() * 100) + 150;
                moveDelay = setTimeout(showTarget, delay);
            });

            startButton.addEventListener("click", startGame);
        });
    </script>
</div>
