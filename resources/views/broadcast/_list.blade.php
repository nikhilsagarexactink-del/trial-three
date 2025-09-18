@php 
    use Carbon\Carbon;
    $userType = userType();
@endphp
@if(!empty($data) && count($data)>0)
@php $i=0; $currentPage = $data->currentPage();@endphp
@foreach($data as $broadcast)
<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td><a class="text-dark"  onClick="openBroadcastModal('{{$broadcast->id}}' , '{{$broadcast->title}}')" href="javascript:void(0);">{{ucfirst($broadcast->title)}}</a> </td>
    <td>{{ Carbon::parse($broadcast->scheduled_date)->format('m-d-Y') }}</td>
    <td>{{ Carbon::parse($broadcast->scheduled_time)->format('h:i A') }}</td>
    <td>{{ucfirst($broadcast->type)}}</td>
    <td>
        {{ collect(explode(',', $broadcast->send_type))
            ->map(fn($item) => ucwords(
                str_replace('_', ' ', trim($item === 'alert' ? 'SMS_alert' : $item))
            ))
            ->join(', ') 
        }}
    </td>
    <td>
        @if($broadcast->status == 'active')
        <span class="text-success">Active</span>
        @elseif($broadcast->status == 'inactive')
        <span class="text-danger">Inactive</span>
        @elseif($broadcast->status == 'deleted')
        <span class="text-danger">Delete</span>
        @endif
    </td>
    <td>
        <div class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="iconmoon-ellipse"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="{{route('user.broadcastEditForm', ['id' => $broadcast->id, 'user_type'=>$userType])}}">Edit</a>
                @if($broadcast->status == 'active')
                <a class="dropdown-item" onClick="changeStatus('{{$broadcast->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                @endif 
                @if($broadcast->status == 'inactive')
                <a class="dropdown-item" onClick="changeStatus('{{$broadcast->id}}','active')" href="javascript:void(0);">Active</a>
                @endif  
                <a class="dropdown-item" onClick="changeStatus('{{$broadcast->id}}','deleted')" href="javascript:void(0);">Delete</a>
                @if($userType == 'admin')
                    <a class="dropdown-item" href="{{route('user.broadcastClone', ['id' => $broadcast->id,'user_type'=>$userType])}}">Clone</a>
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
                loadBroadcastList(pageLink);
            }
        });
    });
</script>