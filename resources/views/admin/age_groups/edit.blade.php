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
                :title="__('Chỉnh sửa Nhóm tuổi')"
                :subtitle="$instance->name ?? __('Cập nhật thông tin nhóm độ tuổi')"
                :back-route="route(RouteAdminSystem::AGE_GROUP_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::AGE_GROUP_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$instance->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.age_groups.forms.edit-left')
                    @include('admin.age_groups.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
