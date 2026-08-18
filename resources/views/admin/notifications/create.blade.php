@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="bell"
                :title="__('Gửi Thông báo mới')"
                :subtitle="__('Soạn nội dung và gửi thông báo đẩy đến người dùng / thiết bị')"
                :back-route="route(RouteAdminSystem::NOTIFICATION_INDEX)"
            />

            <x-form id="notificationForm" :action="route(RouteAdminSystem::NOTIFICATION_STORE)" type="post" :validate="true" enctype="multipart/form-data">
                <input type="hidden" name="device_token" value="">
                <div class="row g-4 justify-content-center">
                    @include('admin.notifications.forms.create-left')
                    @include('admin.notifications.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
    <script src="{{ asset('/public/libs/firebase/firebase.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.notifications.scripts.scripts')
@endpush
