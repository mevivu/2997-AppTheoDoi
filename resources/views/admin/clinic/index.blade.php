@extends('admin.layouts.master')

@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
    @include('admin.common.style.modal_exel')
    @include('admin.common.style.style')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Danh sách phòng khám')"
                                     :subtitle="__('Quản lý hệ thống cơ sở y tế và phòng khám đối tác')"
                                     icon="ti ti-building-hospital"
                                     :addRoute="route('admin.clinic.create')"
                                     :addText="__('Thêm mới')">
                    <x-slot name="actions">
                        <button type="button" class="btn btn-success rounded-pill px-3 shadow-sm d-inline-flex align-items-center" data-bs-toggle="modal"
                                data-bs-target="#importExcelModal">
                            <i class="ti ti-file-import me-1"></i>{{ __('Import') }}
                        </button>
                        <a href="{{ route('admin.clinic.exportTemplate') }}" class="btn btn-secondary rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                            <i class="ti ti-template me-1"></i>{{ __('Template') }}
                        </a>
                        <a href="{{ route('admin.address.exportProvince') }}" class="btn btn-info rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                            <i class="ti ti-map-pin me-1"></i>{{ __('Tỉnh/Thành') }}
                        </a>
                        <a href="{{ route('admin.address.exportWard') }}" class="btn btn-warning rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                            <i class="ti ti-map me-1"></i>{{ __('Phường/Xã') }}
                        </a>
                    </x-slot>
                </x-admin.page-header>
                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.clinic.multiple')" type="post" :validate="true">
                        <div class="table-responsive position-relative">
                            <x-admin.partials.toggle-column-datatable/>
                            @isset($actionMultiple)
                                <x-admin.partials.select-action-multiple :actionMultiple="$actionMultiple"/>
                            @endisset
                            {{ $dataTable->table(['class' => 'table table-bordered'], true) }}
                        </div>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>

    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
@endpush
