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
                :title="__('Thêm Slider / Banner mới')"
                :subtitle="__('Nhập thông tin nhóm banner trình chiếu')"
                :back-route="route(RouteAdminSystem::SLIDER_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::SLIDER_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.sliders.forms.create-left')
                    @include('admin.sliders.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
