@if(!empty($data) && count($data)>0)
@php
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@foreach($data as $trainingReview)
<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td>{{!empty($trainingReview->trainingVideo) ? ucfirst($trainingReview->trainingVideo->title) : "-"}}</td>
    <td>{{!empty($trainingReview->user) ? ucfirst($trainingReview->user->first_name.' '.$trainingReview->user->last_name) : "-"}}</td>
    <td>
        <div class="ratings">
            <i class="far fa-star stars {{$trainingReview->rating > 0 && $trainingReview->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="1"></i>
            <i class="far fa-star stars {{$trainingReview->rating > 1 && $trainingReview->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="2"></i>
            <i class="far fa-star stars {{$trainingReview->rating > 2 && $trainingReview->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="3"></i>
            <i class="far fa-star stars {{$trainingReview->rating > 3 && $trainingReview->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="4"></i>
            <i class="far fa-star stars {{$trainingReview->rating > 4 && $trainingReview->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="5"></i>
        </div>
    </td>
    <td>{{!empty($trainingReview->review) ? $trainingReview->review : "-"}}</td>
   
    <td class="align-middle">
        <div class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="iconmoon-ellipse"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" onClick="editReview({{$trainingReview}}, 'training-video')" href="javascript:void(0);">Edit</a>
                <a class="dropdown-item" onClick="deleteTrainingVideoReview('{{$trainingReview->id}}')" href="javascript:void(0);">Delete</a>
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
                loadTrainingReviewList(pageLink);
            }
        });
    });
</script>