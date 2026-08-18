@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
    <style>
        .wrap-loop-input .add-image-ckfinder{
            max-width: 300px;
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="car"
                :title="__('Cài đặt giá C - Car')"
                :subtitle="__('Cấu hình bảng giá và chính sách cước phí dịch vụ xe ô tô')"
                :back-route="route('admin.dashboard')"
            />

            <x-form :action="route('admin.setting.update')" type="put" :validate="true">
                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-lg-8 col-xl-9">
                        @include('admin.settings.forms.c_car.setting_c_car')
                    </div>
                    @include('admin.settings.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
@endpush

@push('custom-js')
    @include('admin.settings.forms.c_car.script')
@endpush
