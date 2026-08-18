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
                icon="building-community"
                :title="__('Thêm Loại hình Phòng khám mới')"
                :subtitle="__('Nhập thông tin phân loại cơ sở y tế và phòng khám')"
                :back-route="route(RouteAdminSystem::CLINIC_TYPE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::CLINIC_TYPE_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.clinicType.forms.create-left')
                    @include('admin.clinicType.forms.create-right')
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
