@if(!empty($data) && count($data)>0)
@php $i=0; $currentPage = $data->currentPage(); @endphp
@foreach($data as $userRole)
<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td>{{$userRole->name}}</td>
     <td>
        @if($userRole->status == 'active')
        <span class="text-success">Active</span>
        @elseif($userRole->status == 'inactive')
        <span class="text-muted">Inactive</span>
        @elseif($userRole->status == 'deleted')
        <span class="text-danger">Delete</span>
        @endif
    </td> 
    <td>
    <div class="dropdown">
        <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="iconmoon-ellipse"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <!-- @if($userRole->status == 'active')
            <a class="dropdown-item" onClick="changeStatus('{{$userRole->id}}','inactive')" href="javascript:void(0);">Inactive </a>
            @endif 
            @if($userRole->status == 'inactive')
            <a class="dropdown-item" onClick="changeStatus('{{$userRole->id}}','active')" href="javascript:void(0);">Active</a>
            @endif   -->
            <!-- <a class="dropdown-item" onClick="changeStatus('{{$userRole->id}}','deleted')" href="javascript:void(0);">Delete</a> -->
            <a class="dropdown-item" onClick="getModuleList({{$userRole->id}})" href="javascript:void(0)">Permission</a>
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
                getUserRoleList(pageLink);
            }
        });
    });
</script>
