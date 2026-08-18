@extends('admin.layouts.master')

@push('libs-css')
@endpush

@php
    $orderUser = \App\Models\User::find(request()->route('id'));
@endphp

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Lịch sử đơn hàng: ') . ($orderUser->fullname ?? '')"
                                     :subtitle="__('Theo dõi các đơn hàng và gói dịch vụ đã mua của khách hàng')"
                                     icon="ti ti-shopping-cart" />
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
    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
@endpush
