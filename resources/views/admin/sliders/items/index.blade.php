@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Danh sách Item Slider') . ' - ' . $slider->name"
                                     :subtitle="__('Quản lý các hình ảnh và liên kết trong slider')"
                                     icon="ti ti-photo-plus"
                                     :addRoute="route('admin.slider.item.create', $slider->id)"
                                     :addText="__('Thêm slider item')" />
                <div class="card-body">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable />
                        {{$dataTable->table(['class' => 'table table-bordered', 'style' => 'min-width: 900px;'], true)}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.sliders.items.scripts.datatable')
@endpush
