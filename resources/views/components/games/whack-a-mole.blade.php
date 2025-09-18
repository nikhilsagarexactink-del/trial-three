@php
    $userData = getUser();
    $uniqueId = 'whackMoleGame_' . $userData->id . '_' . $rewardDetail->id;
@endphp

<div class="whack-a-mole-game-component" id="{{ $uniqueId }}">
    <div class="align-items-center mb-2">
        <h5 class="mb-0">{{ $game->title ?? 'Whack-a-Mole' }}</h5>
        <div class="mb-2 description">{{ $game->description ?? '' }}</div>

        <div class="d-flex justify-content-between mb-2">
            <div>Score: <strong id="whackMoleScore_{{ $uniqueId }}">0</strong></div>
            <div>Time Left: <strong id="whackMoleTimer_{{ $uniqueId }}">{{ $rewardDetail->reward_game->duration ?? 30 }}</strong>s</div>
        </div>

        <div class="text-center mb-3 whackAMoleStart">
            <button id="startWhackMole_{{ $uniqueId }}" class="btn btn-success">Start Game</button>
        </div>
    </div>

    <div class="whack-mole-area border rounded position-relative">
        @for ($i = 0; $i < 12; $i++)
            <div class="mole-hole" data-index="{{ $i }}">
                <div class="mole" style="display: none;"></div>
            </div>
        @endfor
    </div>

    <div class="mt-3 text-center fw-bold text-success" id="whackMoleResult_{{ $uniqueId }}"></div>

    <style>
        #{{ $uniqueId }} .whack-mole-area {
            display: none;
            background: url('/assets/images/mole-background.png');
            width: 420px;
            height: 300px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            padding: 10px;
        }

        #{{ $uniqueId }} .mole-hole {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
        }

        #{{ $uniqueId }} .mole {
            width: 80%;
            height: 80%;
            background: url('/assets/images/mole.png') no-repeat center center;
            background-size: contain;
            position: absolute;
            bottom: 0;
            left: 10%;
            cursor: pointer;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const wrapper = document.getElementById("{{ $uniqueId }}");
            if (!wrapper) return;

            const rewardDetail = @json($rewardDetail);
            const module = @json($module);
            const moduleId = @json($moduleId ?? null);
            const videoId = @json($videoId ?? null);

            const area = wrapper.querySelector(".whack-mole-area");
            const startBtnWrapper = wrapper.querySelector(".whackAMoleStart");
            const startBtn = wrapper.querySelector("#startWhackMole_{{ $uniqueId }}");
            const scoreEl = wrapper.querySelector("#whackMoleScore_{{ $uniqueId }}");
            const timerEl = wrapper.querySelector("#whackMoleTimer_{{ $uniqueId }}");
            const resultEl = wrapper.querySelector("#whackMoleResult_{{ $uniqueId }}");
            const holes = wrapper.querySelectorAll(".mole-hole");
            const modalId = "{{ $modalId }}"; 

            area.style.display = 'none';
            let score = 0;
            let timeLeft = rewardDetail.reward_game.duration;
            let gameTimer, moleTimer;
            let isPlaying = false;

            function randomHole() {
                const idx = Math.floor(Math.random() * holes.length);
                return holes[idx];
            }

            function showMole() {
                const hole = randomHole();
                const mole = hole.querySelector(".mole");
                mole.style.display = "block";

                mole.onclick = function () {
                    if (!isPlaying) return;
                    score++;
                    scoreEl.textContent = score;
                    mole.style.display = "none";
                };

                setTimeout(() => mole.style.display = "none", 1500);
            }

            function startGame() {
                if (isPlaying) return;

                isPlaying = true;
                score = 0;
                timeLeft = rewardDetail.reward_game.duration;
                scoreEl.textContent = score;
                timerEl.textContent = timeLeft;
                resultEl.textContent = "";
                startBtn.disabled = true;
                startBtnWrapper.style.display = 'none';
                area.style.display = "grid";
                wrapper.querySelector(".description").style.display = 'none';

                moleTimer = setInterval(showMole, 1400);
                gameTimer = setInterval(() => {
                    timeLeft--;
                    timerEl.textContent = timeLeft;
                    if (timeLeft <= 0) {
                        endGame();
                    }
                }, 1000);
            }

            function endGame() {
                isPlaying = false;
                clearInterval(gameTimer);
                clearInterval(moleTimer);
                area.style.display = "none";
                wrapper.querySelector(".description").style.display = 'block';
                resultEl.textContent = `Game Over! You scored ${score} points.`;

                if (score > 0) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('common.saveRewardPoint') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            feature_key: rewardDetail.feature_key,
                            module_key: module,
                            module_id: moduleId,
                            video_id: videoId,
                            game_key: 'whack-a-mole',
                            score: score,
                            athlete_id: @json($athleteId ?? null),

                        },
                        success: function (response) {
                            $('#reviewPointsBtn').prop('disabled', false);
                            if (response.success) {
                                 document.getElementById('closeBtn_' + modalId).style.display = 'none';                                
                                _toast.customSuccess(response.message);
                                setTimeout(() => window.location.reload(), 3000);
                            } else {
                                _toast.error('Something went wrong. Please try again.');
                            }
                        },
                        error: function () {
                            $('#reviewPointsBtn').prop('disabled', false);
                            _toast.error('Please try again.');
                        },
                    });
                }
            }

            startBtn.addEventListener("click", startGame);
        });
    </script>
</div>
