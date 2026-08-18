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
                icon="bulb"
                :title="__('Thêm Năng lực mới')"
                :subtitle="__('Khai báo năng lực đánh giá sự phát triển của trẻ')"
                :back-route="route(RouteAdminSystem::CAPABILITY_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::CAPABILITY_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.capability.forms.create-left')
                    @include('admin.capability.forms.create-right')
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
