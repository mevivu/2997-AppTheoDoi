@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="chart-arrows"
                :title="__('Thêm Tiêu chí Đánh giá PQ mới')"
                :subtitle="__('Nhập thông tin tiêu chuẩn đánh giá thể chất và phát triển PQ')"
                :back-route="route(RouteAdminSystem::RATING_PQ_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::RATING_PQ_STORE)" type="post" :validate="true">
                <input type="hidden" name="device_token" value="">
                <div class="row g-4 justify-content-center">
                    @include('admin.ratingPQ.forms.create-left')
                    @include('admin.ratingPQ.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.ratingPQ.scripts.scripts')
@endpush
