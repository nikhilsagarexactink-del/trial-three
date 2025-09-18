@php 
    $i=0; 
    $currentPage = $data->currentPage();
    $userType = userType();
    $userData = getUser();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $product)
    <tr data-id="{{ $product->id }}">
        <td>{{getSerialNo($i, $currentPage)}}</td>
        <td>{{$product->title}}</td>
        <td>{{$product->point_cost}}</td>
        <td>{{$product->available_quantity}}</td>
        <td>{{$product->description}}</td>
        <td>
<div class="d-flex justify-content-start align-items-center">
            @if($product->availability_status == 'available')
            <span class="text-success">Available</span>
            @elseif($product->availability_status == 'unavailable' || $product->availability_status == null)
            <span class="text-danger">Unavailable</span>
            @endif
            @if($userData->user_type == 'admin') 
            <div class="dropdown ms-2">
                <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="iconmoon-ellipse"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @if($userData->user_type == 'admin')
                        @if($product->availability_status == 'available')
                        <a class="dropdown-item" onClick="changeAvailabilityStatus('{{$product->id}}','unavailable')" href="javascript:void(0);">Unavailable </a>
                        @endif 
                        @if($product->availability_status == 'unavailable' || $product->availability_status == null)
                        <a class="dropdown-item" onClick="changeAvailabilityStatus('{{$product->id}}','available')" href="javascript:void(0);">Available</a>
                        @endif  
                    @endif
                </div>
            </div>       
            @endif
</div>
        </td>
        <td>
        @if($product->status == 'active')
            <span class="text-success">Active</span>
            @elseif($product->status == 'inactive')
            <span class="text-danger">Inactive</span>
            @elseif($product->status == 'deleted')
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
                        <a class="dropdown-item" href="{{route('user.editRewardProductForm', ['id' => $product->id,'user_type'=>$userType])}}">Edit</a>
                        @if($product->status == 'active')
                        <a class="dropdown-item" onClick="changeStatus('{{$product->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                        @endif 
                        @if($product->status == 'inactive')
                        <a class="dropdown-item" onClick="changeStatus('{{$product->id}}','active')" href="javascript:void(0);">Active</a>
                        @endif  
                        <a class="dropdown-item" onClick="changeStatus('{{$product->id}}','deleted')" href="javascript:void(0);">Delete</a>
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