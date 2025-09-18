@if(!empty($data) && count($data)>0)
@foreach($data as $review)
@php 
    $userName = "";
    if(!empty($review->user) && !empty($review->user->screen_name)){
        $userName = ucfirst($review->user->screen_name);
    } else if(!empty($review->user)){
        $userName = ucfirst($review->user->first_name).' '.ucfirst(substr($review->user->last_name, 0, 1));
    }
@endphp
<li class="list-group-item review-list">
    <div class="review-top">
        <div class="rating-star">
            <div class="ratings">
                <i class="far fa-star {{$review->rating > 0 && $review->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="1"></i>
                <i class="far fa-star {{$review->rating > 1 && $review->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="2"></i>
                <i class="far fa-star {{$review->rating > 2 && $review->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="3"></i>
                <i class="far fa-star {{$review->rating > 3 && $review->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="4"></i>
                <i class="far fa-star {{$review->rating > 4 && $review->rating <= 5 ? 'fas' : ''}}" aria-hidden="true" value="5"></i>
            </div>
        </div>
        <p>{{$userName}}</p>
    </div>
    <p class="review-text">{{$review->review}}</p>
    <span class="review-date">{{getLocalDateTime($review->created_at, 'm-d-Y h:i A')}}</span>
</li>
@endforeach
    @if($data->currentPage() < $data->lastPage())
        <li class="list-group-item review-cta" id="loadMorePage{{$data->currentPage()}}">
            <button type="button" class="btn btn-outline-secondary text-center" id="loadMoreBtn" onClick="loadReviewList({{$data->currentPage()}}, '{{$data->nextPageUrl()}}')">Load More<span id="loadMoreBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
        </li>
    @endif
@else
    <div class="alert bg-light text-dark text-center" role="alert">
        No Reviews Yet. Would you like to be the first?
    </div>
@endif