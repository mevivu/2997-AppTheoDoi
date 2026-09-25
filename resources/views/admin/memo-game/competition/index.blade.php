@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header
                    :title="__('Memo Game: Giải Đấu & Cuộc Thi')"
                    :subtitle="__('Quản lý các giải đấu trí nhớ lật thẻ (lưới 5×6, 4 ván liên tiếp 4 chủ đề, không xem trước, xếp hạng tự động)')"
                    icon="ti ti-trophy"
                    :add-route="route(RouteAdminSystem::MEMO_COMPETITION_CREATE)"
                    :add-title="__('Tạo Giải Đấu Mới')"
                />
                <div class="card-body">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable />
                        {{ $dataTable->table(['class' => 'table table-bordered'], true) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
@endpush
