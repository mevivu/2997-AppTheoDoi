@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow border-0">
                <x-admin.page-header
                    icon="receipt"
                    :title="__('Danh sách Giao dịch')"
                    :subtitle="__('Theo dõi và quản lý lịch sử giao dịch, đăng ký gói dịch vụ')"
                />
                
                <div class="card-body p-3">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable/>

                        {{ $dataTable->table(['class' => 'table table-bordered table-striped table-hover align-middle mb-0'], true) }}
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
    @include('admin.common.copy')
@endpush

