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
                :title="__('Thêm Gói cước / Dịch vụ mới')"
                :subtitle="__('Nhập thông tin chi tiết về gói cước và quyền lợi dịch vụ')"
                :back-route="route(RouteAdminSystem::PACKAGE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::PACKAGE_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.package.forms.create-left')
                    @include('admin.package.forms.create-right')
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
    @include('admin.package.scripts.script')
@endpush
