<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('public.layouts.head')
</head>

<body>
    <div class="page">
        @include('public.layouts.header')

        <main>
            <div class="overlay"></div>
            @yield('content')

        </main>

        @include('public.layouts.footer')


        @include('public.layouts.scripts')

        <x-alert />
    </div>

</body>

</html>
