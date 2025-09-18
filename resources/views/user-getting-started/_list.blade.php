@if(!empty($data) && count($data)>0)
@php $i=0; $currentPage = $data->currentPage(); @endphp
@foreach($data as $video)
@php
    $rating = !empty($video->avg_ratings) ? $video->avg_ratings : 0;
    if(!empty($video->favourite)){
        $isFavourite = true;
    }else{
        $isFavourite = false;
    }
@endphp
<div class="col-md-4">
    <div class="card">
        <a href="{{route('athlete.gettingStarted.detail',['id'=>$video->id])}}">
            <!-- data-lity -->
            @if( !empty($video->media) && !empty($video->media->base_url))
            <img class="card-img-top" src="{{$video->media->base_url}}" alt="{{$video->title}}">
            @else
            <img class="card-img-top" src="{{ url('assets/images/default-image.png') }}" alt="{{$video->title}}">
            @endif
        </a>

        <div class="card-body">
            <h5 class="card-title">
                <a href="{{route('athlete.gettingStarted.detail',['id'=>$video->id])}}" data-toggle="tooltip" data-placement="top" title="{{$video->title}}">
                    {{ucfirst($video->title)}}
                </a>
            </h5>
            <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
          
        </div>
    </div>
</div>
@php $i++; @endphp
@endforeach

@else
<div class="alert alert-danger" role="alert">
    Oops. No Videos Found. Try again!
</div>
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
       
        $('[data-toggle="tooltip"]').tooltip();

    });

</script>