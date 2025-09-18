@php 
    $i=0; 
    $currentPage = $data->currentPage();
    $userType = userType();
    $userData = getUser();
    $productStatus = ['new', 'processing', 'shipped', 'completed'];
@endphp
@if (!empty($data) && count($data) > 0)
    @foreach ($data as $product)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ $product->user_name }}</td>
            <td>{{ $product->user_address }}</td>
            <td>{{ $product->user_phone }}</td>
            <td>{{ $product->product->title ?? '-' }}</td>
            <td>{{ $product->points_used }}</td>
            <td>{{ ucfirst($product->product_status) }}</td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if ($userData->user_type == 'admin')
                            @if ($product->product_status != 'processing')
                                <a class="dropdown-item" onClick="changeStatus('{{ $product->id }}','processing')" href="javascript:void(0);">Processing</a>
                            @endif
                            @if($product->product_status != 'completed')
                                <a class="dropdown-item" onClick="changeStatus('{{ $product->id }}','completed')" href="javascript:void(0);">Completed</a>
                            @endif
                            @if($product->product_status != 'shipped')
                                <a class="dropdown-item" onClick="changeStatus('{{ $product->id }}','shipped')" href="javascript:void(0);">Shipped</a>
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