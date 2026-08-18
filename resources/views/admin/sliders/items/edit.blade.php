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
                icon="photo"
                :title="__('Chỉnh sửa Banner Slider')"
                :subtitle="$sliderItem->title ?? __('Cập nhật thông tin chi tiết item của slider')"
                :back-route="route('admin.slider.item.index', ['slider_id' => $sliderItem->slider->id])"
            />

            <x-form :action="route('admin.slider.item.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$sliderItem->id" />
                <x-input type="hidden" name="slider_id" :value="$sliderItem->slider_id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.sliders.items.forms.edit-left')
                    @include('admin.sliders.items.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
@endpush