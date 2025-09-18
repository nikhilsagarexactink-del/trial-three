@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp

    @foreach ($data as $upsell)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ $upsell->title }}</td>
            <td>{!!$upsell->message!!}</td>
            <td> {{ $upsell->start_date != null ? date('m-d-Y', strtotime($upsell->start_date)) : '-'}}</td>
            <td> {{ $upsell->end_date != null ? date('m-d-Y', strtotime($upsell->end_date)) : '-'}}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $upsell->frequency)) }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $upsell->location))  }}</td>
            <td>
                @if ($upsell->status == 'published')
                    <span class="text-success">Published</span>
                @elseif($upsell->status == 'draft')
                    <span class="text-muted">Draft</span>
                @elseif($upsell->status == 'deleted')
                    <span class="text-danger">Delete</span>
                @endif
            </td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    @if ($userType == 'admin')
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"
                                href="{{ route('user.editUpsell', ['id' => $upsell->id, 'user_type' => $userType]) }}">Edit</a>
                            @if ($upsell->status == 'published')
                                <a class="dropdown-item" onClick="changeStatus('{{ $upsell->id }}','draft')"
                                    href="javascript:void(0);">Draft </a>
                            @endif
                            @if ($upsell->status == 'draft')
                                <a class="dropdown-item" onClick="changeStatus('{{ $upsell->id }}','published')"
                                    href="javascript:void(0);">Published</a>
                            @endif
                            <a class="dropdown-item" onClick="changeStatus('{{ $upsell->id }}','deleted')"
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
