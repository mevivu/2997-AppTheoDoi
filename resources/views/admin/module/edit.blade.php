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
                icon="box"
                :title="__('Chỉnh sửa Module')"
                :subtitle="$module->name ?? __('Cập nhật thông tin chi tiết module')"
                :back-route="route(RouteAdminSystem::MODULE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::MODULE_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$module->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.module.forms.edit-left')
                    @include('admin.module.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush

@push('custom-js')
@endpush
