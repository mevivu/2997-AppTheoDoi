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
                icon="slideshow"
                :title="__('Chỉnh sửa Slider / Banner')"
                :subtitle="$slider->name ?? __('Cập nhật thông tin chi tiết slider')"
                :back-route="route(RouteAdminSystem::SLIDER_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::SLIDER_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$slider->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.sliders.forms.edit-left')
                    @include('admin.sliders.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
