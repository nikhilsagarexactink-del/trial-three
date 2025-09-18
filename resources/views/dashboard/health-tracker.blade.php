@php $userType = userType(); @endphp
 
<div class="">
    <div class="health-check-wrap">
        <h3><a href="{{ route('user.healthTracker', ['user_type'=>$userType]) }}">Health Markers</a></h3>
        <div class="health-check">
            <div class="health-checkin">
                <h4>Next Check In</h4>
                <p>{{formatDate($markerNextDate, 'M d, Y')}}</p>
            </div>
            <a href="{{route('user.healthTracker.healthMarker', ['user_type'=>$userType])}}">Check In</a>
        </div>
    </div>
</div>
<div class="">
    <div class="health-check-wrap">
        <h3><a href="{{ route('user.healthTracker', ['user_type'=>$userType]) }}">Health Measurements</a></h3>
        <div class="health-check">
            <div class="health-checkin">
                <h4>Next Check In</h4>
                <p>{{formatDate($measurementNextDate, 'M d, Y')}}</p>
            </div>
            <a href="{{route('user.healthTracker.healthMeasurement', ['user_type'=>$userType])}}">Check In</a>
        </div>
    </div>
</div> 
