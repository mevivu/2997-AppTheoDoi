@extends('admin.layouts.master')

@push('libs-css')
<style>
    .toggle-icon {
        transition: transform 0.25s ease-in-out;
    }
    [aria-expanded="false"] .toggle-icon {
        transform: rotate(-90deg);
    }
</style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            {{-- Card Hướng dẫn sử dụng & Quy tắc cấu hình phiên bản --}}
            <div class="card border-0 custom-shadow rounded-3 mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary rounded-circle me-2">
                            <i class="ti ti-help-circle fs-3"></i>
                        </span>
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark fs-15">{{ __('Cẩm nang & Quy tắc Cấu hình Phiên bản Ứng dụng') }}</h5>
                            <small class="text-muted">{{ __('Hướng dẫn cơ chế kiểm tra phiên bản trên ứng dụng mobile (Android & iOS)') }}</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#appVersionGuideCollapse" aria-expanded="true" aria-controls="appVersionGuideCollapse">
                        <i class="ti ti-chevron-down toggle-icon"></i>
                        <span class="d-none d-sm-inline">{{ __('Thu gọn / Mở rộng') }}</span>
                    </button>
                </div>
                <div class="collapse show" id="appVersionGuideCollapse">
                    <div class="card-body p-4">
                        {{-- 3 Trụ cột cốt lõi --}}
                        <div class="row g-3 mb-4">
                            <!-- Cột 1: Notify Version -->
                            <div class="col-12 col-md-4">
                                <div class="p-3 rounded-3 border h-100" style="background-color: #f8fafc; border-color: #cbd5e1 !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-green-lt px-2 py-1 fs-12 fw-bold">
                                                <i class="ti ti-bell-ringing me-1"></i>Notify Version
                                            </span>
                                        </div>
                                        <span class="badge bg-success text-white">{{ __('Gợi ý cập nhật') }}</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">{{ __('Cập nhật tùy chọn (Soft Update)') }}</h6>
                                    <p class="text-muted fs-12 mb-2" style="line-height: 1.5;">
                                        Áp dụng khi phát hành tính năng mới nhưng phiên bản hiện tại vẫn tương thích với hệ thống.
                                    </p>
                                    <div class="bg-white p-2 rounded border fs-12 text-secondary">
                                        <div class="d-flex align-items-start gap-1 mb-1">
                                            <i class="ti ti-check text-success mt-1 flex-shrink-0"></i>
                                            <span><strong>Điều kiện:</strong> <code>App hiện tại &lt; Notify</code> (và <code>&gt;= Required</code>)</span>
                                        </div>
                                        <div class="d-flex align-items-start gap-1">
                                            <i class="ti ti-device-mobile text-primary mt-1 flex-shrink-0"></i>
                                            <span><strong>Hành vi:</strong> Hiện Popup có 2 nút <em>"Cập nhật ngay"</em> hoặc <em>"Để sau"</em> (hoãn 24h).</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cột 2: Required Version -->
                            <div class="col-12 col-md-4">
                                <div class="p-3 rounded-3 border h-100" style="background-color: #fef2f2; border-color: #fecaca !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-danger-lt px-2 py-1 fs-12 fw-bold">
                                                <i class="ti ti-alert-triangle me-1"></i>Required Version
                                            </span>
                                        </div>
                                        <span class="badge bg-danger text-white">{{ __('Bắt buộc cập nhật') }}</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">{{ __('Khóa màn hình (Force Update)') }}</h6>
                                    <p class="text-muted fs-12 mb-2" style="line-height: 1.5;">
                                        Áp dụng khi sửa lỗi bảo mật nghiêm trọng hoặc nâng cấp API làm ngừng hỗ trợ app cũ.
                                    </p>
                                    <div class="bg-white p-2 rounded border fs-12 text-secondary">
                                        <div class="d-flex align-items-start gap-1 mb-1">
                                            <i class="ti ti-alert-circle text-danger mt-1 flex-shrink-0"></i>
                                            <span><strong>Điều kiện:</strong> <code>App hiện tại &lt; Required</code></span>
                                        </div>
                                        <div class="d-flex align-items-start gap-1">
                                            <i class="ti ti-lock text-danger mt-1 flex-shrink-0"></i>
                                            <span><strong>Hành vi:</strong> Khóa toàn bộ màn hình, ép buộc người dùng mở Store để cập nhật mới được dùng tiếp.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cột 3: Checking Version -->
                            <div class="col-12 col-md-4">
                                <div class="p-3 rounded-3 border h-100" style="background-color: #eff6ff; border-color: #bfdbfe !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-blue-lt px-2 py-1 fs-12 fw-bold">
                                                <i class="ti ti-shield-check me-1"></i>Checking Version
                                            </span>
                                        </div>
                                        <span class="badge bg-primary text-white">{{ __('Duyệt Store') }}</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">{{ __('Chế độ kiểm duyệt (Review Mode)') }}</h6>
                                    <p class="text-muted fs-12 mb-2" style="line-height: 1.5;">
                                        Dành cho quy trình nộp ứng dụng lên Google Play hoặc Apple App Store để thẩm định.
                                    </p>
                                    <div class="bg-white p-2 rounded border fs-12 text-secondary">
                                        <div class="d-flex align-items-start gap-1 mb-1">
                                            <i class="ti ti-eye-off text-info mt-1 flex-shrink-0"></i>
                                            <span><strong>Điều kiện:</strong> <code>App hiện tại == Checking</code></span>
                                        </div>
                                        <div class="d-flex align-items-start gap-1">
                                            <i class="ti ti-shield text-info mt-1 flex-shrink-0"></i>
                                            <span><strong>Hành vi:</strong> Ẩn toàn bộ popup cập nhật để reviewer duyệt app dễ dàng. <em>(Để trống nếu không duyệt)</em>.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bảng Kịch bản Thực tế (Logic Matrix) --}}
                        <div class="border rounded-3 p-3 bg-light mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-table text-primary fs-4"></i>
                                <span class="fw-bold text-dark fs-14">{{ __('Bảng kịch bản hoạt động thực tế trên ứng dụng Mobile') }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered bg-white mb-0 fs-12 align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="min-width: 180px;">{{ __('Tình huống thực tế') }}</th>
                                            <th style="width: 130px;">{{ __('Bản trên máy User') }}</th>
                                            <th style="width: 110px;">{{ __('Required') }}</th>
                                            <th style="width: 110px;">{{ __('Notify') }}</th>
                                            <th style="width: 110px;">{{ __('Checking') }}</th>
                                            <th style="min-width: 260px;">{{ __('Hành vi hiển thị trên điện thoại') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold text-danger">
                                                <i class="ti ti-shield-x me-1"></i>{{ __('Lỗi bảo mật nghiêm trọng / Đổi API') }}
                                            </td>
                                            <td class="text-center"><span class="badge bg-secondary">1.0.0</span></td>
                                            <td class="text-center"><span class="badge bg-danger">1.1.0</span></td>
                                            <td class="text-center"><span class="badge bg-success">1.1.0</span></td>
                                            <td class="text-center"><span class="text-muted">null</span></td>
                                            <td>
                                                <span class="text-danger fw-semibold"><i class="ti ti-lock me-1"></i>{{ __('Khóa màn hình app:') }}</span>
                                                <span class="text-muted">{{ __('Buộc cập nhật lên 1.1.0, không có nút bỏ qua.') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-warning">
                                                <i class="ti ti-sparkles me-1"></i>{{ __('Có tính năng mới (Không bắt buộc)') }}
                                            </td>
                                            <td class="text-center"><span class="badge bg-secondary">1.0.0</span></td>
                                            <td class="text-center"><span class="badge bg-danger">1.0.0</span></td>
                                            <td class="text-center"><span class="badge bg-success">1.1.0</span></td>
                                            <td class="text-center"><span class="text-muted">null</span></td>
                                            <td>
                                                <span class="text-warning fw-semibold"><i class="ti ti-bell-ringing me-1"></i>{{ __('Hiện Popup gợi ý:') }}</span>
                                                <span class="text-muted">{{ __('Có nút "Cập nhật" và nút "Để sau" (hoãn 24h).') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-success">
                                                <i class="ti ti-circle-check me-1"></i>{{ __('Người dùng đang ở bản mới nhất') }}
                                            </td>
                                            <td class="text-center"><span class="badge bg-secondary">1.1.0</span></td>
                                            <td class="text-center"><span class="badge bg-danger">1.0.0</span></td>
                                            <td class="text-center"><span class="badge bg-success">1.1.0</span></td>
                                            <td class="text-center"><span class="text-muted">null</span></td>
                                            <td>
                                                <span class="text-success fw-semibold"><i class="ti ti-check me-1"></i>{{ __('Hoạt động bình thường:') }}</span>
                                                <span class="text-muted">{{ __('Không hiển thị bất kỳ thông báo cập nhật nào.') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-primary">
                                                <i class="ti ti-brand-appstore me-1"></i>{{ __('Đang nộp duyệt Apple / Google Store') }}
                                            </td>
                                            <td class="text-center"><span class="badge bg-secondary">1.2.0</span></td>
                                            <td class="text-center"><span class="badge bg-danger">1.0.0</span></td>
                                            <td class="text-center"><span class="badge bg-success">1.1.0</span></td>
                                            <td class="text-center"><span class="badge bg-primary">1.2.0</span></td>
                                            <td>
                                                <span class="text-primary fw-semibold"><i class="ti ti-shield me-1"></i>{{ __('Chế độ kiểm duyệt (Review):') }}</span>
                                                <span class="text-muted">{{ __('Ẩn popup cập nhật để reviewer thẩm định app.') }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Lưu ý quan trọng --}}
                        <div class="row g-2 text-muted fs-12">
                            <div class="col-12 col-md-6 d-flex align-items-center gap-1">
                                <i class="ti ti-bulb text-warning fs-4 flex-shrink-0"></i>
                                <span><strong>{{ __('Chuẩn SemVer:') }}</strong> {{ __('Luôn đặt phiên bản dạng X.Y.Z (Ví dụ: 1.0.0, 1.0.1, 2.1.0).') }}</span>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-center gap-1">
                                <i class="ti ti-alert-triangle text-danger fs-4 flex-shrink-0"></i>
                                <span><strong>{{ __('Nguyên tắc logic:') }}</strong> {{ __('Tuyệt đối đảm bảo: Required <= Notify để tránh xung đột hành vi.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bảng danh sách phiên bản --}}
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Danh sách Phiên bản Ứng dụng')"
                                     :subtitle="__('Quản lý các phiên bản phát hành và cập nhật app mobile')"
                                     icon="ti ti-brand-appstore" />
                <div class="card-body">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable />
                        {{ $dataTable->table(['class' => 'table table-bordered', 'style' => 'min-width: 900px;'], true) }}
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
