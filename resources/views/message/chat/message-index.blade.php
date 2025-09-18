@extends('layouts.app')
<title>Messages</title>
@section('content')
@include('layouts.sidebar')
@php
$toUserId = request()->route('toUserId');
$categoryId = request()->route('categoryId');
$userType = userType();
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
  <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
    <div class="left-side">
      <!-- Breadcrumb Start -->
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
          <!-- <li class="breadcrumb-item"><a href="{{route('user.messages', ['user_type'=>$userType])}}">Messages</a></li> -->
          <li class="breadcrumb-item active">Chat</li>
        </ol>
      </nav>
      <!-- Breadcrumb End -->
      <!-- Page Title Start -->
      <h2 class="page-title text-capitalize mb-0">
        Message
        @if(!empty($category))
        ({{$category->name}})
        @endif
      </h2>
      <!-- Page Title End -->
    </div>
  </div>
  <div class="bg-white py-5 px-4">
    <div class="container">
      <div class="card direct-chat direct-chat-danger">
        <div class="card-header">
          <h3 class="card-title">Direct Chat</h3>
          <!-- <div class="card-tools">
        <span data-toggle="tooltip" title="3 New Messages" class="badge badge-light">3</span>
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
        <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
          <i class="fas fa-comments"></i>
        </button>
        <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
      </div> -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <div class="direct-chat-messages" id="chatList">
          </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer chat-form">
          <form action="#" method="POST">
            <div class="input-group">
              <textarea name="message" id="messageFieldId" placeholder="Type Message ..." class="form-control chattextarea"></textarea>
              <span class="input-group-append mt-3 form-btn">
                <input type="file" id="imgupload" onchange="saveImage()" style="display:none" />
                <a href="javascript:void(0)"><img id="OpenImgUpload" src="{{ url('assets/images/attachment.svg') }}" class="" alt="" height="25px" width="35px"></a>
                <button type="button" class="btn btn-dark" id="sendBtn" onClick="sendMessage()">Send <span id="sendBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
              </span>
            </div>
          </form>

        </div>
        <!-- /.card-footer-->
      </div>
      <!--/.direct-chat -->
    </div>
  </div>


</div>
<!-- Main Content Start -->

@endsection
@section('js')

