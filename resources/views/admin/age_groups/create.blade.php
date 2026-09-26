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
                icon="ti ti-calendar"
                :title="__('Thêm Nhóm tuổi mới')"
                :subtitle="__('Nhập thông tin nhóm độ tuổi cho video giáo dục và bài tập')"
                :back-route="route(RouteAdminSystem::AGE_GROUP_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::AGE_GROUP_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.age_groups.forms.create-left')
                    @include('admin.age_groups.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
