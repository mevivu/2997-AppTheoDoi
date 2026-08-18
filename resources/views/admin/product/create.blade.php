@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    <link href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
    <style>
        .pac-container {
            z-index: 99999999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="shopping-cart"
                :title="__('Thêm Sản phẩm mới')"
                :subtitle="__('Nhập thông tin sản phẩm, giá bán và chuyên mục liên quan')"
                :back-route="route(RouteAdminSystem::PRODUCT_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::PRODUCT_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.product.forms.create-left')
                    @include('admin.product.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#brand_id').select2();
        });
    </script>
    @include('ckfinder::setup')
@endpush

@push('custom-js')
@endpush
