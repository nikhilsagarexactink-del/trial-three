@php 
    $i=0; 
    $currentPage = $data->currentPage();
    $userType = userType();
    $userData = getUser();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $reward)
    <tr data-id="{{ $reward->id }}">
        <td>{{getSerialNo($i, $currentPage)}}</td>
        <td>{{$reward->feature}}</td>
        @if(!empty($reward->reward_game))
            <td>{{$reward->reward_game->max_points}}</td>
        @else
            <td>{{$reward->point}}</td>
        @endif
        @php
            $gameType = '-';
            if (!empty($reward->reward_game)) {
                if ($reward->reward_game->game_type === 'random') {
                    $gameType = 'Random';
                } elseif ($reward->reward_game->game_type === 'specific') {
                    $gameType = 'Specific';
                }
            }
        @endphp
        <td>{{ $gameType }}</td>
        <td>{{ $reward->reward_game && $reward->reward_game->game?$reward->reward_game->game->title:"-"  }}</td>
        @endphp
        <td>
            @if($reward->status == 'active')
            <span class="text-success">Active</span>
            @elseif($reward->status == 'inactive')
            <span class="text-danger">Inactive</span>
            @elseif($reward->status == 'deleted')
            <span class="text-danger">Delete</span>
            @endif
        </td>
        <td>
            <div class="dropdown">
                <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="iconmoon-ellipse"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @if($userData->user_type == 'admin')
                        <a class="dropdown-item" href="{{route('user.editRewardManagementForm', ['id' => $reward->id,'user_type'=>$userType])}}">Edit</a>
                        @if($reward->status == 'active')
                        <a class="dropdown-item" onClick="changeStatus('{{$reward->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                        @endif 
                        @if($reward->status == 'inactive')
                        <a class="dropdown-item" onClick="changeStatus('{{$reward->id}}','active')" href="javascript:void(0);">Active</a>
                        @endif  
                        <!-- <a class="dropdown-item" onClick="changeStatus('{{$reward->id}}','deleted')" href="javascript:void(0);">Delete</a> -->
                    @endif
                    @if($userData->user_type != 'admin')
                    <a class="dropdown-item" href="{{route('user.viewRewardManagement', ['id' => $reward->id,'user_type'=>$userType])}}">View</a>
                    @endif
                </div>
            </div>
        </td>
    </tr>
    @php $i++; @endphp
    @endforeach
@else
<tr>
    <td colspan="12">
        <div class="alert alert-danger" role="alert">
            No Record Found.
        </div>
    </td>
</tr>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadList(pageLink);
            }
        });
    });
</script>