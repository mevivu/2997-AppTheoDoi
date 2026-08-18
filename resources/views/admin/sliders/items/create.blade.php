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
                :title="__('Thêm Banner vào Slider')"
                :subtitle="__('Nhập thông tin hình ảnh và liên kết cho slider: ') . $slider->name"
                :back-route="route('admin.slider.item.index', ['slider_id' => $slider->id])"
            />

            <x-form :action="route('admin.slider.item.store')" type="post" :validate="true">
                <x-input type="hidden" name="slider_id" :value="$slider->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.sliders.items.forms.create-left')
                    @include('admin.sliders.items.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
@endpush
