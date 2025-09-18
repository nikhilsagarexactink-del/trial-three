@php 
    $i=0; 
    $currentPage = $data->currentPage();
    $userType = userType();
    $userData = getUser();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $group)
    <tr>
        <td>{{getSerialNo($i, $currentPage)}}</td>
        <td>{{$group->name}}</td>
        <td>
            @if($group->status == 'active')
            <span class="text-success">Active</span>
            @elseif($group->status == 'inactive')
            <span class="text-muted">Inactive</span>
            @elseif($group->status == 'deleted')
            <span class="text-danger">Delete</span>
            @endif
        </td>
        <td>
            <div class="dropdown">
                <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="iconmoon-ellipse"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @if($userData->user_type == 'admin' || $userData->user_type == 'parent')
                        <a class="dropdown-item" href="{{route('user.groupEditForm', ['id' => $group->id,'user_type'=>$userType])}}">Edit</a>
                        @if($group->status == 'active')
                        <a class="dropdown-item" onClick="changeStatus('{{$group->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                        @endif 
                        @if($group->status == 'inactive')
                        <a class="dropdown-item" onClick="changeStatus('{{$group->id}}','active')" href="javascript:void(0);">Active</a>
                        @endif  
                        <a class="dropdown-item" onClick="changeStatus('{{$group->id}}','deleted')" href="javascript:void(0);">Delete</a>
                    @if($group->group_code != null)
                    <a class="dropdown-item" onclick="copySignupUrl('{{ route('plans') }}?user_type=athlete&group_code={{ urlencode($group->group_code) }}')" href="javascript:void(0);">
                        Copy Sign-up URL
                    </a>
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
            No Group Found.
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