@if(!empty($data) && count($data)>0)
@php $i=0; $currentPage = $data->currentPage(); @endphp
<ul class="default-profile-list">
    <li>
        <div class="default-profile-upload">
            <input type="hidden" id="uploadImageUrl" value="{{route('common.saveImage')}}">
            <input type="hidden" id="mediaFor" value="default-profile-pictures">
            <input type="file" id="UploadImg" onchange="setImage(this)" class="profile-upload-input" name="profile_picture">
            <div class="profile-upload-text">
                <img src="{{ asset('assets/images/file-upload.svg') }}" alt="">
                <p>Drag and drop file here</p>
                <span>-OR-</span>
                <a class="btn btn-secondary" href="javascript:void(0)">Browse Files</a>
            </div>
        </div>
    </li>
    @foreach($data as $image)
    <li>
        <div class="default-profile-card">
            @if(!empty($image->media) && !empty($image->media->base_url))
            <img class="default-profile-img" src="{{$image->media->base_url}}">
            @else
            <img class="default-profile-img" src="{{ asset('assets/images/default-user.jpg') }}" id="imagePreview" alt="user-img">
            @endif
            <div class="status-icons">
                <a href="javascript:void(0)" onClick="changeStatus({{$image->id}},'deleted')"><i class="fa fa-trash"></i></a>
                @if($image->status == 'active')
                <a href="javascript:void(0)" onClick="changeStatus({{$image->id}},'inactive')"><i class="fas fa-toggle-on"></i></a>
                @else
                <a href="javascript:void(0)" onClick="changeStatus({{$image->id}},'active')"><i class="fas fa-toggle-off"></i></a>
            </div>
            @endif
        </div>
    </li>
    @php $i++; @endphp
    @endforeach
</ul>

@else
<div class="alert alert-danger" role="alert">
    No Record Found.
</div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadImageList(pageLink);
            }
        });
    });
</script>