@if(!empty($data) && count($data)>0)
@php
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@foreach($data as $recipe)
<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td>{{!empty($recipe->recipe) ? ucfirst($recipe->recipe->title) : "-"}}</td>
    <td>{{!empty($recipe->user) ? ucfirst($recipe->user->first_name.' '.$recipe->user->last_name) : "-"}}</td>
    <td>
        <div class="ratings">
            <i class="far fa-star stars {{$recipe->rating > 0 && $recipe->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="1"></i>
            <i class="far fa-star stars {{$recipe->rating > 1 && $recipe->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="2"></i>
            <i class="far fa-star stars {{$recipe->rating > 2 && $recipe->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="3"></i>
            <i class="far fa-star stars {{$recipe->rating > 3 && $recipe->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="4"></i>
            <i class="far fa-star stars {{$recipe->rating > 4 && $recipe->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="5"></i>
        </div>
    </td>
    <td>{{!empty($recipe->review) ? $recipe->review : "-"}}</td>
   
    <td class="align-middle">
        <div class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="iconmoon-ellipse"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" onClick="editReview({{$recipe}}, 'recipe')" href="javascript:void(0);">Edit</a>
                <a class="dropdown-item" onClick="deleteRecipeReview({{$recipe->id}})" href="javascript:void(0);">Delete</a>
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
    function readMore(quote){
        $('#text_limit_'+quote.id).hide();
        $('#text_all_'+quote.id).show();
    };
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadRecipeReviewList(pageLink);
            }
        });
    });
</script>