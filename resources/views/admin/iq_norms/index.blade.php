@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <!-- Header Summary Cards -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card card-sm border-0 custom-shadow rounded-3">
                        <div class="card-body d-flex align-items-center">
                            <span class="avatar avatar-md bg-purple-lt text-purple me-3 rounded-circle">
                                <i class="ti ti-brain fs-3"></i>
                            </span>
                            <div>
                                <div class="text-muted fs-12 fw-semibold text-uppercase">Chuẩn hóa Wechsler</div>
                                <div class="fs-16 fw-bold text-dark">Mean $\mu = 100$, SD $\sigma = 15$</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-sm border-0 custom-shadow rounded-3">
                        <div class="card-body d-flex align-items-center">
                            <span class="avatar avatar-md bg-blue-lt text-blue me-3 rounded-circle">
                                <i class="ti ti-math-function fs-3"></i>
                            </span>
                            <div>
                                <div class="text-muted fs-12 fw-semibold text-uppercase">Công thức độ lệch chuẩn</div>
                                <div class="fs-15 fw-bold text-azure">$\text{IQ} = 100 + 15 \times \frac{X - \mu}{\sigma}$</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-sm border-0 custom-shadow rounded-3">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md bg-green-lt text-green me-3 rounded-circle">
                                    <i class="ti ti-refresh fs-3"></i>
                                </span>
                                <div>
                                    <div class="text-muted fs-12 fw-semibold text-uppercase">Dữ liệu thực tế</div>
                                    <div class="fs-13 text-muted">Tự động quét từ Ratings</div>
                                </div>
                            </div>
                            <form action="{{ route('admin.iq_norm.recalculate') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn quét lại dữ liệu toàn bộ bài test IQ thực tế để tính lại Mean và StdDev cho từng nhóm tuổi không?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="ti ti-rotate-clockwise me-1"></i>Tái chuẩn hóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card DataTable -->
            <div class="card custom-shadow border-0 rounded-3">
                <x-admin.page-header :title="__('Bảng chuẩn IQ Wechsler theo độ tuổi')"
                                     :subtitle="__('Quản lý giá trị Mean (μ) và StdDev (σ) nhóm tuổi dùng để quy đổi điểm thô ra chỉ số IQ chuẩn hóa')"
                                     icon="ti ti-chart-bar"
                                     :addRoute="route('admin.iq_norm.create')"
                                     :addText="__('Thêm mốc tuổi')" />
                <div class="card-body p-4">
                    <x-form id="formMultiple" :action="route('admin.iq_norm.multiple')" type="post" :validate="true">
                        <div class="table-responsive position-relative">
                            <x-admin.partials.toggle-column-datatable />
                            @isset($actionMultiple)
                                <x-admin.partials.select-action-multiple :actionMultiple="$actionMultiple" />
                            @endisset
                            {{ $dataTable->table(['class' => 'table table-bordered table-hover align-middle'], true) }}
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
