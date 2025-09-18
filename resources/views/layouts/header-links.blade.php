<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="user-timezone"
    content="{{ Auth::check() && Auth::user()->timezone ? Auth::user()->timezone : 'America/Denver' }}">


<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.min.css">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropper/3.0.0/cropper.min.css" type="text/css"> -->
<link rel="stylesheet" href="{{ url('assets/css/cropper/cropper.css') }}" type="text/css">
<link rel="stylesheet" href="{{ url('assets/css/cropper/main.css') }}" type="text/css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/lity/2.4.1/lity.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css"
    rel="stylesheet" />
<!-- Styles -->
<!-- favicon -->
<link rel="icon" type="image/x-icon" href="{{ url('assets/images/favicon.ico') }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ url('plugins/fontawesome-free/css/all.min.css') }}">
<!-- iCheck -->
<link rel="stylesheet" href="{{ url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ url('dist/css/adminlte.min.css') }}">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{ url('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{ url('plugins/daterangepicker/daterangepicker.css') }}">
<!-- Slick slider -->
<link rel="stylesheet" href="{{ url('plugins/slick/slick-theme.css') }}">
<link rel="stylesheet" href="{{ url('plugins/slick/slick.css') }}">
<link rel="stylesheet" href="{{ url('plugins/nouislider/nouislider.min.css') }}">
<!-- Custom style -->
<link rel="stylesheet" href="{{ url('assets/css/main.css') }}">
<!-- summernote -->
<link rel="stylesheet" href="{{ url('plugins/summernote/summernote-bs4.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css"
    type="text/css">
<link href="{{ url('assets/css/app.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.css" rel="stylesheet">
<!-- country code tel -->
</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css">
<script>
    var csrfToken = '{{ csrf_token() }}';
</script>
