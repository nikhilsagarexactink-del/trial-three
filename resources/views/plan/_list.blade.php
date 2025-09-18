@php 
    $i=0; 
    $currentPage = $data->currentPage();
    $userType = userType();
    $userData = getUser();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $plan)
    <tr>
        <td>{{getSerialNo($i, $currentPage)}}</td>
        <td>{{$plan->name}}</td>
        <td>{{ ucfirst($plan->visibility) }}</td>
        <td>
            @if($plan->status == 'active')
            <span class="text-success">Active</span>
            @elseif($plan->status == 'inactive')
            <span class="text-muted">Inactive</span>
            @elseif($plan->status == 'deleted')
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
                        <a class="dropdown-item" href="{{route('user.editPlanForm', ['id' => $plan->id,'user_type'=>$userType])}}">Edit</a>
                        @if($plan->status == 'active')
                        <a class="dropdown-item" onClick="changeStatus('{{$plan->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                        @endif 
                        @if($plan->status == 'inactive')
                        <a class="dropdown-item" onClick="changeStatus('{{$plan->id}}','active')" href="javascript:void(0);">Active</a>
                        @endif  
                        <a class="dropdown-item" onClick="changeStatus('{{$plan->id}}','deleted')" href="javascript:void(0);">Delete</a>
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