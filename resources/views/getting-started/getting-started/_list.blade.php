@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $video)
        <tr data-id="{{$video->id }}">
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ $video->title }}</td>
            <td>{{ $video->video_url }}</td>
            <td>{{ $video->provider_type }}</td>
            <td>
            <span id="text_limit_{{ $video->id }}"
            style="{{ strlen(strip_tags($video->description)) > 50 ? '' : 'display:none' }}">{{ \Illuminate\Support\Str::limit(strip_tags($video->description), 50, '...')}}
                    <a href="javascript:void(0)" onClick="readMore({{ $video }})">Read More</a></span>
                <span id="text_all_{{ $video->id }}"
                    style="{{ strlen(strip_tags($video->description)) < 50 ? '' : 'display:none' }}">{!! $video->description !!}</span>
            </td>
            <td>
                @if ($video->status == 'active')
                    <span class="text-success">Active</span>
                @elseif($video->status == 'inactive')
                    <span class="text-muted">Inactive</span>
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
                    @if ($userType == 'admin')
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"
                                href="{{ route('user.gettingStarted.editForm', ['id' => $video->id, 'user_type' => $userType]) }}">Edit</a>
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
                        </div>
                    @endif
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
