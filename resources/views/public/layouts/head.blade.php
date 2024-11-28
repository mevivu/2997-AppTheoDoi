<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="url-home" content="{{ url('/') }}">
    <meta name="currency" content="{{ config('custom.currency') }}">
    <meta name="position_currency" content="{{ config('custom.format.position_currency') }}">
    <title>@yield('title', 'True Love')</title>
    <link rel="shortcut icon" href="{{ asset(config('custom.images.favicon')) }}" type="image/x-icon" />

    <link href="{{ asset('/public/libs/tabler/dist/css/tabler-vendors.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/libs/tabler/plugins/tabler-icon/webfont/tabler-icons.min.css') }}" rel="stylesheet"type="text/css">
    <link href="{{ asset('public/libs/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet"type="text/css">
    <link rel="stylesheet" href="{{ asset('public/libs/fontawesome6/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/jquery-ui.1.12.1.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="{{ asset('public/libs/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/libs/Parsley.js-2.9.2/style.css') }}" rel="stylesheet">



    @stack('libs-css')



    <link rel="stylesheet" href="{{ asset('public/assets/css/styles.css') }}">
    @stack('custom-css')
</head>
