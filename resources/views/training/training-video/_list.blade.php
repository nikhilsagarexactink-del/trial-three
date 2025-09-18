@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
        $userData = getUser();
    @endphp
    @foreach ($data as $video)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ $video->title }}</td>
            <td>{{ $video->video_url }}</td>
            <td>{{ $video->provider_type }}</td>
            <td>{{ $video->is_featured == 1 ? 'Yes' : 'No' }}</td>
            <td>{{ ucwords($video->user_types) }}</td>
            <td>
            <span id="text_limit_{{ $video->id }}"
                    style="{{ strlen(strip_tags($video->description)) > 50 ? '' : 'display:none' }}">{{ \Illuminate\Support\Str::limit(strip_tags($video->description), 50, '...')}}
                    <a href="javascript:void(0)" onClick="readMore({{ $video }})">Read More</a></span>
                <span id="text_all_{{ $video->id }}"
                    style="{{ strlen(strip_tags($video->description)) < 50 ? '' : 'display:none' }}">{!! $video->description !!}</span>
            </td>
            <td>{{ !empty($video->date) ? date('m-d-Y', strtotime($video->date)) : '' }}</td>
            <td>
                @if ($video->status == 'active')
                    <span class="text-success">Active</span>
                @elseif($video->status == 'inactive')
                    <span class="text-danger">Inactive</span>
                @elseif($video->status == 'deleted')
                    <span class="text-danger">Delete</span>
                @endif
            </td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if ($userData->user_type == 'admin')
                            @if (Auth::guard('web')->user()->user_type == 'content-creator')
                                <a class="dropdown-item"
                                    href="{{ route('trainer.editTrainingVideoForm', ['id' => $video->id]) }}">Edit</a>
                            @else
                                <a class="dropdown-item"
                                    href="{{ route('user.editTrainingVideoForm', ['id' => $video->id, 'user_type' => $userType]) }}">Edit</a>
                            @endif

                            @if ($video->status == 'active')
                                <a class="dropdown-item" onClick="changeStatus('{{ $video->id }}','inactive')"
                                    href="javascript:void(0);">Inactive </a>
                            @endif
                            @if ($video->status == 'inactive')
                                <a class="dropdown-item" onClick="changeStatus('{{ $video->id }}','active')"
                                    href="javascript:void(0);">Active</a>
                            @endif
                            <a class="dropdown-item" onClick="changeStatus('{{ $video->id }}','deleted')"
                                href="javascript:void(0);">Delete</a>
                            @if($video->provider_type == 'vimeo')
                                <a class="dropdown-item" onClick="viewStats('{{ $video->id }}', '{{ $video->title }}')" href="javascript:void(0);">View Stats</a>
                            @endif
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
    function readMore(quote) {
        $('#text_limit_' + quote.id).hide();
        $('#text_all_' + quote.id).show();
    };
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
