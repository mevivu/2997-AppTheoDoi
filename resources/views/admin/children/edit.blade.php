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
                icon="baby-carriage"
                :title="__('Chỉnh sửa Hồ sơ Trẻ')"
                :subtitle="$children->name ?? __('Cập nhật thông tin chi tiết và tình trạng của trẻ')"
                :back-route="route(RouteAdminSystem::CHILDREN_INDEX)"
            />

            <x-form id="notificationForm" :action="route(RouteAdminSystem::CHILDREN_UPDATE)" type="put" :validate="true">
                <input type="hidden" name="id" value="{{ $children->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.children.forms.edit-left', ['children' => $children])
                    @include('admin.children.forms.edit-right', ['children' => $children])
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <!-- button in datatable -->
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.children.scripts.scripts')
@endpush
