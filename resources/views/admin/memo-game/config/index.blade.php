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
                    :title="__('Memo Game: Cấu hình độ tuổi & Lưới thẻ')"
                    :subtitle="__('Quy định kích thước lưới lật thẻ (2x3, 3x4, 4x4...), thời gian làm bài và số lượt theo độ tuổi của bé')"
                    icon="ti ti-adjustments"
                    :add-route="route(RouteAdminSystem::MEMO_AGE_CONFIG_CREATE)"
                    :add-title="__('Thêm cấu hình mới')"
                />
                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.memo-game.config.multiple')" type="post" :validate="true">
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
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
@endpush
