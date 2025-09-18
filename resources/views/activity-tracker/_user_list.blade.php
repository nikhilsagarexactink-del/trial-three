@if(!empty($data) && count($data)>0)
@php 
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@foreach($data as $user)
    @if(!empty($user->user))
        <tr>
            <td>{{getSerialNo($i, $currentPage)}}</td>
            <td>{{!empty($user->user) ? ucfirst($user->user->first_name.' '.$user->user->last_name) : ''}}</td>
            <td>{{!empty($user->user) ? $user->user->email : ''}}</td>      
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{route('user.activityTracker',['user_type'=>$userType, 'user_id'=>$user->user_id])}}">View Activity</a>                 
                    </div>
                </div>
            </td>
        </tr>
    @endif
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
                loadUserList(pageLink);
            }
        });
    });
</script>