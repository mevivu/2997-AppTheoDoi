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
                :title="__('Thêm Module mới')"
                :subtitle="__('Khai báo module hệ thống để quản lý phân quyền và tính năng')"
                :back-route="route(RouteAdminSystem::MODULE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::MODULE_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.module.forms.create-left')
                    @include('admin.module.forms.create-right')
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
