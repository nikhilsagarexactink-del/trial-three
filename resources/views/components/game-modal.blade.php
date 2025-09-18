@php 
    $gameInfo = getDynamicGames( $rewardDetail);
    $componentClass = $gameInfo['componentClass'];
    $gameKey = $gameInfo['game_key'];
    $userId = getUser()->id;
    $rewardId = $rewardDetail->id ?? 'default';
    $modalId = 'gameModal_' . $userId . '_' . $rewardId;
@endphp


<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="gameModalTitle_{{ $userId }}_{{ $rewardId }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 450px;">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title" id="gameModalTitle_{{ $userId }}_{{ $rewardId }}">Play the Game to get the Rewards</h5>
                <button type="button" class="close close-confirm-btn" id="closeBtn_{{ $modalId }}" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center align-items-center" style="height: 450px;">
                @if($componentClass)
                    <x-dynamic-component :component="$componentClass" :rewardDetail="$rewardDetail"  :module="$module" :module-id="$moduleId" :video-id="$videoId" :athlete-id="$athleteId" :modal-id="$modalId" />
                @endif
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function () {
    const modalId = "#{{ $modalId }}";
    const closeBtnId = "#closeBtn_{{ $modalId }}";
    let isClosingConfirmed = false;

    const modal = $(modalId);

    function confirmModalClose_{{ $userId }}_{{ $rewardId }}() {
        bootbox.confirm({
            title: "Are you sure you want to close the game?",
            message: "Closing now may forfeit your chance to earn rewards.",
            buttons: {
                confirm: {
                    label: 'Yes, Close',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'No, Stay',
                    className: 'btn-secondary'
                }
            },
            callback: function(result) {
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('common.saveRewardPoint') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            feature_key: @json($rewardDetail->feature_key),
                            module_id: @json($moduleId ?? null),
                            game_key : @json($gameKey),
                            athlete_id: @json($athleteId ?? null),
                            result : 'not_played',
                        },
                        success: function(response) {
                            $('#reviewPointsBtn').prop('disabled', false);
                            if (response.success) {
                                _toast.customSuccess(response.message);
                                setTimeout(function() {
                                    window.location.reload();
                                }, 3000)
                            } else {
                                _toast.error('Somthing went wrong. please try again');
                            }
                        },
                        error: function(err) {
                            $('#reviewPointsBtn').prop('disabled', false);
                            _toast.error('Please try again.');
                        },
                    });
                }
            }
        });
    }

    $(document).ready(function () {
        $(closeBtnId).on('click', function () {
            confirmModalClose_{{ $userId }}_{{ $rewardId }}();
        });

        modal.on('hide.bs.modal', function (e) {
            if (!isClosingConfirmed) {
                e.preventDefault();
                confirmModalClose_{{ $userId }}_{{ $rewardId }}();
                return false;
            } else {
                isClosingConfirmed = false;
            }
        });

        $(modalId).modal({
            backdrop: 'static',
            keyboard: false
        });
    });
    
   
})();
</script>
