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
                icon="shield-lock"
                :title="__('Thêm Vai trò mới')"
                :subtitle="__('Thiết lập vai trò mới và phân bổ quyền hạn tương ứng trong hệ thống')"
                :back-route="route(RouteAdminSystem::ROLE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::ROLE_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.role.forms.create-left')
                    @include('admin.role.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
    @include('admin.role.scripts.selectAllPermissions')
@endpush
