@if(!empty($data) && count($data)>0)
@php 
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@foreach($data as $permission)
<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td>{{!empty($permission->user) ? ucfirst($permission->user->first_name.' '.$permission->user->last_name) : ''}}</td>
    <td>{{!empty($permission->user) ? $permission->user->email : ''}}</td>      
    <td>
        <div class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="iconmoon-ellipse"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="javascript:void(0);" onClick="deleteUserPermission('{{$permission->id}}')">Remove Permission</a>                 
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
                loadUserPermissionList(pageLink);
            }
        });
    });
</script>