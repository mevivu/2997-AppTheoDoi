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
                icon="baby-carriage"
                :title="__('Thêm chỉ số tiêu chuẩn tuần thai')"
                :subtitle="__('Nhập thông tin tuần thai, chiều dài chuẩn (cm), cân nặng chuẩn (g) và mô tả')"
                :back-route="route(RouteAdminSystem::FETAL_GROWTH_STANDARD_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::FETAL_GROWTH_STANDARD_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.fetal_growth_standards.forms.create-left')
                    @include('admin.fetal_growth_standards.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
