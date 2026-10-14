@php
    use App\Traits\RouteAdminSystem;
@endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    <style>
        .iq-guide-card {
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 50%, #f5f3ff 100%);
            border: 1px solid #bfdbfe;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.05);
            transition: all 0.25s ease;
        }

        .iq-guide-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            font-size: 0.8rem;
            color: #334155;
            font-weight: 600;
        }

        .guide-step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
    </style>
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
                                <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('Chuẩn hóa Wechsler') }}</div>
                                <div class="fs-15 fw-bold text-dark">Mean μ = 100, SD σ = 15</div>
                                <div class="text-muted fs-11">{{ __('Thang đo trí tuệ chuẩn hóa quốc tế') }}</div>
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
                                <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('Công thức quy đổi điểm IQ') }}</div>
                                <div class="fs-15 fw-bold text-azure font-monospace">IQ = 100 + 15 × (X - μ) / σ</div>
                                <div class="text-muted fs-11">{{ __('X: Điểm thô bài test trẻ làm trên app') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-sm border-0 custom-shadow rounded-3">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md bg-green-lt text-green me-3 rounded-circle">
                                    <i class="ti ti-rotate-clockwise fs-3"></i>
                                </span>
                                <div>
                                    <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('Dữ liệu bài test thực tế') }}</div>
                                    <div class="fs-13 fw-semibold text-dark">{{ __('Tự động quét từ Ratings') }}</div>
                                    <div class="text-muted fs-11">{{ __('Cập nhật lại Mean & StdDev') }}</div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#modalRecalculateGuide">
                                <i class="ti ti-rotate-clockwise"></i>
                                <span>{{ __('Tái chuẩn hóa') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expandable Business Guide Callout -->
            <div class="card iq-guide-card mb-4 border-0">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-primary text-white rounded-circle">
                                <i class="ti ti-bulb fs-3"></i>
                            </span>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark fs-15">{{ __('Hướng dẫn Nghiệp vụ Bảng chuẩn IQ & Tác vụ "Tái chuẩn hóa"') }}</h4>
                                <span class="text-muted fs-12">{{ __('Tìm hiểu cách hệ thống tính điểm IQ chuẩn hóa và thời điểm nên sử dụng nút "Tái chuẩn hóa"') }}</span>
                            </div>
                        </div>

                        <button class="btn btn-sm btn-outline-secondary rounded-pill d-inline-flex align-items-center gap-1"
                                type="button" data-bs-toggle="collapse" data-bs-target="#guideCollapse" aria-expanded="false" aria-controls="guideCollapse">
                            <i class="ti ti-info-circle"></i>
                            <span>{{ __('Xem chi tiết') }}</span>
                            <i class="ti ti-chevron-down fs-11"></i>
                        </button>
                    </div>

                    <!-- Collapsible Content -->
                    <div class="collapse show" id="guideCollapse">
                        <div class="row g-3 pt-2 border-top border-blue-subtle">
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="guide-step-num">1</span>
                                        <span class="fw-bold text-dark fs-13">{{ __('Điểm thô thực tế (X)') }}</span>
                                    </div>
                                    <p class="text-muted fs-12 mb-0">
                                        Là số câu trả lời đúng mà trẻ đạt được khi hoàn thành bài đánh giá IQ trên ứng dụng (ví dụ: đúng 14/30 câu thì $X = 14$).
                                    </p>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="guide-step-num">2</span>
                                        <span class="fw-bold text-dark fs-13">{{ __('Bảng chuẩn theo tuổi (μ & σ)') }}</span>
                                    </div>
                                    <p class="text-muted fs-12 mb-0">
                                        Hệ thống tra cứu theo tuổi của trẻ để lấy: <strong>Mean μ</strong> (điểm trung bình trẻ cùng tuổi) và <strong>StdDev σ</strong> (độ phân tán điểm số).
                                    </p>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="guide-step-num">3</span>
                                        <span class="fw-bold text-dark fs-13">{{ __('Chỉ số IQ Wechsler') }}</span>
                                    </div>
                                    <p class="text-muted fs-12 mb-0">
                                        Quy đổi bằng công thức chuẩn: <code>100 + 15 × (X - μ) / σ</code>. Mức 100 là trung bình dân số chuẩn, phân loại theo 7 cấp độ Wechsler.
                                    </p>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="p-3 bg-white rounded-3 border border-success-subtle h-100 shadow-xs" style="background: #f0fdf4;">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="guide-step-num" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">4</span>
                                        <span class="fw-bold text-success fs-13">{{ __('Khi nào "Tái chuẩn hóa"?') }}</span>
                                    </div>
                                    <p class="text-muted fs-12 mb-0">
                                        Bấm khi app đã có thêm lượng bài test thực tế mới đủ lớn (≥ 30 trẻ/độ tuổi). Hệ thống sẽ tự động quét Ratings và tính lại μ, σ mới nhất.
                                    </p>
                                </div>
                            </div>
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

    <!-- Modal Hướng dẫn & Xác nhận Tái chuẩn hóa chi tiết -->
    <div class="modal modal-blur fade" id="modalRecalculateGuide" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="ti ti-rotate-clockwise fs-3"></i>
                        <span>{{ __('Tái chuẩn hóa Bảng chuẩn IQ từ Ratings thực tế') }}</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4">
                    <h4 class="fw-bold text-dark mb-2">
                        {{ __('Tính năng "Tái chuẩn hóa" hoạt động như thế nào?') }}
                    </h4>
                    <p class="text-muted fs-13 mb-3">
                        Khi bạn xác nhận, hệ thống sẽ thực hiện tác vụ tự động quét toàn bộ dữ liệu trong bảng <strong>Ratings</strong> (kết quả bài kiểm tra IQ thực tế của các bé trên ứng dụng) và xử lý:
                    </p>

                    <div class="list-group list-group-flush border rounded-3 mb-3">
                        <div class="list-group-item d-flex align-items-start gap-2 py-2">
                            <i class="ti ti-check text-success fs-4 mt-1"></i>
                            <div class="fs-13">
                                <strong>{{ __('Gom nhóm theo độ tuổi:') }}</strong>
                                <span class="text-muted">{{ __('Tách riêng bài làm của trẻ theo từng độ tuổi (từ 1 đến 20 tuổi).') }}</span>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-start gap-2 py-2">
                            <i class="ti ti-check text-success fs-4 mt-1"></i>
                            <div class="fs-13">
                                <strong>{{ __('Tính toán thống kê mới:') }}</strong>
                                <span class="text-muted">{{ __('Tính Cỡ mẫu (N), Điểm trung bình (Mean μ), Độ lệch chuẩn (StdDev σ), Điểm Min và Max.') }}</span>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-start gap-2 py-2">
                            <i class="ti ti-check text-success fs-4 mt-1"></i>
                            <div class="fs-13">
                                <strong>{{ __('Cập nhật tự động:') }}</strong>
                                <span class="text-muted">{{ __('Lưu đè bộ tham số mới vào bảng chuẩn IQ này và xóa bộ nhớ đệm (Cache).') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Callout Lưu ý quan trọng -->
                    <div class="alert alert-warning border border-warning-subtle d-flex align-items-start gap-2 p-3 mb-0 rounded-3">
                        <i class="ti ti-alert-triangle text-warning fs-3 flex-shrink-0 mt-1"></i>
                        <div class="fs-12">
                            <strong class="d-block mb-1 text-dark">{{ __('Lưu ý quan trọng trước khi thực hiện:') }}</strong>
                            <ul class="mb-0 ps-3 text-muted">
                                <li>{{ __('Chỉ nên thực hiện khi hệ thống đã có thêm lượng bài test mới đủ lớn (khuyến nghị mỗi độ tuổi ≥ 30 - 50 bài test).') }}</li>
                                <li>{{ __('Các bài test đã hoàn thành trong quá khứ vẫn giữ nguyên điểm IQ lịch sử đã chấm.') }}</li>
                                <li>{{ __('Các bài test mới làm sau thời điểm này sẽ áp dụng bộ tham số mới.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted me-auto" data-bs-dismiss="modal">
                        {{ __('Hủy bỏ') }}
                    </button>

                    <form action="{{ route('admin.iq_norm.recalculate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="ti ti-rotate-clockwise fs-4"></i>
                            <span>{{ __('Tiến hành quét & Tái chuẩn hóa ngay') }}</span>
                        </button>
                    </form>
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
