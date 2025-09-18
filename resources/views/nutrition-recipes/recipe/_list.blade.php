@if(!empty($data) && count($data)>0)
@php 
    $i=0; $currentPage = $data->currentPage(); 
    $userType = userType();
@endphp
@foreach($data as $recipe)

<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td>{{$recipe->title}}</td>
    <td>{{$recipe->subhead}}</td>
    <td>{{$recipe->prep_time}}</td>
    <td>{{$recipe->cook_time ?? 0}}</td>
    <td>{{$recipe->freeze_time ?? 0}}</td>
    <td>{{$recipe->servings}}</td>
    <td>{{$recipe->carbs}}</td>
    <td>{{!empty($recipe->date) ?date("m-d-Y", strtotime($recipe->date))  : ''}}</td>
    <td>
        @if($recipe->status == 'active')
        <span class="text-success">Active</span>
        @elseif($recipe->status == 'inactive')
        <span class="text-muted">Inactive</span>
        @elseif($recipe->status == 'deleted')
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
                    <a class="dropdown-item" href="{{route('user.editRecipeForm', ['id' => $recipe->id, 'user_type'=>$userType])}}">Edit</a>
                    @if($recipe->status == 'active')
                    <a class="dropdown-item" onClick="changeStatus('{{$recipe->id}}','inactive')" href="javascript:void(0);">Inactive </a>
                    @endif 
                    @if($recipe->status == 'inactive')
                    <a class="dropdown-item" onClick="changeStatus('{{$recipe->id}}','active')" href="javascript:void(0);">Active</a>
                    @endif
                    <a class="dropdown-item" onClick="changeStatus('{{$recipe->id}}','deleted')" href="javascript:void(0);">Delete</a>
                @endif
            </div>
        </div>
    </td>
</tr>
@php $i++; @endphp
@endforeach

@else
<tr>
    <td colspan="20">
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