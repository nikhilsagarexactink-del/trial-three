@if(!empty($data) && count($data)>0)
@php
$i=0; $currentPage = $data->currentPage();
$userData = getUser();
$userType = userType();
@endphp
@foreach($data as $message)
@php
$toUserId = $message->fromUser->id==$userData->id ? $message->toUser->id : $message->fromUser->id;
@endphp
<tr>
    <td class="no-wrap">{{getSerialNo($i, $currentPage)}}</td>
    <td class="no-wrap">{{!empty($message->fromUser) ? $message->fromUser->first_name.' '.$message->fromUser->last_name : '-'}}</td>
    <td class="no-wrap">{{!empty($message->toUser) ? $message->toUser->first_name.' '.$message->toUser->last_name : '-'}}</td>
    <td>{!!!empty($message->message) ? $message->message->message : ''!!}</td>
    <td>{{!empty($message->category) ? $message->category->name : '-'}}</td>
    <td>
        <a href="{{route('user.messagesIndex', ['user_type'=>$userType, 'toUserId' => $toUserId, 'categoryId'=>((!empty($message->category) ? $message->category->id : ''))])}}">
            <i class="fa fa-eye" aria-hidden="true"></i>
        </a>
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
                loadMessageList(pageLink);
            }
        });
    });
</script>