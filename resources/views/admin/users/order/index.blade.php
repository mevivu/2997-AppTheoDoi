@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
@endpush

@php
    $orderUser = $orderUser ?? \App\Models\User::find(request()->route('id'));
@endphp

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="receipt"
                :title="__('Lịch sử giao dịch: ') . ($orderUser->fullname ?? '')"
                :subtitle="__('Theo dõi tất cả các giao dịch thanh toán gói cước và dịch vụ của khách hàng')"
                :back-route="route('admin.user.edit', $orderUser->id ?? request()->route('id'))"
            />

            <div class="card border-0 custom-shadow rounded-3">
                <div class="card-body p-4">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable />
                        {{ $dataTable->table(['class' => 'table table-bordered table-hover'], true) }}
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
