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
                :title="__('Thêm Chỉ số BMI mới')"
                :subtitle="__('Khai báo chuẩn đánh giá chỉ số BMI theo độ tuổi và giới tính')"
                :back-route="route(RouteAdminSystem::BMI_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::BMI_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.bmi.forms.create-left')
                    @include('admin.bmi.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
