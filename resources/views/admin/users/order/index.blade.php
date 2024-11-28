@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">@lang('Đơn hàng của')

                        <x-link :href="route('admin.user.edit', request()->route('id'))" :title="\App\Models\User::find(request()->route('id'))->fullname" class="text-decoration-underline" />
                    </h2>
                </div>
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
