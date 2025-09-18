@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $quote)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ ucfirst($quote->quote_type) }}</td>
            <td>{{ ucfirst($quote->author) }}</td>
            <td>
                <span id="text_limit_{{ $quote->id }}"
                    style="{{ strlen(strip_tags($quote->description)) > 50 ? '' : 'display:none' }}">{{ \Illuminate\Support\Str::limit($quote->description, 50, '...') }}
                    <a href="javascript:void(0)" onClick="readMore({{ $quote }})">Read More</a></span>
                <span id="text_all_{{ $quote->id }}"
                    style="{{ strlen(strip_tags($quote->description)) < 50 ? '' : 'display:none' }}">{{ $quote->description }}</span>
            </td>
            <td>
                @if ($quote->status == 'active')
                    <span class="text-success">Active</span>
                @elseif($quote->status == 'inactive')
                    <span class="text-danger">Inactive</span>
                @elseif($quote->status == 'deleted')
                    <span class="text-danger">Delete</span>
                @endif
            </td>
            <td class="align-middle">
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    @if ($userType == 'admin')
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"
                                href="{{ route('user.editQuoteForm', ['id' => $quote->id, 'user_type' => $userType]) }}">Edit</a>
                            @if ($quote->status == 'active')
                                <a class="dropdown-item" onClick="changeStatus('{{ $quote->id }}','inactive')"
                                    href="javascript:void(0);">Inactive </a>
                            @endif
                            @if ($quote->status == 'inactive')
                                <a class="dropdown-item" onClick="changeStatus('{{ $quote->id }}','active')"
                                    href="javascript:void(0);">Active</a>
                            @endif
                            <a class="dropdown-item" onClick="changeStatus('{{ $quote->id }}','deleted')"
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
                loadQuoteList(pageLink);
            }
        });
    });
</script>
