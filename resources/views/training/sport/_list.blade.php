@if(!empty($data) && count($data)>0)
@php 
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@foreach($data as $sport)
<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td>{{$sport->name}}</td>
    <td>
        @if($sport->status == 'active')
        <span class="text-success">Active</span>
        @elseif($sport->status == 'inactive')
        <span class="text-danger">Inactive</span>
        @elseif($sport->status == 'deleted')
        <span class="text-danger">Delete</span>
        @endif
    </td>
    <td>
        <div class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="iconmoon-ellipse"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @if($userType == 'admin')
                    <a class="dropdown-item" href="{{route('user.training.sport.editForm', ['id' => $sport->id, 'user_type'=>$userType])}}">Edit</a>
                    @if($sport->status == 'active')
                        <a class="dropdown-item" onClick="changeStatus('{{$sport->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                    @endif 
                    @if($sport->status == 'inactive')
                        <a class="dropdown-item" onClick="changeStatus('{{$sport->id}}','active')" href="javascript:void(0);">Active</a>
                    @endif  
                    <a class="dropdown-item" onClick="changeStatus('{{$sport->id}}','deleted')" href="javascript:void(0);">Delete</a>
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