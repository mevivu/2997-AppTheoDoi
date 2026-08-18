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
                :title="__('Chỉnh sửa Quyền')"
                :subtitle="$permission->title ?? __('Cập nhật thông tin chi tiết của quyền hạn')"
                :back-route="route(RouteAdminSystem::PERMISSION_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::PERMISSION_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$permission->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.permission.forms.edit-left')
                    @include('admin.permission.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
