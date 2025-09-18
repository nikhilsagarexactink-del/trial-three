@php
    $userData = getUser();
    $uniqueId = 'spinningWheelGame_' . $userData->id . '_' . $rewardDetail->id;
@endphp

<div class="spinning-wheel-game-component" id="{{ $uniqueId }}">
    <div class="mb-2">
        <h5 class="mb-0">{{ $game->title ?? 'Spinning Wheel' }}</h5>
        <div class="description mb-2">{{ $game->description ?? 'Tap the knob to spin the wheel and win rewards!' }}</div>
    </div>

    <div class="wheel-container">
        <div class="wheel-pointer"></div>
        <canvas id="wheelCanvas_{{ $uniqueId }}" width="300" height="300"></canvas>
        <div class="wheel-knob" id="knob_{{ $uniqueId }}"></div>
    </div>

    <div class="text-center mt-3">
        <div id="result_{{ $uniqueId }}" class="mt-2"></div>
    </div>
</div>

<style scoped>
.spinning-wheel-game-component {
    max-width: 360px;
    margin: auto;
    font-family: sans-serif;
}

.wheel-container {
    position: relative;
    width: 300px;
    height: 300px;
    margin: auto;
}

.wheel-pointer {
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-53%) translateY(40%) rotate(56deg);
    width: 0;
    height: 0;
    border-left: 20px solid transparent;
    border-right: 20px solid transparent;
    border-bottom: 34px solid #dc3545;
    z-index: 10;
}

.wheel-knob {
    position: absolute;
    width: 45px;
    height: 45px;
    background: #dc3545;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 5;
    box-shadow: 0 0 8px rgba(0,0,0,0.3);
    cursor: pointer;
}

#result {
    font-weight: bold;
    font-size: 18px;
    color: green;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    (function () {
        const uniqueId = @json($uniqueId);
        const canvas = document.getElementById('wheelCanvas_' + uniqueId);
        const ctx = canvas.getContext('2d');
        const knob = document.getElementById('knob_' + uniqueId);
        const resultDiv = document.getElementById('result_' + uniqueId);
        let hasSpun = false;

        const min = @json($rewardDetail->reward_game->min_points);
        const max = @json($rewardDetail->reward_game->max_points);
        const maxSegments = 8;
        const modalId = "{{ $modalId }}"; 
        let segmentValues = [];

        const diff = max - min;

        if (diff === 0) {
            segmentValues = Array(maxSegments).fill(min);
        } else if (diff === 8) {
            for (let i = 0; i < maxSegments; i++) {
                segmentValues.push(min + i);
            }
        } else if (diff < 8) {
            for (let i = min; i <= max; i++) {
                segmentValues.push(i);
            }
            while (segmentValues.length < maxSegments) {
                segmentValues.push(...segmentValues);
            }
            segmentValues = segmentValues.slice(0, maxSegments);
        } else {
            const step = diff / (maxSegments - 1);
            for (let i = 0; i < maxSegments; i++) {
                segmentValues.push(Math.round(min + step * i));
            }
            segmentValues = [...new Set(segmentValues)];
            while (segmentValues.length < maxSegments) {
                segmentValues.push(segmentValues[segmentValues.length - 1]);
            }
            segmentValues = segmentValues.slice(0, maxSegments);
        }

        const totalSegments = segmentValues.length;

        const segments = segmentValues.map((value, i) => {
            const color = `hsl(${(i * 360 / totalSegments)}, 70%, 60%)`;
            return { value, color };
        });

        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 140;

        let currentAngle = 0;
        let spinVelocity = 0;
        let accelerating = false;
        let decelerating = false;
        let frameId;

        let spinSpeed = 0.3; // default spin speed

        function drawWheel() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const anglePerSegment = 2 * Math.PI / totalSegments;

            segments.forEach((seg, i) => {
                const startAngle = currentAngle + i * anglePerSegment;
                const endAngle = startAngle + anglePerSegment;

                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.fillStyle = seg.color;
                ctx.fill();
                ctx.closePath();

                const midAngle = startAngle + anglePerSegment / 2;
                const textX = centerX + Math.cos(midAngle) * radius * 0.6;
                const textY = centerY + Math.sin(midAngle) * radius * 0.6;

                ctx.save();
                ctx.translate(textX, textY);
                ctx.rotate(midAngle);
                ctx.fillStyle = "#fff";
                ctx.font = "bold 16px sans-serif";
                ctx.textAlign = "center";
                ctx.fillText(seg.value, 0, 0);
                ctx.restore();
            });
        }

        function animate() {
            currentAngle += spinVelocity;
            document.getElementById('closeBtn_' + modalId).style.display = 'none';
            drawWheel();

            if (accelerating) {
                spinVelocity += 0.005;
                if (spinVelocity > spinSpeed) spinVelocity = spinSpeed;
                frameId = requestAnimationFrame(animate);
            } else if (decelerating) {
                spinVelocity *= 0.97;
                if (spinVelocity < 0.002) {
                    decelerating = false;
                    spinVelocity = 0;
                    showResult();
                } else {
                    frameId = requestAnimationFrame(animate);
                }
            } else if (spinVelocity > 0) {
                frameId = requestAnimationFrame(animate);
            }
        }

        function showResult() {
            hasSpun = true;
            const anglePerSegment = 360 / totalSegments;
            const degrees = (currentAngle * 180 / Math.PI) % 360;
            const normalized = (360 - degrees + 270) % 360;
            let index = Math.floor(normalized / anglePerSegment);

            index = Math.max(0, Math.min(index, totalSegments - 1));
            const reward = segments[index].value;

            resultDiv.innerHTML = `🎉 You won <span style="color:#28a745">${reward} points!</span>`;

            $.ajax({
                type: "POST",
                url: "{{ route('common.saveRewardPoint') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    feature_key: @json($rewardDetail->feature_key),
                    module_key: @json($module),
                    game_key: 'spinning-wheel',
                    reward_points: reward,
                    video_id : @json($videoId??null),
                    module_id: @json($moduleId ?? null),
                    athlete_id: @json($athleteId ?? null),
                },
                success: function(response) {
                    $('#reviewPointsBtn').prop('disabled', false);
                    if (response.success) {
                        _toast.customSuccess(response.message);
                        setTimeout(() => window.location.reload(), 3000);
                    } else {
                        _toast.error('Something went wrong. Please try again.');
                    }
                },
                error: function() {
                    $('#reviewPointsBtn').prop('disabled', false);
                    _toast.error('Please try again.');
                }
            });
        }

        knob.addEventListener('mousedown', () => {
            if (hasSpun || accelerating || decelerating) return;

            // 🎯 Randomize spin speed on each click (between 0.3 and 0.6)
            spinSpeed = (Math.random() * 0.3) + 0.3;

            hasSpun = true;
            resultDiv.innerHTML = '';
            accelerating = true;
            decelerating = false;
            cancelAnimationFrame(frameId);
            animate();
        });

        document.addEventListener('mouseup', () => {
            if (accelerating) {
                accelerating = false;
                decelerating = true;
            }
        });

        drawWheel();
    })();
});
</script>
