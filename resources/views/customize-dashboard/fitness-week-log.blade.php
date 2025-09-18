
@php 
    $userType = userType();
    $currentDay = strtoupper(getLocalDateTime('', 'l'));
    $weekData = $weekData['weekData'];
    $weekData = json_decode(json_encode($weekData), true);
@endphp

    <!-- <h3><a href="{{ route('user.fitnessProfile', ['user_type' => $userType]) }}">Fitness Week</a></h3> -->
    <div class="col-md-12 mainWidget_" data-id="{{ $widget_key }}">
    <div class="card fitness-box cursor-pointer pt-3"  
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
    
        <div class="row align-items-unset mb-4 card-body">
        <ul class="this-week-row d-flex">
    <li>
        <div class="fitness-vt">
            <h4>
                This Week
            </h4>
        </div>
    </li>
    @foreach ($weekData as $day => $week)
        <li>
            <div class="week-days-head" onclick="redirectTo('{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.fitnessProfile', ['user_type' => $userType]) }}')">
                <span class="week-days {{($currentDay === $day ? 'current-day' : '')}}">{{ $day }} </span>
                @if (!empty($week))
                    <ul class="list-unstyled">
                        @foreach ($week as $key => $weekArr)
                            <li>
                                <img src="{{ asset('assets/images/default-health.png') }}">
                                <span
                                    class="font-weight-bold h6">{{ str_replace('_', ' ', $weekArr['exercise']) }}</span>
                            </li>
                            
                        @endforeach

                    </ul>
                    @if ($day == $currentDay)
                        <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.fitnessProfile', ['user_type' => $userType]) }}"
                            class="btn btn-start">START</a>
                    @endif
                @else
                    <img class="day-off" src="{{ url('assets/images/day-off.png') }}">
                @endif
                
            </div>
        </li>
    @endforeach
    </ul>
        </div>
    </div>
</div>
    </div>
@section('js')
<script>
    let startOfWeek = null
    let endOfWeek = null
// let barChartData = [];
$(document).ready({
    $("#fitnessWeekLog").html('<div class="text-center">{{ ajaxListLoader() }}</div>');
     startOfWeek = moment().startOf('week').add('d', 1); //.format('YYYY-MM-DD');
     endOfWeek = moment().endOf('week').add('d', 1);
    getDays();
})

function getDays() {
     //.format('YYYY-MM-DD');
    var daysArr = [];
    var currentDate = moment(startOfWeek);
    var stopDate = moment(endOfWeek);
    while (currentDate <= stopDate) {
        daysArr.push(moment(currentDate).format('YYYY-MM-DD'))
        currentDate = moment(currentDate).add(1, 'days');
    }
    return daysArr;
}


</script>
@endsection
