@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Cột mốc nhật ký')"
                                     :subtitle="__('Danh mục cột mốc gợi ý theo giai đoạn phát triển của trẻ')"
                                     icon="ti ti-flag"
                                     :addRoute="route('admin.journal_milestone.create')"
                                     :addText="__('Thêm mới')" />
                <div class="card-body">
                    <!-- Stage Filter Tabs / Chips -->
                    <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted fw-bold me-2"><i class="ti ti-filter me-1"></i>Lọc theo giai đoạn:</span>
                        <a href="{{ route('admin.journal_milestone.index') }}" class="btn btn-sm {{ !request('stage') ? 'btn-primary' : 'btn-outline-secondary' }}">
                            Tất cả
                        </a>
                        @foreach($stages as $stage)
                            <a href="{{ route('admin.journal_milestone.index', ['stage' => $stage->value]) }}"
                               class="btn btn-sm {{ request('stage') === $stage->value ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ $stage->label() }}
                            </a>
                        @endforeach
                    </div>

                    <div class="table-responsive position-relative">
                        {{ $dataTable->table(['class' => 'table table-bordered table-striped align-middle'], true) }}
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
@endpush
