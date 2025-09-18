@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @if($userType == 'admin')
        @foreach ($data as $workout)
            <tr>
                <td>{{ getSerialNo($i, $currentPage) }}</td>
                <td>{{ ucfirst($workout->name) }}</td>
                <!-- <td>{{ ucfirst($workout->no_of_reps) }} </td> -->
                <td>
                    @if ($workout->description)
                        {{truncateWords($workout->description, 20)}}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @php
                        $days = json_decode($workout->days, true);
                        if (is_array($days)) {
                            $capitalizedDays = array_map('ucfirst', $days);
                            echo implode(', ', $capitalizedDays);
                        } else {
                            echo '-';
                        }
                    @endphp

                </td>
                <td>
                    @if ($workout->status == 'draft')
                        <span class="text-info">Draft</span>
                    @elseif ($workout->status == 'active')
                        <span class="text-success">Active</span>
                    @elseif($workout->status == 'inactive')
                        <span class="text-danger">Inactive</span>
                    @elseif($workout->status == 'deleted')
                        <span class="text-danger">Delete</span>
                    @endif
                </td>
                <td>
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="iconmoon-ellipse"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"
                                href="{{ route('user.editFormWorkout', ['id' => $workout->id, 'user_type' => $userType]) }}">Edit</a>
                            @if ($workout->status == 'active' || $workout->status == 'draft')
                                <a class="dropdown-item" onClick="changeWorkoutStatus('{{ $workout->id }}', 'inactive')"
                                    href="javascript:void(0);">Inactive</a>
                            @endif
                            @if ($workout->status == 'inactive' || $workout->status == 'draft')
                                <a class="dropdown-item" onClick="changeWorkoutStatus('{{ $workout->id }}', 'active')"
                                    href="javascript:void(0);">Active</a>
                            @endif
                            <a class="dropdown-item" onClick="changeWorkoutStatus('{{ $workout->id }}', 'deleted')"
                                href="javascript:void(0);">Delete</a>
                            <a class="dropdown-item"
                                href="{{ route('user.viewWorkout', ['id' => $workout->id, 'user_type' => $userType]) }}">View</a>
                            <a class="dropdown-item" onClick="cloneWorkout('{{ $workout->id }}')"
                                href="javascript:void(0)">Clone</a>
                        </div>
                    </div>
                </td>
            </tr>
            @php $i++; @endphp
        @endforeach
    @else    
        @foreach ($data as $workout)
            <div class="col-md-4">
                <div class="card">
                    <a href="{{ route('user.viewWorkout', ['id' => $workout->id, 'user_type' => $userType]) }}">
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
                            <a href="{{ route('user.viewWorkout', ['id' => $workout->id, 'user_type' => $userType]) }}"
                                data-toggle="tooltip" data-placement="top" title="{{ $workout->name }}">
                                {{ ucfirst($workout->name) }}
                            </a>
                        </h5>
                        <div class="tag-head">
                            @php
                                $days = json_decode($workout->days, true);
                            @endphp
                                @if (is_array($days))
                                    @foreach ($days as $day)
                                        <span class="tag">{{ ucfirst($day) }}</span>
                                    @endforeach
                                @else
                                    <span>-</span>
                                @endif
                            @if ($workout->status == 'draft')
                                <span class="tag-draft">{{ ucfirst($workout->status) }}</span>
                             @endif
                        </div>
                        <p>{{truncateWords($workout->description, 20)}}</p>
                        <div class="btn_row">
                            <a class="btn btn-primary" title="Edit Workout"
                                    href="{{ route('user.editFormWorkout', ['id' => $workout->id, 'user_type' => $userType]) }}"><i class="fas fa-pencil-alt"></i> <!-- Edit --></a>
                          
                                <a class="btn btn-sm btn-primary" title="Change Status" onClick="changeWorkoutStatus('{{ $workout->id }}', '{{ $workout->status == 'active' ? 'inactive' : 'active' }}')"
                                                href="javascript:void(0);">{{$workout->status == 'active' ? 'To Use' : 'In Use'}}</a>
                                                  <a onClick="changeWorkoutStatus('{{ $workout->id }}', 'deleted')" title="Delete Workout"
                                    href="javascript:void(0);" class="btn btn-danger">
                                <i class="fa fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@else

            <div class="alert alert-danger" role="alert">
                Please add your first workout.
            </div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadWorkoutList(pageLink);
            }
        });
    });
</script>
