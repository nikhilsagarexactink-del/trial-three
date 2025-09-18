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
        <h4>Login Activity</h4>
        <div class="white-bg p-3 dash-table">
            <h5>Last Login Details </h5>
            <div class="common-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if((!empty($allAthletes)) && $allAthletes->count() > 0)
                    @foreach($allAthletes as $athlete)
                        <tr>
                            <td>{{$athlete->first_name}}</td>
                            <td>{{!empty($athlete->last_login_date)? \Carbon\Carbon::parse($athlete->last_login_date)->format('m-d-Y'):"-" }}</td>
                            <td>{{ !empty($athlete->last_login_date)?\Carbon\Carbon::parse($athlete->last_login_date)->format('h:i A'):"-" }}</td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
</div>
