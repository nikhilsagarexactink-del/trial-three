@php $userType = userType(); @endphp
@if(!empty($data) && count($data)>0)
@foreach($data as $sub)
<tr>
    <td>{{ucfirst($sub['plan_name']) ? ucfirst($sub['plan_name']) : "-"}}</td>
    <td>{{ucfirst($sub['subscription_type'])}}</td>
    <td>{{($sub['subscription_type'] == 'monthly') ? "$".number_format($sub['cost_per_month'], 2) : "$".number_format($sub['cost_per_year'], 2) }}</td>
    <td> {{date('m-d-Y', strtotime($sub['subscription_date']))}}</td>
    <td>{{ucfirst($sub['stripe_status'])}}</td>
    <td>
        <div class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="iconmoon-ellipse"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @if($userType != 'admin') 
                    <a class="dropdown-item" onClick="deleteDowngrade('{{$sub['id']}}')" href="javascript:void(0);">Delete</a>
                @endif
            </div>
        </div>
    </td>
</tr>
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
                loadDowngrade(pageLink);
            }
        });
    });
</script>