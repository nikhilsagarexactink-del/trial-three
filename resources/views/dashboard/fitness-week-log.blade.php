@php 
    $userType = userType();
    $currentDay = strtoupper(getLocalDateTime('', 'l'));
@endphp
<ul class="this-week-row">
    <li>
        <div class="fitness-vt">
            <h4>
                This Week
            </h4>
        </div>
    </li>
    @foreach ($weekData as $day => $week)
        <li>
            <div class="week-days-head">
                <span class="week-days {{($currentDay == $day ? 'current-day' : '')}}">{{ $day }}</span>
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
                        <a href="{{ route('user.fitnessProfile', ['user_type' => $userType]) }}"
                            class="btn btn-start">START</a>
                    @endif
                @else
                    <img class="day-off" src="{{ url('assets/images/day-off.png') }}">
                @endif
            </div>
        </li>
    @endforeach
</ul>
