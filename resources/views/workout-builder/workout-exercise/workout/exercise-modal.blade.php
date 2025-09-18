@if (!empty($data) && count($data) > 0)
    <div class="row">
        @foreach ($data as $exercise)
            <div class="col-sm-4">
                <div class="exercise-set-head pb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    @if (!empty($exercise->media) && !empty($exercise->media->base_url))
                                        <img style="height:50px;width:50px;" src="{{ $exercise->media->base_url }}">
                                    @else
                                        <img style="height:50px;width:50px;"
                                            src="{{ url('assets/images/default-workout-image.jpg') }}">
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <h5 class="card-title fw-bold">{{ ucfirst($exercise['name']) }}</h5>
                                    <p class="card-text">
                                        {{ !empty($exercise['description']) ? strip_tags($exercise['description']) : '' }}
                                    </p>
                                    {{-- <a href="javascript:void(0)" class="btn btn-primary"
                                    onClick="addToSet({{ $exercise }})">ADD TO
                                    SET</a> --}}
                                </div>
                            </div>
                        </div>
                        <div class="set-cta">
                            <a href="javascript:void(0)" class="btn btn-primary"
                                onClick="addToSet({{ $exercise }})">ADD TO
                                SET</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
