@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Danh sách khách hàng') }}</h2>
                    <div class="d-flex align-items-center gap-2">
                        {{-- <form action="{{ route('admin.user.clearNormalTokens') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn đăng xuất tất cả tài khoản gói thường để áp dụng phiên đăng nhập mới?');">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="ti ti-logout"></i>
                                <span class="ms-1">{{ __('Đăng xuất toàn bộ gói thường') }}</span>
                            </button>
                        </form> --}}
                        <x-link :href="route('admin.user.create')" class="btn btn-primary">
                            <i class="ti ti-plus"></i>
                            <span class="ms-1">@lang('add')</span>
                        </x-link>
                    </div>
                </div>
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
