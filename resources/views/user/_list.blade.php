@php 
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
    $permissions = getModulePermission(['key'=>'login-as-all-users']);
    $loginPermission = !empty($permissions) && $permissions[0]['permission'] == 'yes' ? 'yes' : 'no';
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $user)
        <tr>
            
            <td>{{getSerialNo($i, $currentPage)}}</td>
            <td>{{$user->first_name}} {{$user->last_name}}</td>
            <td>{{$user->email}}</td>
            <td>{{ucfirst($user->user_type)}}</td>
            <td>{{!empty($user->userSubsription) ? ucfirst($user->userSubsription->plan_name) : ''}}</td>
            <td>{{ !empty($user->groupUsers) && $user->groupUsers->isNotEmpty() ?  $user->groupUsers->pluck('group.name')->implode(', ') : '-' }}</td>
            <td>{{!empty($user->last_login_date) ? getLocalDateTime($user->last_login_date, 'm-d-Y g:i A') : '-'}}</td>
            <td>{{getLocalDateTime($user->created_at, 'm-d-Y g:i A')}}</td>
            <td>        
                @if($user->status == 'active')
                <span class="text-success">Active</span>
                @elseif($user->status == 'inactive')
                <span class="text-muted">Inactive</span>
                @elseif($user->status == 'deleted')
                <span class="text-danger">Delete</span>
                @elseif($user->status == 'payment_failed')
                <span class="text-danger">Payment Failed</span>
                @endif
            </td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @if($userType == 'admin')
                        <a class="dropdown-item" href="{{route('user.editUserForm', ['id' => $user->id, 'user_type'=>$userType])}}">Edit</a>
                        @if($user->status == 'active')
                            <a class="dropdown-item" onClick="changeStatus('{{$user->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                        @endif 
                        @if($user->status == 'inactive')
                            <a class="dropdown-item" onClick="changeStatus('{{$user->id}}','active')" href="javascript:void(0);">Active</a>
                        @endif  
                        <a class="dropdown-item" onClick="changeStatus('{{$user->id}}','deleted')" href="javascript:void(0);">Delete</a>
                        @if($loginPermission=='yes')
                        <a class="dropdown-item" onClick="loginAsUser({{$user}})" href="javascript:void(0);">Login as {{ucfirst($user->first_name.' '.$user->last_name)}}</a>
                        @endif
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