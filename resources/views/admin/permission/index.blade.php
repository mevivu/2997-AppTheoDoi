@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Danh sách Quyền hệ thống')"
                                     :subtitle="__('Quản lý danh sách các quyền hạn (Permissions) cho Dev và Quản trị viên')"
                                     icon="ti ti-key"
                                     :addRoute="route('admin.permission.create')"
                                     :addText="__('Thêm Quyền')" />
                <div class="card-body">
                    <div class="alert alert-info-light mb-3">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Lưu ý:</strong> Đây là phần dành cho Nhà phát triển để cấu hình slug phân quyền. Vui lòng không điều chỉnh nếu chưa rõ chức năng.
                    </div>
                    <x-form id="formMultiple" :action="route('admin.permission.multiple')" type="post" :validate="true">
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
