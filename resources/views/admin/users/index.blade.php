@extends('admin.layouts.master')

@push('libs-css')
    <style>
        #userTable tbody td { vertical-align: middle; }
        .wallet-cell,.rank-cell { display:inline-flex; align-items:center; gap:9px; text-align:left; min-width:128px; padding:6px 8px; border:1px solid #e6edf2; border-radius:10px; background:#fbfdfe; }
        .wallet-cell-icon,.rank-cell-icon { width:34px; height:34px; flex:0 0 auto; display:grid; place-items:center; border-radius:9px; font-size:18px; }
        .wallet-cell-icon { color:#0f8f83; background:#e8f7f4; }
        .wallet-cell small,.rank-cell small { display:block; color:#7a8595; font-size:10px; line-height:1.2; }
        .wallet-cell strong,.rank-cell strong { display:block; color:#253247; font-size:12px; line-height:1.45; white-space:nowrap; }
        .parent-rank-cell { min-width:150px; text-align:left; padding:7px 9px; border:1px solid #e6edf2; border-radius:10px; background:#fbfdfe; font-size:11px; }
        .parent-rank-progress { height:4px; margin:6px 0 4px; overflow:hidden; border-radius:8px; background:#e9eef2; }
        .parent-rank-progress span { display:block; height:100%; border-radius:8px; }
        .parent-rank-cell small { color:#8a94a3; }
        #userTable tbody tr:hover .wallet-cell,#userTable tbody tr:hover .rank-cell,#userTable tbody tr:hover .parent-rank-cell { border-color:#cbdde7; background:#f5fafc; }
    </style>
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
                    @include('admin.users.partials.parent-rank-info')
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

        // Toggle icon mở rộng thông tin phân hạng
        $('#collapseParentRankInfo').on('show.bs.collapse', function () {
            $('#iconToggleRankInfo').removeClass('ti-chevron-down').addClass('ti-chevron-up');
        }).on('hide.bs.collapse', function () {
            $('#iconToggleRankInfo').removeClass('ti-chevron-up').addClass('ti-chevron-down');
        });
    </script>
@endpush

