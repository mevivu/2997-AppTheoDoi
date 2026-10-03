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
                :title="__('Chỉnh sửa tiêu chuẩn tuần thai')"
                :subtitle="__('Tuần :week', ['week' => $response->week])"
                :back-route="route(RouteAdminSystem::FETAL_GROWTH_STANDARD_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::FETAL_GROWTH_STANDARD_UPDATE)" type="put" :validate="true">
                <input type="hidden" name="id" value="{{ $response->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.fetal_growth_standards.forms.edit-left')
                    @include('admin.fetal_growth_standards.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
