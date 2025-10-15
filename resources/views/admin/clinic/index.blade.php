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
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Danh sách phòng khám') }}</h2>
                    <div class="action-buttons-wrapper">
                        <x-link :href="route('admin.clinic.create')" class="btn btn-modern btn-primary-enhanced">
                            <i class="ti ti-plus btn-icon"></i>{{ __('add') }}
                        </x-link>

                        <button type="button" class="btn btn-modern btn-success-enhanced" data-bs-toggle="modal"
                                data-bs-target="#importExcelModal">
                            <i class="ti ti-file btn-icon"></i>{{ __('Import') }}
                        </button>

                        <!-- Export Template Button -->
                        <a href="{{ route('admin.clinic.exportTemplate') }}" class="btn btn-modern btn-dark-blue-enhanced">
                            <i class="ti ti-download btn-icon"></i>{{ __('Export Template') }}
                        </a>

                        <a href="{{ route('admin.address.exportProvince') }}" class="btn btn-modern btn-info-enhanced">
                            <i class="ti ti-download btn-icon"></i>{{ __('Export tỉnh') }}
                        </a>

                        <a href="{{ route('admin.address.exportWard') }}" class="btn btn-modern btn-warning-enhanced">
                            <i class="ti ti-download btn-icon"></i>{{ __('Export phường/xã') }}
                        </a>
                    </div>
                </div>
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

    @include('admin.common.js.modal_exel', [
     'importRoute' => route('admin.clinic.import'),
 ])
@endpush