<script>
  tinymce.init({
    selector: ".chattextarea",
    menubar: false,
    statusbar: false,
    toolbar_location: "bottom",
    plugins: ['emoticons', 'link'],
    //autoresize_bottom_margin: 0,
    // max_height: 600,
    placeholder: "Enter message . . .",
    toolbar: "bold italic strikethrough link numlist bullist blockquote emoticons image",
  });
  $(document).ready(function() {
    loadThreadChatList();
  });
  /**
   * Load user chat list.
   * @request search, status
   * @response object.
   */
  function loadThreadChatList(url) {
    $("#chatList").html('{{ajaxListLoader()}}');
    url = url || "{{route('common.loadChatList',['toUserId'=>$toUserId])}}";
    var formData = $('#searchFilterForm').serialize();
    $.ajax({
      type: "GET",
      url: url,
      data: {
        categoryId: "{{$categoryId}}",
        user_id: "{{$toUserId}}"
      },
      success: function(response) {
        if (response.success) {
          $("#chatList").html("");
          $('#chatList').append(response.data.html);
        }
      },
      error: function() {
        _toast.error('Something went wrong.');
      }
    });
  }

  function scrollBottom() {
    $(".direct-chat-messages").animate({
      scrollTop: $(".direct-chat-messages")[0].scrollHeight
    }, 1000);
  }

  /**
   * Save message
   * @request search, status
   * @response object.
   */
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('messageFieldId').addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        event.preventDefault();
      }
    });
  });

  function sendMessage(media = '') {
    let message = tinymce.get('messageFieldId').getContent(); //$("#messageFieldId").val();    
    if (message || media) {
      $('#sendBtn').prop('disabled', true);
      $('#sendBtnLoader').show();
      $.ajax({
        type: "POST",
        url: "{{route('common.sendMessage')}}",
        data: {
          user_id: "{{$toUserId}}",
          categoryId: "{{$categoryId}}",
          message: message ? message : 'file',
          media_id: media ? media.id : '',
          message_type: media ? 'file' : 'text'
        },
        success: function(response) {
          $('#sendBtn').prop('disabled', false);
          $('#sendBtnLoader').hide();
          loadThreadChatList();
          if (response.success) {
            tinymce.get("messageFieldId").setContent('');
            scrollBottom();
            let data = response.data;
            let messageHtml = '';
            if (data.message_type == 'text') {
              messageHtml = `<div class="direct-chat-msg right">
                                  <img class="direct-chat-img" src="${data.from_user_image}" alt="message user image">
                                  <div>
                                      <div class="direct-chat-text">
                                          <h5 class="direct-chat-name">${data.from_user_name}</h5>
                                          ${data.message}
                                      </div>
                                      <span class="direct-chat-timestamp">${getLocalDateTime($data.created_at, 'm-d-Y g:i A')}</span>
                                  </div>
                                </div>`;
              $('#chatList').append(messageHtml);
            } else {
              let fileLink = `<span>File <a href="javascript:void(0)" onclick="mediaNotFound()"><i class="fa fa-download" aria-hidden="true"></i></a></span>`;
              if (data.media && data.media.base_url) {
                fileLink = `<span>File <a href="` + data.media.base_url + `" download="` + data.media.base_url + `"><i class="fa fa-download" aria-hidden="true"></i></a></span>`;
              }
              messageHtml = `<div class="direct-chat-msg right">
                                  <img class="direct-chat-img" src="${data.from_user_image}" alt="message user image">
                                  <div>
                                      <div class="direct-chat-text">
                                          <h5 class="direct-chat-name">${data.from_user_name}</h5>
                                          <span>` + fileLink + `</span>
                                      </div>
                                      <span class="direct-chat-timestamp">${data.created_at}</span>
                                  </div>
                                </div>`;
              $('#chatList').append(messageHtml);
            }
            //$("#messageFieldId").val('');
          }
        },
        error: function() {
          $('#sendBtn').prop('disabled', false);
          $('#sendBtnLoader').hide();
          _toast.error('Somthing went wrong.');
        }
      });
    }
  }

  /**
   * Save message file
   * @request form fields
   * @response object.
   */
  function saveImage() {
    var filename = $("#imgupload").val();
    var extension = filename.replace(/^.*\./, '');
    extension = extension.toLowerCase();
    if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension == 'svg' || extension == 'mpeg' || extension == 'pdf') {
      var fileObj = document.getElementById('imgupload').files[0];
      var formData = new FormData();
      formData.append('file', fileObj);
      formData.append('mediaFor', 'messages');
      formData.append('_token', "{{csrf_token()}}");
      $('#sendBtn').prop('disabled', true);
      $('#sendBtnLoader').show();
      $.ajax({
        type: "POST",
        url: "{{route('common.saveMultipartMedia')}}",
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          // $('#updateBtn').prop('disabled', false);
          // $('#updateBtnLoader').hide();
          $('#imgupload').val('');
          if (response.success) {
            _toast.success(response.message);
            sendMessage(response.data);
            console.log(response.data);
          } else {
            _toast.error('Somthing went wrong. please try again');
          }
        },
        error: function(err) {
          $('#imgupload').val('');
          $('#updateBtn').prop('disabled', false);
          $('#updateBtnLoader').hide();
          if (err.status === 422) {
            var errors = $.parseJSON(err.responseText);
            $.each(errors.errors, function(key, val) {
              $("#" + key + "-error").text(val);
            });
          } else {
            _toast.error('Please try again.');
          }
        },
      });
    } else {
      _toast.error("Please select jpg, jpeg, png, pdf image only.");
    }
  };

  function mediaNotFound() {
    _toast.error('File not found.');
  }

  $('#OpenImgUpload').click(function() {
    $('#imgupload').trigger('click');
  });
</script>
@endsection