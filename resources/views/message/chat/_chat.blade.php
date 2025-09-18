 <!-- Conversations are loaded here -->
 @php
 $userData = getUser();
 @endphp

 @foreach($data as $message)
 @php $mediaUrl = !empty($message['file_name']) ? getFileUrl($message['file_name'], 'messages') : ''; @endphp
 <!-- Message. Default to the left -->
 @if($message['created_by']==$userData->id)
 <!-- Message to the right -->
 <div class="direct-chat-msg right">
     <!-- /.direct-chat-infos -->
     <img class="direct-chat-img" src="{{getUserImage($message['from_user_image'], 'profile-pictures')}}" alt="message user image">
     <!-- /.direct-chat-img -->
     <div>
        <div class="direct-chat-text">
            <h5 class="direct-chat-name">{{ucfirst($message['message_by'])}}</h5>
            @if($message['message_type'] == 'text')
                <span>{!! $message['message'] !!}</span>
            @elseif($message['message_type'] == 'file' && !empty($mediaUrl))
                <span>File <a href="{{$mediaUrl}}" download="{{$mediaUrl}}"><i class="fa fa-download" aria-hidden="true"></i></a></span>
            @else
                <span>File <a href="javascript:void(0)" onclick="mediaNotFound()"><i class="fa fa-download" aria-hidden="true"></i></a></span>
            @endif
        </div>
        <span class="direct-chat-timestamp">{{createdAtTimezone($message['created_at'])}}</span>
     </div>
     <!-- /.direct-chat-text -->
 </div>
 @else
 <div class="direct-chat-msg">    
     <!-- /.direct-chat-infos -->
     <img class="direct-chat-img" src="{{getUserImage($message['to_user_image'], 'profile-pictures')}}" alt="message user image">
     <!-- /.direct-chat-img -->
     <div>
         <div class="direct-chat-text">
            <h5 class="direct-chat-name">{{ucfirst($message['message_by'])}}</h5>
            <span> {!! $message['message'] !!}</span>
         </div>
         <span class="direct-chat-timestamp">{{createdAtTimezone($message['created_at'])}}</span>
     </div>

     <!-- /.direct-chat-text -->
 </div>
 @endif
 @endforeach
 <script>
     $(document).ready(function() {
         scrollBottom();
     });
 </script>