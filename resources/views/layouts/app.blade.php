<!DOCTYPE html>
<html>

<head>
    <!-- css links & meta datas -->
    @include('layouts.header-links')
    @yield('head')
</head>

<body class="{{ !Auth::guard('web')->check() ? 'sidebar-collapse' : ''}}">
    <div id="app">
        <!-- header -->
        @include('layouts.header')
        <!-- sidebar -->
        @yield('content')
    </div>
    @include('partials.global-prompt')
    <!-- js links -->
    @include('layouts.footer-links')

    @yield('js')
</body>

</html>