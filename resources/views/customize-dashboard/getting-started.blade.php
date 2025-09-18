@php
    $userType = userType();
    $userData = getUser();
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
    @if($categories_gettingStarted && count($categories_gettingStarted) > 0)
    <h4 class="dash-recipe-title">
        <a class="text-dark" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.gettingStarted.index', ['user_type' => $userType]) }}">
            Getting Started
        </a>
    </h4>
    <a class="equal-height d-block" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.gettingStarted.index', ['user_type' => $userType]) }}" >            
        <div class="white-bg p-3 h-100">
            <ul class="gatting-started-links"> 
                @if(count($categories_gettingStarted) > 0)
                    @foreach($categories_gettingStarted as $key => $category)
                            @if($key == 5)
                                @break
                            @endif
                            <li class="">{{$category['name']}}</li>
                    @endforeach
                @endif
            </ul>     
        </div>    
    </a>
   @else
    <h4 class="dash-recipe-title">Getting Started</h4>
    <div class="card  p-3">
        <div class="alert alert-danger" role="alert">
                Oops. Getting Started Section is not Available. Please Contact to Admin
        </div>
    </div>
   @endif
</div>