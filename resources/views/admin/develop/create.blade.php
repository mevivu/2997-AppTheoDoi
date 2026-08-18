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
                icon="book"
                :title="__('Thêm Hướng dẫn Phát triển mới')"
                :subtitle="__('Nhập thông tin hướng dẫn theo các giai đoạn phát triển của trẻ')"
                :back-route="route(RouteAdminSystem::DEVELOP_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::DEVELOP_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.develop.forms.create-left')
                    @include('admin.develop.forms.create-right')
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
    @include('admin.develop.scripts.script')
@endpush
