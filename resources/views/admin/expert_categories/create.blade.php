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
                icon="category-plus"
                :title="__('Thêm Danh mục Chuyên đề mới')"
                :subtitle="__('Nhập tên danh mục, icon đại diện và mô tả chuyên môn')"
                :back-route="route(RouteAdminSystem::EXPERT_CATEGORY_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::EXPERT_CATEGORY_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.expert_categories.forms.create-left')
                    @include('admin.expert_categories.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
@endpush
