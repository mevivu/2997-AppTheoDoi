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
                icon="notebook"
                :title="__('Thêm Môn học mới')"
                :subtitle="__('Nhập thông tin môn học và danh mục đào tạo')"
                :back-route="route(RouteAdminSystem::SUBJECT_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::SUBJECT_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.subject.forms.create-left')
                    @include('admin.subject.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.subject.scripts.scripts')
@endpush
