@php
    use App\Traits\RouteAdminSystem;
@endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
    <style>
        /* Hero Banner */
        .iq-hero-banner {
            background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 50%, #faf5ff 100%);
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.06);
            margin-bottom: 1.5rem;
        }

        .iq-hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563eb 0%, #8b5cf6 50%, #ec4899 100%);
        }

        .iq-age-badge {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            font-weight: 800;
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
            flex-shrink: 0;
        }

        .iq-stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 6px 12px;
            border-radius: 99px;
            font-size: 0.82rem;
            color: #475569;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            white-space: nowrap;
        }

        .iq-stat-pill strong {
            color: #0f172a;
            font-weight: 700;
        }

        /* Section Cards */
        .iq-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .iq-card:hover {
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.07);
            border-color: #cbd5e1;
        }

        .iq-card-header {
            padding: 1rem 1.35rem;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .iq-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Focus Well for Core Math Parameters */
        .iq-params-well {
            background: linear-gradient(135deg, #f0f7ff 0%, #f8faff 100%);
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            padding: 1.15rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .iq-params-badge {
            position: absolute;
            top: -10px;
            right: 16px;
            background: #2563eb;
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 2px 10px;
            border-radius: 99px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        /* Simulator Card */
        .iq-sim-card {
            background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
            border: 1px solid #c7d2fe;
            border-radius: 16px;
            box-shadow: 0 6px 24px rgba(79, 70, 229, 0.07);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .iq-sim-header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #3b82f6 100%);
            color: #ffffff;
            padding: 1rem 1.35rem;
        }

        .iq-score-display {
            font-size: 3.25rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
            color: #1e1b4b;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .iq-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 8px;
            border-radius: 6px;
            background: #e2e8f0;
            outline: none;
            transition: background 0.2s;
        }

        .iq-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.45);
            border: 3px solid #ffffff;
            transition: transform 0.15s ease;
        }

        .iq-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }

        /* 7-Tier Spectrum Bar */
        .iq-spectrum {
            display: flex;
            gap: 4px;
            border-radius: 10px;
            overflow: hidden;
            padding: 4px;
            background: #f1f5f9;
        }

        .iq-spectrum-item {
            flex: 1;
            padding: 8px 4px;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: #ffffff;
            border-radius: 6px;
            opacity: 0.45;
            transition: all 0.25s ease;
            position: relative;
        }

        .iq-spectrum-item.active {
            opacity: 1 !important;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
            z-index: 2;
        }

        .iq-spectrum-item.active::after {
            content: '▲';
            position: absolute;
            bottom: -13px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.65rem;
            color: #0f172a;
        }

        /* Wechsler Reference List */
        .wechsler-ref-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 4px;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            font-size: 0.82rem;
            transition: all 0.15s ease;
        }

        .wechsler-ref-item:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .wechsler-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid" style="padding-bottom: 90px;">
            {{-- Header Navigation Banner --}}
            <x-admin.page-header
                class="mb-3"
                icon="brain"
                :title="__('Chỉnh sửa Bảng chuẩn IQ Wechsler')"
                :subtitle="__('Mốc độ tuổi: ') . $response->age . __(' tuổi — Cập nhật phân phối chuẩn Mean (μ), StdDev (σ)')"
                :breadcrumbs="$breadcrumbs"
                :back-route="route(RouteAdminSystem::IQ_NORM_INDEX)"
                :back-title="__('Danh sách mốc tuổi')"
            >
                <x-slot:actions>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill d-inline-flex align-items-center gap-1 px-3"
                            data-bs-toggle="modal" data-bs-target="#modalRecalculateGuide">
                        <i class="ti ti-rotate-clockwise"></i>
                        <span>{{ __('Tái tính toán Ratings') }}</span>
                    </button>
                </x-slot:actions>
            </x-admin.page-header>

            {{-- Visual Hero Anchor & Quick KPI Strip --}}
            <div class="iq-hero-banner">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-auto d-flex align-items-center gap-3">
                        <div class="iq-age-badge" title="{{ __('Độ tuổi áp dụng') }}">
                            <span>{{ $response->age }}</span>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h3 class="mb-0 fw-bold text-dark fs-18">
                                    {{ __('Mốc chuẩn hóa:') }} {{ $response->age }} {{ __('tuổi') }}
                                </h3>
                                <span class="badge {{ $response->status->badge() }}">
                                    {{ $response->status->description() }}
                                </span>
                                @if($response->version)
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        <i class="ti ti-tag me-1"></i>{{ $response->version }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-muted fs-12 mt-1">
                                <i class="ti ti-info-circle me-1 text-primary"></i>
                                {{ __('Công thức Wechsler:') }}
                                <code class="text-indigo fw-bold">IQ = 100 + 15 × (X - {{ number_format($response->mean_score, 2) }}) / {{ number_format($response->sd_score, 2) }}</code>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md d-flex align-items-center justify-content-md-end flex-wrap gap-2">
                        <div class="iq-stat-pill" title="{{ __('Cỡ mẫu khảo sát dùng để chuẩn hóa') }}">
                            <i class="ti ti-users text-purple fs-5"></i>
                            <span>{{ __('Cỡ mẫu:') }} <strong>{{ number_format($response->sample_size ?? 0) }}</strong> {{ __('trẻ') }}</span>
                        </div>

                        <div class="iq-stat-pill" title="{{ __('Điểm thô trung bình của nhóm tuổi') }}">
                            <i class="ti ti-math-function text-primary fs-5"></i>
                            <span>Mean μ: <strong>{{ number_format($response->mean_score, 2) }}</strong></span>
                        </div>

                        <div class="iq-stat-pill" title="{{ __('Độ lệch chuẩn điểm thô') }}">
                            <i class="ti ti-chart-arrows text-azure fs-5"></i>
                            <span>StdDev σ: <strong>{{ number_format($response->sd_score, 2) }}</strong></span>
                        </div>

                        <div class="iq-stat-pill" title="{{ __('Khoảng điểm thô thực tế') }}">
                            <i class="ti ti-arrows-horizontal text-muted fs-5"></i>
                            <span>Min-Max: <strong>{{ $response->min_raw_score ?? 0 }} - {{ $response->max_raw_score ?? 30 }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Form --}}
            <x-form :action="route(RouteAdminSystem::IQ_NORM_UPDATE)" type="put" :validate="true" id="iqNormEditForm">
                <x-input type="hidden" name="id" :value="$response->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.iq_norms.forms.edit-left')
                    @include('admin.iq_norms.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>

    {{-- Modal Xóa mốc chuẩn hóa --}}
    <div class="modal modal-blur fade" id="modalDeleteNorm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <i class="ti ti-alert-triangle text-danger" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold text-dark mt-2">{{ __('Xác nhận xóa mốc chuẩn?') }}</h3>
                    <div class="text-muted fs-13">
                        {{ __('Bạn có chắc chắn muốn xóa mốc độ tuổi') }} <strong>{{ $response->age }} {{ __('tuổi') }}</strong>?
                        <br>{{ __('Hành động này sẽ làm các bài test IQ cho độ tuổi này không tính được điểm Wechsler cho tới khi có mốc chuẩn thay thế.') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row g-2">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                    {{ __('Hủy bỏ') }}
                                </button>
                            </div>
                            <div class="col">
                                <form action="{{ route(RouteAdminSystem::IQ_NORM_DELETE, $response->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="ti ti-trash me-1"></i>{{ __('Xóa mốc này') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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

@push('custom-js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Live Wechsler IQ Simulator Logic
            const slider = document.getElementById('sim_raw_score_slider');
            const numInput = document.getElementById('sim_raw_score_input');
            const meanInput = document.querySelector('input[name="mean_score"]');
            const sdInput = document.querySelector('input[name="sd_score"]');

            const elIqScore = document.getElementById('sim-iq-score');
            const elZScore = document.getElementById('sim-z-score');
            const elPercentile = document.getElementById('sim-percentile');
            const elTierBadge = document.getElementById('sim-tier-badge');
            const elTierDesc = document.getElementById('sim-tier-desc');
            const elFormulaPreview = document.getElementById('sim-formula-preview');

            // Standard Normal CDF approximation (Abramowitz and Stegun)
            function normalCdf(z) {
                const b1 = 0.319381530;
                const b2 = -0.356563782;
                const b3 = 1.781477937;
                const b4 = -1.821255978;
                const b5 = 1.330274429;
                const p = 0.2316419;
                const c = 0.39894228;

                if (z >= 0.0) {
                    const t = 1.0 / (1.0 + p * z);
                    return (1.0 - c * Math.exp(-z * z / 2.0) * t * (t * (t * (t * (t * b5 + b4) + b3) + b2) + b1));
                } else {
                    const t = 1.0 / (1.0 - p * z);
                    return (c * Math.exp(-z * z / 2.0) * t * (t * (t * (t * (t * b5 + b4) + b3) + b2) + b1));
                }
            }

            // Wechsler Classification Table
            function getWechslerClassification(iq) {
                if (iq >= 130) {
                    return {
                        id: 'tier-130',
                        name: 'Rất ưu tú / Xuất sắc',
                        desc: 'Top 2.2% dân số chuẩn (Very Superior)',
                        badgeClass: 'bg-purple text-white',
                        color: '#7c3aed',
                        accentBg: 'rgba(124, 58, 237, 0.1)'
                    };
                }
                if (iq >= 120) {
                    return {
                        id: 'tier-120',
                        name: 'Ưu tú / Thông minh',
                        desc: 'Top 6.7% dân số chuẩn (Superior)',
                        badgeClass: 'bg-blue text-white',
                        color: '#2563eb',
                        accentBg: 'rgba(37, 99, 235, 0.1)'
                    };
                }
                if (iq >= 110) {
                    return {
                        id: 'tier-110',
                        name: 'Trên trung bình',
                        desc: '16.1% dân số chuẩn (High Average)',
                        badgeClass: 'bg-cyan text-white',
                        color: '#06b6d4',
                        accentBg: 'rgba(6, 182, 212, 0.1)'
                    };
                }
                if (iq >= 90) {
                    return {
                        id: 'tier-90',
                        name: 'Trung bình',
                        desc: '50.0% dân số chuẩn (Average)',
                        badgeClass: 'bg-success text-white',
                        color: '#16a34a',
                        accentBg: 'rgba(22, 163, 74, 0.1)'
                    };
                }
                if (iq >= 80) {
                    return {
                        id: 'tier-80',
                        name: 'Dưới trung bình',
                        desc: '16.1% dân số chuẩn (Low Average)',
                        badgeClass: 'bg-warning text-white',
                        color: '#d97706',
                        accentBg: 'rgba(217, 119, 6, 0.1)'
                    };
                }
                if (iq >= 70) {
                    return {
                        id: 'tier-70',
                        name: 'Giới hạn chậm',
                        desc: '6.7% dân số chuẩn (Borderline)',
                        badgeClass: 'bg-orange text-white',
                        color: '#ea580c',
                        accentBg: 'rgba(234, 88, 12, 0.1)'
                    };
                }
                return {
                    id: 'tier-0',
                    name: 'Rất kém / Khuyết tật trí tuệ',
                    desc: '2.2% dân số chuẩn (Extremely Low)',
                    badgeClass: 'bg-danger text-white',
                    color: '#dc2626',
                    accentBg: 'rgba(220, 38, 38, 0.1)'
                };
            }

            function updateSimulator() {
                const rawX = parseFloat(slider?.value || numInput?.value || 0);
                const mean = parseFloat(meanInput?.value || 0);
                const sd = parseFloat(sdInput?.value || 1);

                if (isNaN(rawX) || isNaN(mean) || isNaN(sd) || sd <= 0) {
                    return;
                }

                // Deviation score Z and Wechsler IQ
                const z = (rawX - mean) / sd;
                const iq = Math.round(100 + 15 * z);

                // Percentile
                const pct = normalCdf(z) * 100;
                let pctText = '';
                if (pct >= 99) {
                    pctText = 'Top 1% cao nhất';
                } else if (pct <= 1) {
                    pctText = 'Dưới 1% thấp nhất';
                } else {
                    pctText = `Hạng phân vị: ~${pct.toFixed(1)}%`;
                }

                // Classification tier
                const tier = getWechslerClassification(iq);

                // Update UI elements
                if (elIqScore) {
                    elIqScore.textContent = iq;
                    elIqScore.style.color = tier.color;
                }

                if (elZScore) {
                    const sign = z >= 0 ? '+' : '';
                    elZScore.textContent = `Z = ${sign}${z.toFixed(2)} σ`;
                }

                if (elPercentile) {
                    elPercentile.textContent = pctText;
                }

                if (elTierBadge) {
                    elTierBadge.className = `badge ${tier.badgeClass} fs-13 py-1 px-3`;
                    elTierBadge.textContent = tier.name;
                }

                if (elTierDesc) {
                    elTierDesc.textContent = tier.desc;
                }

                if (elFormulaPreview) {
                    elFormulaPreview.innerHTML = `IQ = 100 + 15 × (${rawX} - ${mean.toFixed(2)}) / ${sd.toFixed(2)} = <strong>${iq}</strong>`;
                }

                // Highlight Spectrum Item
                document.querySelectorAll('.iq-spectrum-item').forEach(item => {
                    item.classList.remove('active');
                });
                const activeItem = document.getElementById(tier.id);
                if (activeItem) {
                    activeItem.classList.add('active');
                }
            }

            // Sync Slider and Number Input
            if (slider && numInput) {
                slider.addEventListener('input', function () {
                    numInput.value = this.value;
                    updateSimulator();
                });

                numInput.addEventListener('input', function () {
                    slider.value = this.value;
                    updateSimulator();
                });
            }

            // Re-calc when Mean or SD changes in form
            if (meanInput) {
                meanInput.addEventListener('input', updateSimulator);
            }
            if (sdInput) {
                sdInput.addEventListener('input', updateSimulator);
            }

            // Initial calculation
            updateSimulator();
        });
    </script>
@endpush
