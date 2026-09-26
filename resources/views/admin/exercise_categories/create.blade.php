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
                :title="__('Thêm Danh mục bài tập mới')"
                :subtitle="__('Nhập thông tin danh mục bài tập theo chủ đề PQ, IQ, EQ, AQ, Thai giáo và độ tuổi')"
                :back-route="route(RouteAdminSystem::EXERCISE_CATEGORY_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::EXERCISE_CATEGORY_STORE)" type="post" :validate="true" :has-files="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.exercise_categories.forms.create-left')
                    @include('admin.exercise_categories.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
