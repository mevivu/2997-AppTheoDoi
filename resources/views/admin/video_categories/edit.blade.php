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
                icon="ti ti-folder"
                :title="__('Chỉnh sửa Danh mục video')"
                :subtitle="$instance->name ?? __('Cập nhật thông tin danh mục video')"
                :back-route="route(RouteAdminSystem::VIDEO_CATEGORY_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::VIDEO_CATEGORY_UPDATE)" type="put" :validate="true" :has-files="true">
                <x-input type="hidden" name="id" :value="$instance->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.video_categories.forms.edit-left')
                    @include('admin.video_categories.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
