@php 
$userType = userType();
$markerNextDate = $detail['markerNextDate'];
$measurementNextDate = $detail['measurementNextDate'];
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
    <div class="equal-height">
        <div class="health-check-wrap">
            <h3><a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.healthTracker', ['user_type' => $userType]) }}">
                Health Markers
            </a></h3>
            <div class="health-check">
                <div class="health-checkin">
                    <h4>Next Check In</h4>
                    <p>{{formatDate($markerNextDate, 'M d, Y')}}</p>
                </div>
                <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.healthTracker', ['user_type' => $userType]) }}">Check In</a>
            </div>
        </div>
        <div class="health-check-wrap">
            <h3><a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.healthTracker', ['user_type' => $userType]) }}">Health Measurements</a></h3>
            <div class="health-check">
                <div class="health-checkin">
                    <h4>Next Check In</h4>
                    <p>{{formatDate($measurementNextDate, 'M d, Y')}}</p>
                </div>
                <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.healthTracker.healthMeasurement', ['user_type' => $userType]) }}">Check In</a>
                
            </div>
        </div>
    </div> 
</div>
