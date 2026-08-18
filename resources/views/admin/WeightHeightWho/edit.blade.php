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
                icon="ruler-measure"
                :title="__('Chỉnh sửa Chuẩn WHO')"
                :subtitle="__('Cập nhật chỉ số cân nặng và chiều cao theo tiêu chuẩn WHO')"
                :back-route="route(RouteAdminSystem::WHO_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::WHO_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$response->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.WeightHeightWho.forms.edit-left')
                    @include('admin.WeightHeightWho.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
