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
                :title="__('Thêm Admin mới')"
                :subtitle="__('Nhập đầy đủ thông tin để khởi tạo tài khoản quản trị viên mới')"
                :back-route="route(RouteAdminSystem::ADMIN_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::ADMIN_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.admins.forms.create-left')
                    @include('admin.admins.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
