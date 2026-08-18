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
                icon="user-plus"
                :title="__('Thêm Khách hàng mới')"
                :subtitle="__('Nhập đầy đủ thông tin hồ sơ để khởi tạo tài khoản khách hàng mới')"
                :back-route="route(RouteAdminSystem::USER_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::USER_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.users.forms.create-left')
                    @include('admin.users.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <!-- button in datatable -->
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/'.trans()->getLocale().'.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.layouts.modal.modal-pick-address')
    @include('admin.scripts.google-map-input')
@endpush
