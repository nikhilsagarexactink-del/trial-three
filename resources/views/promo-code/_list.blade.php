@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    
    @foreach ($data as $promoCode)
  
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ $promoCode->code }}</td>
            <td>{{ ucfirst($promoCode->no_of_users_allowed) }}</td>
            <td>{{ formatDate($promoCode->expiration_date, 'm-d-Y') }}</td>
            <td>{{ ucfirst($promoCode->discount_type) }}</td>
            <td>{{ $promoCode->discount_amount ? $promoCode->discount_amount : '--' }}</td>
            <td>{{ $promoCode->discount_percentage ? $promoCode->discount_percentage : '--' }}</td>
            <td> {{ $promoCode->plans->isNotEmpty() ? $promoCode->plans->pluck('plan.name')->unique()->map(fn($plan) => ucwords($plan))->implode(', ') : '--' }} </td>
            <td>   {{ $promoCode->plans->isNotEmpty() ? $promoCode->plans->pluck('plan_type')->unique()->map(fn($plan) => ucwords($plan))->implode(', ') : '--' }}</td>
            <td>
                @if ($promoCode->status == 'active')
                    <span class="text-success">Active</span>
                @elseif($promoCode->status == 'inactive')
                    <span class="text-danger">Inactive</span>
                @elseif($promoCode->status == 'deleted')
                    <span class="text-danger">Delete</span>
                @endif
            </td>
            <td class="align-middle">
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"
                            href="{{ route('user.promoCode.edit', ['id' => $promoCode->id, 'user_type' => $userType]) }}">Edit</a>
                        @if ($promoCode->status == 'active')
                            <a class="dropdown-item" onClick="changeStatus('{{ $promoCode->id }}','inactive')"
                                href="javascript:void(0);">Inactive </a>
                        @endif
                        @if ($promoCode->status == 'inactive')
                            <a class="dropdown-item" onClick="changeStatus('{{ $promoCode->id }}','active')"
                                href="javascript:void(0);">Active</a>
                        @endif
                        <a class="dropdown-item" onClick="changeStatus('{{ $promoCode->id }}','deleted')"
                            href="javascript:void(0);">Delete</a>
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
                loadPromoCodeList(pageLink);
            }
        });
    });
</script>
