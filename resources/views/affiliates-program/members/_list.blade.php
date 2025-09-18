@php 
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $affiliate)
        <tr>  
            <td>{{getSerialNo($i, $currentPage)}}</td>
            <td>{{$affiliate->user->first_name}} {{$affiliate->user->last_name}}</td>
            <td>{{$affiliate->user->email}}</td>
            <td>{{!empty($affiliate->user->userSubsription) ? ucfirst($affiliate->user->userSubsription->plan_name) : ''}}</td>
            <td>{{getLocalDateTime($affiliate->terms_agreed_at, 'm-d-Y g:i A')}}</td>
            <td>${{$affiliate->total_earnings > 0 ? $affiliate->total_earnings : 0}}</td>
            <td>        
                @if($affiliate->status == 'pending')
                <span class="text-muted">Pending</span>
                @elseif($affiliate->status == 'approved')
                <span class="text-success">Approved</span>
                @elseif($affiliate->status == 'denied')
                <span class="text-danger">Denied</span>
                @endif
            </td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @if($userType = 'admin')
                        <!-- <a class="dropdown-item" href="{{route('user.editUserForm', ['id' => $affiliate->id, 'user_type'=>$userType])}}">Edit</a> -->
                        @if($affiliate->status == 'pending')
                            <a class="dropdown-item" onClick="changeStatus('{{$affiliate->id}}','approved')" href="javascript:void(0);">Approve </a>
                            <a class="dropdown-item" onClick="changeStatus('{{$affiliate->id}}','denied')" href="javascript:void(0);">Deny </a>
                        @endif 
                        @if($affiliate->status == 'approved')
                            <a class="dropdown-item" onClick="changeStatus('{{$affiliate->id}}','denied')" href="javascript:void(0);" >Deny</a>
                        @endif
                        @if($affiliate->status == 'denied')
                            <a class="dropdown-item" onClick="changeStatus('{{$affiliate->id}}','approved')" href="javascript:void(0);">Approve </a>
                        @endif
                        @if($affiliate->status == 'approved' && $affiliate->total_earnings >= 100 )
                            <a class="dropdown-item" onClick="logPayout('{{$affiliate->user_id}}' , '{{$affiliate->total_earnings}}' , '{{$affiliate->userPayoutSetting  ? $affiliate->userPayoutSetting->payout_method : ''}}')"  href="javascript:void(0);" >Log Payout </a>
                        @endif
                        @if($affiliate->status == 'approved')
                            <a class="dropdown-item" href="{{route('user.payoutHistoryIndex', ['id' => $affiliate->user_id,'user_type'=>$userType])}}">Payout History</a>
                        @endif
                        <a class="dropdown-item" onClick="changeStatus('{{$affiliate->id}}','deleted')" href="javascript:void(0);">Delete</a>
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