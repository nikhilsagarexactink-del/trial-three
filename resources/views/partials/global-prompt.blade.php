@php
if(Auth::guard('web')->check()){
    $isPromptMessage = App\Repositories\UserRepository::isPromptMessage();
}
@endphp
@if(isset($isPromptMessage) && $isPromptMessage['status'])
    <div class="global-prompt" id="global-prompt"> 📢 {!!$isPromptMessage['message']!!}
        <a href="javascript:void(0)" onclick="removePrompt()">[Dismiss]</a>
    </div>
@endif
<script>
    function removePrompt() {
        document.getElementById('global-prompt').style.display='none';   
    }
</script>
