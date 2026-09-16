@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="chart-dots"
                :title="__('Chi tiết Kết quả Memo Game #') . $response->id"
                :subtitle="__('Báo cáo phân tích bài test trí nhớ thị giác & độ tập trung của bé')"
                :back-route="route(RouteAdminSystem::MEMO_RATING_INDEX)"
            />

            <!-- Top Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card custom-shadow border-0 h-100">
                        <div class="card-body text-center p-3">
                            <div class="text-muted fs-12 text-uppercase fw-bold mb-1">{{ __('Điểm tổng hợp') }}</div>
                            <div class="fs-28 fw-bolder text-warning">
                                <i class="ti ti-trophy me-1"></i>{{ number_format($response->score, 1) }}
                            </div>
                            <div class="mt-2">
                                @php
                                    $badgeClass = match($response->evaluation_label) {
                                        'Xuất sắc' => 'bg-success text-white',
                                        'Tốt' => 'bg-primary text-white',
                                        'Khá' => 'bg-info text-white',
                                        'Trung bình' => 'bg-warning text-dark',
                                        default => 'bg-secondary text-white',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1 fs-12">{{ $response->evaluation_label ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card custom-shadow border-0 h-100">
                        <div class="card-body text-center p-3">
                            <div class="text-muted fs-12 text-uppercase fw-bold mb-1">{{ __('Thời gian làm bài') }}</div>
                            <div class="fs-28 fw-bolder text-primary">
                                <i class="ti ti-clock me-1"></i>
                                {{ floor($response->total_duration_spent / 60) > 0 ? floor($response->total_duration_spent / 60) . 'm ' : '' }}
                                {{ ($response->total_duration_spent % 60) . 's' }}
                            </div>
                            <div class="mt-2 text-muted fs-12">
                                {{ __('Giới hạn tối đa') }}: {{ floor(($response->ageConfig->total_duration ?? 180) / 60) }} {{ __('phút') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card custom-shadow border-0 h-100">
                        <div class="card-body text-center p-3">
                            <div class="text-muted fs-12 text-uppercase fw-bold mb-1">{{ __('Số cặp hoàn thành') }}</div>
                            <div class="fs-28 fw-bolder text-success">
                                <i class="ti ti-cards me-1"></i>{{ $response->total_pairs_matched }}
                            </div>
                            <div class="mt-2 text-muted fs-12">
                                {{ __('Tổng số cặp ghép đúng qua 3 lượt') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card custom-shadow border-0 h-100">
                        <div class="card-body text-center p-3">
                            <div class="text-muted fs-12 text-uppercase fw-bold mb-1">{{ __('Số lần lật sai') }}</div>
                            <div class="fs-28 fw-bolder text-danger">
                                <i class="ti ti-alert-triangle me-1"></i>{{ $response->total_mistakes }}
                            </div>
                            <div class="mt-2 text-muted fs-12">
                                {{ __('Số lần chọn sai 2 thẻ khác nhau') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Left: Thông tin bé & Cấu hình -->
                <div class="col-12 col-lg-5">
                    <div class="card custom-shadow h-100">
                        <div class="card-header bg-transparent border-bottom">
                            <h4 class="card-title mb-0 text-primary">
                                <i class="ti ti-user me-1"></i> {{ __('Thông tin bài test') }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted py-2" style="width: 140px;">{{ __('Bé tham gia') }}:</td>
                                        <td class="fw-bold py-2">
                                            {{ $response->child->fullname ?? $response->child->name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-2">{{ __('Độ tuổi làm bài') }}:</td>
                                        <td class="py-2">
                                            <span class="badge bg-blue-subtle text-primary border border-primary-subtle px-2 py-1">
                                                {{ $response->age }} {{ __('tuổi') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if ($response->child && $response->child->user)
                                        <tr>
                                            <td class="text-muted py-2">{{ __('Phụ huynh') }}:</td>
                                            <td class="py-2">
                                                {{ $response->child->user->fullname ?? 'N/A' }}
                                                @if(!empty($response->child->user->phone))
                                                    <span class="text-muted fs-12">({{ $response->child->user->phone }})</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-muted py-2">{{ __('Chủ đề đã chọn') }}:</td>
                                        <td class="py-2">
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <i class="ti ti-palette text-primary me-1"></i>
                                                {{ $response->theme->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-2">{{ __('Lưới thẻ bài') }}:</td>
                                        <td class="py-2">
                                            @if ($response->ageConfig)
                                                <span class="fw-bold font-monospace">{{ $response->ageConfig->rows }} x {{ $response->ageConfig->columns }}</span>
                                                <span class="text-muted fs-12">({{ $response->ageConfig->total_cards }} thẻ / {{ $response->ageConfig->pairs_count }} cặp)</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-2">{{ __('Thời điểm làm bài') }}:</td>
                                        <td class="py-2 text-muted">
                                            <i class="ti ti-calendar me-1"></i> {{ $response->created_at ? $response->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Nhận xét & Đánh giá -->
                <div class="col-12 col-lg-7">
                    <div class="card custom-shadow h-100">
                        <div class="card-header bg-transparent border-bottom">
                            <h4 class="card-title mb-0 text-primary">
                                <i class="ti ti-message-2 me-1"></i> {{ __('Nhận xét & Khuyến nghị') }}
                            </h4>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="alert alert-light border p-3 mb-3">
                                <div class="fw-bold text-dark mb-1">
                                    <i class="ti ti-bulb text-warning me-1"></i> {{ __('Đánh giá tổng quát') }}:
                                </div>
                                <p class="text-muted mb-0 fs-13" style="line-height: 1.6;">
                                    {{ $response->feedback ?: __('Bé đã hoàn thành tốt bài test trí nhớ Memo Game. Khả năng định vị hình ảnh và ghi nhớ ngắn hạn biểu hiện rõ qua tốc độ lật các cặp thẻ trùng khớp.') }}
                                </p>
                            </div>

                            <div class="p-3 bg-light rounded border">
                                <div class="fw-bold text-dark fs-13 mb-2">{{ __('Phân tích chỉ số trí nhớ') }}:</div>
                                <div class="row g-2 text-center fs-12">
                                    <div class="col-4">
                                        <div class="p-2 bg-white rounded border">
                                            <div class="text-muted mb-1">{{ __('Tốc độ trung bình') }}</div>
                                            <span class="fw-bold text-primary">
                                                {{ $response->total_pairs_matched > 0 ? round($response->total_duration_spent / $response->total_pairs_matched, 1) . 's/cặp' : '0s' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-white rounded border">
                                            <div class="text-muted mb-1">{{ __('Tỷ lệ chính xác') }}</div>
                                            @php
                                                $totalClicks = ($response->total_pairs_matched * 2) + ($response->total_mistakes * 2);
                                                $accuracy = $totalClicks > 0 ? round(($response->total_pairs_matched * 2 / $totalClicks) * 100) : 0;
                                            @endphp
                                            <span class="fw-bold text-success">{{ $accuracy }}%</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-white rounded border">
                                            <div class="text-muted mb-1">{{ __('Số lượt hoàn thành') }}</div>
                                            <span class="fw-bold text-purple">{{ count($response->rounds) }} / {{ $response->ageConfig->total_rounds ?? 3 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Rounds Breakdown Table -->
            <div class="card custom-shadow">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-primary">
                        <i class="ti ti-list-details me-1"></i> {{ __('Chi tiết từng lượt game (03 lượt / bài test)') }}
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-striped card-table mb-0">
                            <thead>
                                <tr class="text-muted fs-12 text-uppercase">
                                    <th class="text-center" style="width: 80px;">{{ __('Lượt') }}</th>
                                    <th>{{ __('Thời gian hoàn thành') }}</th>
                                    <th class="text-center">{{ __('Số cặp ghép đúng') }}</th>
                                    <th class="text-center">{{ __('Số lần lật sai') }}</th>
                                    <th class="text-center">{{ __('Điểm lượt này') }}</th>
                                    <th>{{ __('Đánh giá lượt') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($response->rounds as $round)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">
                                                #{{ $round->round_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-clock text-muted"></i>
                                                <span class="fw-bold font-monospace">{{ $round->duration_spent }}s</span>
                                                <small class="text-muted">
                                                    ({{ floor($round->duration_spent / 60) > 0 ? floor($round->duration_spent / 60) . 'm ' : '' }}{{ $round->duration_spent % 60 }}s)
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-success font-monospace fs-14">
                                                <i class="ti ti-check text-success me-1"></i>{{ $round->pairs_matched }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-danger font-monospace fs-14">
                                                <i class="ti ti-x text-danger me-1"></i>{{ $round->mistakes }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-yellow-subtle text-warning border border-warning-subtle fw-bold fs-13">
                                                {{ number_format($round->score, 1) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($round->mistakes == 0)
                                                <span class="badge bg-success-subtle text-success fs-12">
                                                    <i class="ti ti-sparkles me-1"></i> {{ __('Hoàn hảo (Không sai lần nào)') }}
                                                </span>
                                            @elseif ($round->mistakes <= 2)
                                                <span class="badge bg-info-subtle text-info fs-12">
                                                    <i class="ti ti-thumb-up me-1"></i> {{ __('Tốt (Rất ít sai sót)') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning fs-12">
                                                    <i class="ti ti-check me-1"></i> {{ __('Đã hoàn thành') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="ti ti-info-circle me-1"></i> {{ __('Chưa có dữ liệu chi tiết từng lượt cho bài test này') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route(RouteAdminSystem::MEMO_RATING_INDEX) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> {{ __('Quay lại danh sách') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
