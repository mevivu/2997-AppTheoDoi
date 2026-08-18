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
                :title="__('Thêm Chuẩn WHO mới')"
                :subtitle="__('Nhập thông số cân nặng và chiều cao theo tiêu chuẩn WHO')"
                :back-route="route(RouteAdminSystem::WHO_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::WHO_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.WeightHeightWho.forms.create-left')
                    @include('admin.WeightHeightWho.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
