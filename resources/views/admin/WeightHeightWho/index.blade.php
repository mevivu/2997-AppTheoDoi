@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Danh sách Cân nặng - Chiều cao chuẩn WHO')"
                                     :subtitle="__('Bảng quy chuẩn tăng trưởng chiều cao và cân nặng theo độ tuổi của WHO')"
                                     icon="ti ti-scale"
                                     :addRoute="route('admin.weight-height-who.create')"
                                     :addText="__('Thêm mới')">
                    <x-slot name="actions">
                        <button type="button"
                                class="btn btn-success rounded-pill px-3 shadow-sm d-inline-flex align-items-center"
                                data-bs-toggle="modal"
                                data-bs-target="#importExcelModal">
                            <i class="ti ti-file-import me-1"></i> {{ __('Import') }}
                        </button>
                        <a href="{{ route('admin.weight-height-who.export') }}" class="btn btn-info rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                            <i class="ti ti-file-export me-1"></i> {{ __('Export') }}
                        </a>
                    </x-slot>
                </x-admin.page-header>
                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.weight-height-who.multiple')" type="post" :validate="true">
                        <div class="table-responsive position-relative">
                            <x-admin.partials.toggle-column-datatable />
                            @isset($actionMultiple)
                                <x-admin.partials.select-action-multiple :actionMultiple="$actionMultiple" />
                            @endisset
                            {{ $dataTable->table(['class' => 'table table-bordered'], true) }}
                        </div>
                    </x-form>
                </div>
                <!-- Modal Import-->
                <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="importExcelModalLabel">{{ __('Import Excel File') }}</h5>
                            </div>
                            <div class="modal-body">
                                <x-form id="importExcelForm" :action="route('admin.weight-height-who.import')" method="post"
                                        enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="excelFile" class="form-label">{{ __('Excel File') }}</label>
                                        <input type="file" class="form-control" id="excelFile" name="excelFile"
                                               required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="genderSelect" class="form-label">{{ __('Select Gender') }}</label>
                                        <x-select id="genderSelect" name="gender" :required="true">
                                            @foreach ($gender as $key => $value)
                                                <x-select-option :value="$key" :title="$value"/>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </x-form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                                <button type="submit" form="importExcelForm"
                                        class="btn btn-primary">{{ __('Import') }}</button>
                            </div>
                        </div>
                    </div>
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

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])

@endpush
