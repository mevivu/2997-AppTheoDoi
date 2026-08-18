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
                icon="syringe"
                :title="__('Chỉnh sửa Loại Vắc xin')"
                :subtitle="$instance->name ?? __('Cập nhật thông tin chi tiết loại vắc xin')"
                :back-route="route(RouteAdminSystem::VACCINATION_TYPE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::VACCINATION_TYPE_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$instance->id"/>
                <div class="row g-4 justify-content-center">
                    @include('admin.vaccinationType.forms.edit-left')
                    @include('admin.vaccinationType.forms.edit-right')
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
@endpush

@push('custom-js')
@endpush
