<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
<meta name="X-TOKEN" content="{{ csrf_token() }}">
<meta name="url-home" content="{{ url('/') }}">
<meta name="currency" content="{{ config('custom.currency') }}">
<meta name="position_currency" content="{{ config('custom.format.position_currency') }}">
<title>@yield('title', 'Admin')</title>
<link rel="manifest" href="{{ route('admin.manifest') }}">
<meta name="theme-color" content="#2563eb">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Chăm Con Admin">
<link rel="apple-touch-icon" href="{{ asset('/public/admin/assets/images/admin-icon-192.png') }}">
@php
    $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
    $settings = $settingRepository->getAll();
    $siteLogo = $settings->where('setting_key', 'site_logo')->first()?->plain_value
        ?? \App\Traits\ImageSystem::DEFAULT_IMAGE;
@endphp
<link rel="shortcut icon" type="image/x-icon" href="{{ asset($siteLogo) }}" />
<!-- CSS files -->
<link href="{{ asset('/public/libs/tabler/dist/css/tabler.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('/public/libs/tabler/dist/css/tabler-vendors.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('public/libs/tabler/plugins/tabler-icon/webfont/tabler-icons.min.css') }}" rel="stylesheet"type="text/css">
<link href="{{ asset('public/libs/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet"type="text/css">
<link href="{{ asset('public/libs/Parsley.js-2.9.2/style.css') }}" rel="stylesheet">
<!-- datatable -->
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('/public/libs/datatables/plugins/bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/public/libs/datatables/plugins/buttons/css/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/public/libs/datatables/plugins/responsive/css/responsive.bootstrap5.min.css') }}">
<style>
    @import url('https://rsms.me/inter/inter.css');
    :root {
    --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }
    body {
    font-feature-settings: "cv03", "cv04", "cv11";
    }
</style>
<link href="{{ asset('public/admin/assets/css/style.css') }}?v=1.0.2" rel="stylesheet">
@stack('libs-css')
@stack('custom-css')
