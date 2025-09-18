@php 
$userType = userType();
$workout = $workouts;
@endphp

<div class="col-md-3 mainWidget_" data-id="{{ $widget_key }}" 
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
        <h4 class="dash-recipe-title">My Workout</h4>
        @if (!empty($workouts) && count($workouts) > 0)
            <div class="workout-card-slider equal-height">
                        @foreach ($workouts as $workout)
                            <div class="card">
                                <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' :  route('user.viewWorkout', ['id' => $workout->id, 'user_type' => $userType])  }}">
                                    <!-- data-lity -->
                                    @if (!empty($workout->media) && !empty($workout->media->base_url))
                                        <img class="card-img-top" src="{{ $workout->media->base_url }}" alt="{{ $workout->name }}">
                                    @else
                                        <img class="card-img-top" src="{{ url('assets/images/default-workout-image.jpg') }}"
                                            alt="{{ $workout->name }}">
                                    @endif
                                </a>
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' :  route('user.viewWorkout', ['id' => $workout->id, 'user_type' => $userType])  }}"
                                                data-toggle="tooltip" data-placement="top" title="{{ $workout->name }}">
                                                {{ ucfirst($workout->name) }}
                                            </a>
                                        </h5>
                                        <p>
                                        {{truncateWords($workout->description, 20)}}
                                        </p> 
                                        <ul class="widgets-desc-list">
                                            <li><strong>Category:</strong> {{ $workout->categories->pluck('name')->implode(', ') ?: '-' }}</li>
                                            <li><strong>Difficulty:</strong> {{ $workout->difficulties->pluck('name')->implode(', ') ?: '-' }}</li>
                                            <li><strong>Age Group:</strong> 
                                                @if(!empty($workout->ageRanges))
                                                    {{ $workout->ageRanges->map(fn($age) => "{$age->min_age_range} - {$age->max_age_range}")->implode(', ') }}
                                                @else
                                                    -
                                                @endif
                                            </li>
                                            <li><strong>Sport:</strong> {{ $workout->sports->pluck('name')->implode(', ') ?: '-' }}</li>
                                            <li><strong>Days:</strong> 
                                                @if(!empty($workout->days))
                                                    @php
                                                        $days = json_decode($workout->days, true);
                                                    @endphp
                                                    {{ is_array($days) ? implode(', ', array_map('ucfirst', $days)) : '-' }}
                                                @else
                                                    -
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                            </div>
                        @endforeach
            </div>
            @else
            <div class="card equal-height">
                <div class="alert alert-danger" role="alert">
                    Oops. No Workout  Found. Try again!
                </div>
            <div>
            @endif
        </div>
        
<script>
    $(document).ready(function () {
        if ($('.workout-card-slider').find('.card').length > 1) {
            $('.workout-card-slider').slick({
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: false,
                arrows: true,
                autoplay: true,
                speed: 500,
                autoplaySpeed: 2000,
                infinite: true,
                fade:true,
            });
        }
    });
</script>
@section('js')

@endsection