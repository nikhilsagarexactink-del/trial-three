@if(!empty($data) && count($data)>0)
@php
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@foreach($data as $ageRange)
<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td>{{!empty($ageRange->min_age_range) ? $ageRange->min_age_range : 0}}</td>
    <td>{{!empty($ageRange->max_age_range) ? $ageRange->max_age_range : 0}}</td>
    <td>
        @if($ageRange->status == 'active')
        <span class="text-success">Active</span>
        @elseif($ageRange->status == 'inactive')
        <span class="text-danger">Inactive</span>
        @elseif($ageRange->status == 'deleted')
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
                <a class="dropdown-item" href="{{route('user.editAgeRangeForm', ['id' => $ageRange->id,'user_type'=>$userType])}}">Edit</a>
               @if($ageRange->status == 'active')
                <a class="dropdown-item" onClick="changeStatus('{{$ageRange->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                @endif 
                @if($ageRange->status == 'inactive')
                <a class="dropdown-item" onClick="changeStatus('{{$ageRange->id}}','active')" href="javascript:void(0);">Active</a>
                @endif  
                <a class="dropdown-item" onClick="changeStatus('{{$ageRange->id}}','deleted')" href="javascript:void(0);">Delete</a>
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