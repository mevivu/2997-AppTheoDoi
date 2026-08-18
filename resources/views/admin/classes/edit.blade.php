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
                icon="school"
                :title="__('Chỉnh sửa Lớp học')"
                :subtitle="$response->name ?? __('Cập nhật thông tin chi tiết lớp học')"
                :back-route="route(RouteAdminSystem::CLASSES_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::CLASSES_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$response->id"/>
                <div class="row g-4 justify-content-center">
                    @include('admin.classes.forms.edit-left')
                    @include('admin.classes.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.classes.scripts.scripts')
@endpush
