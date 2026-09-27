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
                icon="ti ti-folder-plus"
                :title="__('Thêm Danh mục bài học mới')"
                :subtitle="__('Phân nhóm bài học theo nhóm độ tuổi và 5 trụ cột giáo dục')"
                :back-route="route(RouteAdminSystem::LESSON_CATEGORY_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::LESSON_CATEGORY_STORE)" type="post" :validate="true" :has-files="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.lesson_categories.forms.create-left')
                    @include('admin.lesson_categories.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
