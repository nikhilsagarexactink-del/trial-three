@php
$i = 0;
$currentPage = $data->currentPage();
$userType = userType();
$permissions = getModulePermission(['key' => 'login-as-all-users']);
$loginPermission = !empty($permissions) && $permissions[0]['permission'] == 'yes' ? 'yes' : 'no';
$challengeId = request()->route('id');
@endphp

@if(!empty($data) && count($data) > 0)
@foreach($data as $user)
<tr>
    <td>{{ getSerialNo($i, $currentPage) }}</td>
    <td><a  href="{{ route('user.viewUserChallengeProgress', ['challenge_id' => $challengeId, 'user_id' =>$user->user->id , 'user_type' => $userType]) }}">{{ $user->user->first_name }} {{ $user->user->last_name }}</a></td>
    <td>{{ $user->user->email }}</td>
    <td>
        @if($user->user->status == 'inactive')
        <span class="text-warning">Inactive</span>
        @elseif($user->user->status == 'active')
        <span class="text-success">Active</span>
        @endif
    </td>
    <!-- <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if($userType == 'admin')
                            @if($user->status == 'inactive')
                                <a class="dropdown-item" onClick="changeChallengeUserStatus('{{$user->id}}','active')" href="javascript:void(0);">Active</a>
                            @elseif($user->status == 'active')
                                <a class="dropdown-item" onClick="changeChallengeUserStatus('{{$user->id}}','inactive')" href="javascript:void(0);">Inactive</a>
                            @endif  
                        @endif
                    </div>
                </div>
            </td> -->
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
            loadUsers(pageLink);
        }
    });

    $(document).on('click', '.dropdown-toggle', function() {
        $(this).next('.dropdown-menu').toggle();
    });
});
</script>