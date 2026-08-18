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
                :title="__('Chỉnh sửa Vai trò')"
                :subtitle="$role->title ?? __('Cập nhật thông tin vai trò và danh sách quyền hạn')"
                :back-route="route(RouteAdminSystem::ROLE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::ROLE_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$role->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.role.forms.edit-left')
                    @include('admin.role.forms.edit-right')
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
