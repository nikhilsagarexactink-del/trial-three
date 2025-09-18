@php 
$userType = userType();
@endphp

<div class="col-md-6 mainWidget_" data-id="{{ $widget_key }}" 
     @if($isCustomize)
         onmouseenter="showRemoveButton({{ json_encode($widget_key) }})" 
         onmouseleave="hideRemoveButton({{ json_encode($widget_key) }})"
     @endif>
    @if($isCustomize)
        <button class="remove-widget-btn" id="remove-btn-{{ $widget_key }}" 
            onclick="removeWidget(event, {{ json_encode($widget_key) }})" 
            style="display: none;">&times;
        </button>
    @endif
        <h4>Challenge Leaderboard</h4>
        <div class="white-bg bg-dark p-0 dash-table">
            <div id="leaderboard"></div>
        </div>
</div>
<script>
    function getChallengeLeaderboard(){
        $.ajax({
            url: "{{ route('common.getChallengeLeaderboard') }}",
            type: "GET",
            success: function (response) {
                $('#leaderboard').html("");    
                $('#leaderboard').append(response.data.html);    
            },
            error: function (err) {
                console.error('Error loading widgets list:', err);
            },
        });
    }
    $(document).ready(function() {
        getChallengeLeaderboard();
    })
</script>
