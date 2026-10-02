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
                icon="category"
                :title="__('Chỉnh sửa Danh mục Chuyên đề')"
                :subtitle="$instance->name"
                :back-route="route(RouteAdminSystem::EXPERT_CATEGORY_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::EXPERT_CATEGORY_UPDATE)" type="put" :validate="true">
                <input type="hidden" name="id" value="{{ $instance->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.expert_categories.forms.edit-left')
                    @include('admin.expert_categories.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
@endpush
