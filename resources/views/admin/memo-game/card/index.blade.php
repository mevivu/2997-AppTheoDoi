@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.action')
    @include('admin.memo-game.card.css.card-filter')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                {{-- Standard Page Header with Actions --}}
                <x-admin.page-header
                    :title="__('Memo Game: Thư viện thẻ bài')"
                    :subtitle="__('Quản lý hình ảnh thẻ bài dùng để tạo các cặp lật thẻ theo từng chủ đề')"
                    icon="cards"
                    :addRoute="route('admin.memo-game.card.create')"
                    :addTitle="__('Thêm 1 thẻ mới')"
                >
                    <x-slot:actions>
                        <a href="{{ route('admin.memo-game.card.bulkCreate') }}"
                           class="btn-memo-bulk">
                            <i class="ti ti-cloud-upload fs-16"></i>
                            <span>{{ __('Upload hàng loạt thẻ') }}</span>
                        </a>
                    </x-slot:actions>
                </x-admin.page-header>

                {{-- Modern Filter Chips Bar --}}
                <div class="memo-filter-bar">
                    <span class="memo-filter-label">
                        <i class="ti ti-filter text-primary"></i>{{ __('Lọc chủ đề') }}:
                    </span>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @php
                            $isAllActive = !request()->filled('theme_id');
                        @endphp
                        <a href="{{ route('admin.memo-game.card.index') }}"
                           class="memo-chip memo-chip-all {{ $isAllActive ? 'active' : '' }}">
                            <i class="ti ti-apps"></i>
                            <span>{{ __('Tất cả') }}</span>
                            <span class="memo-chip-count">
                                {{ $themes->sum('cards_count') }}
                            </span>
                        </a>
                        @foreach ($themes as $t)
                            @php
                                $isActive = request('theme_id') == $t->id;
                                $code = strtolower($t->code);
                                $chipType = 'memo-chip-default';
                                $icon = 'ti ti-cards';

                                if (str_contains($code, 'vehic') || str_contains($code, 'xe')) {
                                    $chipType = 'memo-chip-vehicles';
                                    $icon = 'ti ti-car';
                                } elseif (str_contains($code, 'flow') || str_contains($code, 'hoa')) {
                                    $chipType = 'memo-chip-flowers';
                                    $icon = 'ti ti-flower';
                                } elseif (str_contains($code, 'numb') || str_contains($code, 'so')) {
                                    $chipType = 'memo-chip-numbers';
                                    $icon = 'ti ti-numbers';
                                } elseif (str_contains($code, 'flag') || str_contains($code, 'co')) {
                                    $chipType = 'memo-chip-flags';
                                    $icon = 'ti ti-flag';
                                }
                            @endphp
                            <a href="{{ route('admin.memo-game.card.index', ['theme_id' => $t->id]) }}"
                               class="memo-chip {{ $chipType }} {{ $isActive ? 'active' : '' }}">
                                <i class="{{ $icon }}"></i>
                                <span>{{ $t->name }}</span>
                                <span class="memo-chip-count">
                                    {{ $t->cards_count }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.memo-game.card.multiple')" type="post" :validate="true">
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
