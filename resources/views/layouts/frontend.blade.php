<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('components.header')

<body>
<div >

<div id="doro-page"> <a href="#" class="js-doro-nav-toggle doro-nav-toggle"><i></i></a>

    <!-- Main Section -->
    <div id="doro-main">
        @yield('content')
    </div>
</div>
</div>
@include('components.footer')

</body>
</html>
