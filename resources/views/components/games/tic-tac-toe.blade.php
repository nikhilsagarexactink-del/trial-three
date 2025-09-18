@php
    $userData = getUser();
    $uniqueId = 'ticTacToe_' . $userData->id . '_' . $rewardDetail->id;
@endphp

<div class="tic-tac-toe-game-component" id="{{ $uniqueId }}">
    <div class="align-items-center mb-2">
        <h5 class="mb-0">{{ $game->title ?? 'Tic Tac Toe' }}</h5>
        <div class="mb-2 description">{{ $game->description ?? '' }}</div>

        <div class="choose-button text-center mb-3">
            <label class="me-2 fw-semibold">Choose your symbol to start:</label>
            <button class="btn btn-outline-primary me-2" data-symbol="X">Play as X</button>
            <button class="btn btn-outline-success" data-symbol="O">Play as O</button>
        </div>
    </div>

    <div class="tic-tac-toe-board mb-3"></div>

    <div class="mt-3 text-center fw-bold" id="ticTacToeResult_{{ $uniqueId }}"></div>

    <style>
        #{{ $uniqueId }} .tic-tac-toe-board {
            display: none;
            width: 300px;
            height: 300px;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
        }

        #{{ $uniqueId }} .cell {
            width: 100px;
            height: 100px;
            font-size: 2rem;
            text-align: center;
            line-height: 100px;
            background-color: #f0f0f0;
            border: 2px solid #ccc;
            cursor: pointer;
            user-select: none;
        }

        #{{ $uniqueId }} .cell.disabled {
            pointer-events: none;
            background-color: #e0e0e0;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const wrapper = document.getElementById("{{ $uniqueId }}");
            if (!wrapper) return;

            const board = wrapper.querySelector(".tic-tac-toe-board");
            const resultEl = wrapper.querySelector("#ticTacToeResult_{{ $uniqueId }}");
            const symbolButtons = wrapper.querySelectorAll("[data-symbol]");
            const chooseButtonWrapper = wrapper.querySelector(".choose-button");
            const descriptionWrapper = wrapper.querySelector(".description");
            const modalId = "{{ $modalId }}"; 
            let gameEnded = false; // <-- ADD THIS LINE

            // 🔹 Ensure fresh state if loaded again
            board.innerHTML = "";
            resultEl.textContent = "";
            board.style.display = 'none';
            chooseButtonWrapper.style.display = 'block';
            descriptionWrapper.style.display = 'block';

            let cells = [];
            let boardState = Array(9).fill(null);
            let isPlaying = false;
            let userSymbol = 'X';
            let botSymbol = 'O';

            const winPatterns = [
                [0, 1, 2], [3, 4, 5], [6, 7, 8],
                [0, 3, 6], [1, 4, 7], [2, 5, 8],
                [0, 4, 8], [2, 4, 6]
            ];

            function checkWinner(b) {
                for (const [a, b1, c] of winPatterns) {
                    if (b[a] && b[a] === b[b1] && b[a] === b[c]) {
                        return b[a];
                    }
                }
                return b.every(cell => cell) ? 'Draw' : null;
            }

            function botMove() {
                const emptyIndices = boardState.map((val, i) => val === null ? i : null).filter(i => i !== null);
                const move = emptyIndices[Math.floor(Math.random() * emptyIndices.length)];
                if (move != null) {
                    boardState[move] = botSymbol;
                    cells[move].textContent = botSymbol;
                    cells[move].classList.add('disabled');
                }
            }

            function endGame(result) {
                if (gameEnded) return; // prevent multiple endGame calls
                gameEnded = true;
                isPlaying = false;
                board.style.display = 'none';
                descriptionWrapper.style.display = 'block';

                cells.forEach(cell => cell.classList.add('disabled'));

                let status = (result === 'Draw') ? 'draw' : (result === userSymbol ? 'won' : 'lose');
                resultEl.textContent = `Game Over! You ${status.toUpperCase()}!`;

                $.post("{{ route('common.saveRewardPoint') }}", {
                    _token: "{{ csrf_token() }}",
                    feature_key: @json($rewardDetail->feature_key),
                    module_key: @json($module),
                    game_key: 'tic-tac-toe',
                    result: status,
                    video_id: @json($videoId ?? null),
                    module_id: @json($moduleId ?? null),
                    athlete_id: @json($athleteId ?? null),

                }).done(function (response) {
                    if (response.success) {
                         document.getElementById('closeBtn_' + modalId).style.display = 'none';
                        _toast.customSuccess(response.message);
                        setTimeout(() => window.location.reload(), 3000);
                    } else {
                        _toast.error('Something went wrong.');
                    }
                }).fail(() => _toast.error('Please try again.'));
            }

            function handleCellClick(index) {
                if (!isPlaying || boardState[index]) return;

                boardState[index] = userSymbol;
                cells[index].textContent = userSymbol;
                cells[index].classList.add('disabled');

                let result = checkWinner(boardState);
                if (result) return endGame(result);

                setTimeout(() => {
                    botMove();
                    let botResult = checkWinner(boardState);
                    if (botResult) endGame(botResult);
                }, 400);
            }

            function startGame(selectedSymbol) {
                gameEnded = false; // reset for a new game
                // 🔹 Reset before starting a new game
                boardState = Array(9).fill(null);
                cells = [];
                board.innerHTML = "";
                resultEl.textContent = "";

                isPlaying = true;
                board.style.display = "grid";
                chooseButtonWrapper.style.display = 'none';
                descriptionWrapper.style.display = 'none';

                userSymbol = selectedSymbol;
                botSymbol = (userSymbol === 'X') ? 'O' : 'X';

                for (let i = 0; i < 9; i++) {
                    const cell = document.createElement("div");
                    cell.className = "cell";
                    cell.dataset.index = i;
                    cell.addEventListener("click", () => handleCellClick(i));
                    board.appendChild(cell);
                }

                cells = board.querySelectorAll(".cell");

                if (userSymbol === 'O') {
                    setTimeout(() => {
                        botMove();
                    }, 300);
                }
            }

            symbolButtons.forEach(btn => {
                btn.addEventListener("click", function () {
                    const chosen = this.dataset.symbol;
                    startGame(chosen);
                });
            });
        });
    </script>

</div>
