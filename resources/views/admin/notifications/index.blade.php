@extends('admin.layouts.master')

@push('libs-css')

@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">@lang('Danh Sách Thông Báo')</h2>
                    <div class="d-flex justify-content-between gap-2">
                        <x-button.modal-delete class="btn btn-danger d-none" id="deleteSelect">
                            <i class="ti ti-trash"></i>
                            <span class="ms-1">@lang('deleteMulti')</span>
                        </x-button.modal-delete>
                        <x-link :href="route('admin.notification.create')" class="btn btn-primary">
                            <i class="ti ti-plus"></i>
                            <span class="ms-1">@lang('add')</span>
                        </x-link>
                    </div>
                </div>

                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.notification.multiple')" type="post" :validate="true">
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
    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
@endpush
