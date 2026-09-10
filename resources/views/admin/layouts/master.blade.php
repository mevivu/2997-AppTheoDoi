<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('admin.layouts.head')
</head>

<body>
<div class="page">
    <x-admin-sidebar-left/>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    @include('admin.layouts.sidebar-top')
    <div class="page-wrapper">
        @section('breadcrumbs')
            @include('admin.layouts.partials.breadcrumbs')
        @show
        @yield('content')
        @include('admin.layouts.footer')
        @include('admin.layouts.modal.modal-logout')
        @include('admin.layouts.modal.modal-delete')
        @include('admin.layouts.modal.modal-force-delete')
    </div>

</div>

<!-- Back to top button -->
<div class="back-to-top" id="backToTop" title="Lên đầu trang">
    <i class="ti ti-arrow-up"></i>
</div>

@include('admin.layouts.scripts')
@include('admin.notifications.scripts.firebase-script')

<x-alert/>
</body>

</html>
