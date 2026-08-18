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
                icon="scale"
                :title="__('Chỉnh sửa Chỉ số BMI')"
                :subtitle="__('Cập nhật thang đo tiêu chuẩn BMI')"
                :back-route="route(RouteAdminSystem::BMI_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::BMI_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$response->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.bmi.forms.edit-left')
                    @include('admin.bmi.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
