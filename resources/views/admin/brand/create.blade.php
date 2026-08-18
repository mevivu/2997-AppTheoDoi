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
                icon="building-store"
                :title="__('Thêm Thương hiệu mới')"
                :subtitle="__('Nhập thông tin thương hiệu và đối tác cung cấp')"
                :back-route="route(RouteAdminSystem::BRAND_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::BRAND_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.brand.forms.create-left')
                    @include('admin.brand.forms.create-right')
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
    @include('admin.brand.scripts.script')
@endpush
