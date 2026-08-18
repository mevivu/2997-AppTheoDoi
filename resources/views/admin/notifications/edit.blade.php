@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="bell"
                :title="__('Chỉnh sửa Thông báo')"
                :subtitle="$notification->title ?? __('Cập nhật thông tin chi tiết thông báo')"
                :back-route="route(RouteAdminSystem::NOTIFICATION_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::NOTIFICATION_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$notification->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.notifications.forms.edit-left')
                    @include('admin.notifications.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.notifications.scripts.scripts')
@endpush
