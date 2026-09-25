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
                    :add-route="$canCreateCompetition ? route(RouteAdminSystem::MEMO_COMPETITION_CREATE) : null"
                    :add-title="__('Tạo Giải Đấu Mới')"
                >
                    @if (!$canCreateCompetition)
                        <x-slot:actions>
                            <button type="button"
                                    class="btn btn-secondary btn-add-custom"
                                    disabled
                                    title="{{ __('Chỉ có thể tạo giải mới sau khi giải hiện tại kết thúc') }}">
                                <i class="ti ti-lock"></i>
                                <span>{{ __('Chưa thể tạo giải mới') }}</span>
                            </button>
                        </x-slot:actions>
                    @endif
                </x-admin.page-header>
                @if (!$canCreateCompetition)
                    <div class="alert alert-warning mx-3 mt-3 mb-0" role="alert">
                        <div class="d-flex align-items-start gap-2">
                            <i class="ti ti-alert-triangle fs-3"></i>
                            <div>
                                <div class="fw-bold">{{ __('Hệ thống chỉ cho phép một giải đấu chưa kết thúc.') }}</div>
                                <div>
                                    {{ __('Giải ":name" kết thúc lúc :time. Sau thời điểm này bạn có thể tạo giải mới.', [
                                        'name' => $blockingCompetition->name,
                                        'time' => $blockingCompetition->end_at->format('d/m/Y H:i'),
                                    ]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
