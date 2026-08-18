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
                icon="user-edit"
                :title="__('Chỉnh sửa Admin')"
                :subtitle="$admin->fullname ?? __('Cập nhật thông tin chi tiết tài khoản quản trị viên')"
                :back-route="route(RouteAdminSystem::ADMIN_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::ADMIN_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$admin->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.admins.forms.edit-left')
                    @include('admin.admins.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
