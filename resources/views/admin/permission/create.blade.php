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
                icon="key"
                :title="__('Thêm Quyền mới')"
                :subtitle="__('Khai báo quyền hạn mới để gán cho các vai trò trong hệ thống')"
                :back-route="route(RouteAdminSystem::PERMISSION_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::PERMISSION_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.permission.forms.create-left')
                    @include('admin.permission.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
