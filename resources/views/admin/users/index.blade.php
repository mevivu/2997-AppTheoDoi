@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Danh sách khách hàng')"
                                     :subtitle="__('Quản lý thông tin tài khoản cha mẹ và người dùng ứng dụng')"
                                     icon="ti ti-users"
                                     :addRoute="route('admin.user.create')"
                                     :addText="__('Thêm mới')" />
                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.user.multiple')" type="post" :validate="true">
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
    @include('admin.users.partials.modal.modal-deposit')
    @include('admin.users.partials.modal.modal-withdraw')
    @include('admin.users.partials.modal.modal-deactivate')
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
    @include('admin.users.partials.scripts.deposit-wallet-script')
    @include('admin.users.partials.scripts.withdraw-wallet-script')
    <script>
        // Modal Ngưng hoạt động (Cập nhật trạng thái)
        $(document).on('click', '.open-modal-deactivate', function () {
            var form = $("#modalFormDeactivate"), 
                action = $(this).data('route'),
                fullname = $(this).data('fullname'),
                code = $(this).data('code');
            form.attr('action', action);
            var displayName = fullname || (code ? ('#' + code) : '');
            if (code && fullname) {
                displayName += ' (#' + code + ')';
            }
            $('#deactivateUserName').text(displayName || '-');
        });

        // Modal Xóa vĩnh viễn
        $(document).on('click', '.open-modal-force-delete', function () {
            var form = $("#modalFormForceDelete"), 
                action = $(this).data('route'),
                fullname = $(this).data('fullname'),
                code = $(this).data('code');
            form.attr('action', action);
            var displayName = fullname || (code ? ('#' + code) : '');
            if (code && fullname) {
                displayName += ' (#' + code + ')';
            }
            if ($('#forceDeleteUserName').length) {
                $('#forceDeleteUserName').text(displayName ? ('(' + displayName + ')') : '');
            }
        });
    </script>
@endpush

