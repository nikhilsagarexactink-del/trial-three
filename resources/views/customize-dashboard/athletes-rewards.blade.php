@php $userType = userType();

@endphp

<div class="col-md-4 mainWidget_" data-id="{{ $widget_key }}" 
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

    <h4><a class="text-dark" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.useYourRewardIndex', ['user_type' => $userType]) }}">Athletes Rewards</a></h4>
    <a class="equal-height d-block" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.useYourRewardIndex', ['user_type' => $userType]) }}">
        <div class="white-bg p-3 h-100">
            <ul class="gatting-started-links">
            @if((!empty($allAthletes)) && $allAthletes->count() > 0)
                    @foreach($allAthletes as $athlete)
                        <li>{{$athlete->first_name}} {{$athlete->last_name}}: {{$athlete->total_reward_points?$athlete->total_reward_points:'0'}}  Points</li>
                    @endforeach
                    @endif
            </ul>
        </div>
</a>
                        
</div>

