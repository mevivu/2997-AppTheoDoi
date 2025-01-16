@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Danh sách Cân nặng chiều cao Theo chuẩn Who') }}</h2>
                    <div>
                        <x-link :href="route('admin.weight-height-who.create')"
                                class="btn btn-primary"><i class="ti ti-plus"></i>{{ __('Thêm mới') }}
                        </x-link>

                        <button type="button"
                                class="btn btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#importExcelModal">
                            <i class="ti ti-file"></i> {{ __('Import') }}
                        </button>

                        <a href="{{ route('admin.weight-height-who.export') }}" class="btn btn-info">
                            <i class="ti ti-export"></i> {{ __('Export') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
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
