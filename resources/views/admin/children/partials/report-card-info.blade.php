@php
    $stages = collect($reportCardSummary['stages'] ?? [])
        ->keyBy('education_level')
        ->all();
    $highlights = $reportCardSummary['highlights'] ?? [];

    $ratingBadges = [
        'xuat_sac' => ['badge' => 'bg-purple-lt text-purple', 'label' => 'Xuất sắc'],
        'gioi' => ['badge' => 'bg-success-lt text-success', 'label' => 'Giỏi'],
        'kha' => ['badge' => 'bg-primary-lt text-primary', 'label' => 'Khá'],
        'dat' => ['badge' => 'bg-warning-lt text-warning', 'label' => 'Đạt'],
        'chua_dat' => ['badge' => 'bg-danger-lt text-danger', 'label' => 'Chưa đạt'],
        'hoan_thanh_xuat_sac' => ['badge' => 'bg-purple-lt text-purple', 'label' => 'HT Xuất sắc'],
        'hoan_thanh_tot' => ['badge' => 'bg-success-lt text-success', 'label' => 'HT Tốt'],
        'hoan_thanh' => ['badge' => 'bg-warning-lt text-warning', 'label' => 'Hoàn thành'],
        'chua_hoan_thanh' => ['badge' => 'bg-danger-lt text-danger', 'label' => 'Chưa HT'],
    ];

    $statusBadges = [
        'calculated' => ['badge' => 'badge-outline text-success border-success', 'label' => 'Đã tính'],
        'manual' => ['badge' => 'badge-outline text-primary border-primary', 'label' => 'Nhập tay'],
        'incomplete' => ['badge' => 'badge-outline text-warning border-warning', 'label' => 'Thiếu điểm'],
        'overridden' => ['badge' => 'badge-outline text-info border-info', 'label' => 'Ghi đè'],
        'error' => ['badge' => 'badge-outline text-danger border-danger', 'label' => 'Lỗi'],
    ];
@endphp

<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-book-2 text-warning fs-4"></i>
                    {{ __('Học Bạ Điện Tử & Đánh Giá Kết Quả Học Tập') }}
                </h5>
                <small class="text-muted">
                    {{ __('Chuẩn hóa thuật toán xếp loại học lực theo Thông tư 27 (Tiểu học) & Thông tư 22 (THCS / THPT) của Bộ GD&ĐT') }}
                </small>
            </div>
            <span class="badge bg-warning-lt px-3 py-2 fs-13 rounded-pill fw-bold">
                <i class="ti ti-school me-1"></i> {{ __('12 Khối Lớp') }}
            </span>
        </div>
    </div>

    <!-- 3 Thẻ Tổng Quan Cấp Học (Tiểu học, THCS, THPT) -->
    <div class="col-12">
        <div class="row g-3">
            @php
                $stageCards = [
                    'primary' => [
                        'title' => 'Cấp Tiểu Học',
                        'classes' => 'Lớp 1 - 5',
                        'reg' => 'Thông tư 27/2020',
                        'icon' => 'ti-backpack',
                        'color' => 'success',
                    ],
                    'lower_secondary' => [
                        'title' => 'Cấp Trung Học Cơ Sở',
                        'classes' => 'Lớp 6 - 9',
                        'reg' => 'Thông tư 22/2021',
                        'icon' => 'ti-school',
                        'color' => 'primary',
                    ],
                    'upper_secondary' => [
                        'title' => 'Cấp Trung Học Phổ Thông',
                        'classes' => 'Lớp 10 - 12',
                        'reg' => 'Thông tư 22/2021',
                        'icon' => 'ti-certificate',
                        'color' => 'warning',
                    ],
                ];
            @endphp

            @foreach($stageCards as $sKey => $sConf)
                @php
                    $sData = $stages[$sKey] ?? null;
                    $compYears = $sData['classes_with_full_year'] ?? 0;
                    $totYears = $sData['classes_total'] ?? count($sData['classes'] ?? []);
                    $latestRat = $sData['latest_rating'] ?? null;
                @endphp
                <div class="col-12 col-md-4">
                    <div class="card p-3 border border-light-subtle rounded-3 bg-white h-100 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-{{ $sConf['color'] }}-lt rounded-2 fs-4">
                                    <i class="ti {{ $sConf['icon'] }} text-{{ $sConf['color'] }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark fs-14">{{ $sConf['title'] }}</h6>
                                    <span class="text-muted fs-11">{{ $sConf['classes'] }} • <span class="badge bg-light text-muted fs-10">{{ $sConf['reg'] }}</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12 mt-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ __('Năm học hoàn thành:') }}</span>
                                <strong class="text-dark">{{ $compYears }} / {{ $totYears }} {{ __('năm') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">{{ __('Xếp loại gần nhất:') }}</span>
                                @if($latestRat)
                                    @php
                                        $bInfo = $ratingBadges[$latestRat['value']] ?? ['badge' => 'bg-secondary text-white', 'label' => $latestRat['label']];
                                    @endphp
                                    <span class="badge {{ $bInfo['badge'] }} fw-semibold fs-11 px-2 py-1">
                                        {{ $latestRat['class'] }}: {{ $bInfo['label'] }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted fs-11">{{ __('Chưa có dữ liệu') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Highlights (Môn học thế mạnh / Cần cải thiện nếu có) -->
    @if(!empty($highlights['strong_subjects']) || !empty($highlights['need_attention']))
        <div class="col-12">
            <div class="card p-3 border border-light-subtle rounded-3 bg-white shadow-sm">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="ti ti-star-filled text-warning"></i>
                            <h6 class="mb-0 fw-semibold text-slate fs-13">{{ __('Môn Học Thế Mạnh (Điểm TB Cao Nhất)') }}</h6>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            @forelse($highlights['strong_subjects'] ?? [] as $topSub)
                                <span class="badge bg-success-lt text-success px-2 py-1 fs-12">
                                    {{ $topSub['name'] }}: <strong>{{ number_format($topSub['value'], 1) }}</strong>
                                </span>
                            @empty
                                <span class="text-muted fs-12">{{ __('Chưa đủ dữ liệu điểm số') }}</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="ti ti-trending-up text-primary"></i>
                            <h6 class="mb-0 fw-semibold text-slate fs-13">{{ __('Môn Cần Quan Tâm & Bồi Dưỡng Thêm') }}</h6>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            @forelse($highlights['need_attention'] ?? [] as $impSub)
                                <span class="badge bg-warning-lt text-warning px-2 py-1 fs-12">
                                    {{ $impSub['name'] }}: <strong>{{ number_format($impSub['value'], 1) }}</strong>
                                </span>
                            @empty
                                <span class="text-muted fs-12">{{ __('Không có môn nào dưới ngưỡng khuyến nghị') }}</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bảng Chi Tiết Điểm Số & Xếp Loại 12 Lớp Học -->
    <div class="col-12">
        <div class="card border border-light-subtle rounded-3 bg-white shadow-sm overflow-hidden">
            <div class="card-header py-2 px-3 bg-light-subtle border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-table text-primary"></i>
                    <h6 class="card-title mb-0 fs-14 fw-bold">{{ __('Bảng Tổng Hợp Kết Quả 12 Khối Lớp') }}</h6>
                </div>
                <div class="fs-12 text-muted">
                    {{ __('Nhấp nút') }} <span class="badge bg-light text-dark"><i class="ti ti-zoom-check"></i> Chẩn đoán</span> {{ __('để kiểm tra từng tiêu chí thuật toán') }}
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter table-hover text-center align-middle mb-0 fs-13">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th style="width: 100px;">{{ __('Khối Lớp') }}</th>
                            <th>{{ __('Học Kỳ 1') }}</th>
                            <th>{{ __('Học Kỳ 2') }}</th>
                            <th>{{ __('Cả Năm') }}</th>
                            <th style="width: 120px;">{{ __('Ảnh Học Bạ') }}</th>
                            <th style="width: 140px;">{{ __('Thao Tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stages as $stageKey => $stageData)
                            @foreach($stageData['classes'] as $cls)
                                @php
                                    $cid = $cls['class_id'];
                                    $cname = $cls['name'] ?? ('Lớp ' . $cid);
                                    $hk1 = $cls['semesters']['semester_1'] ?? null;
                                    $hk2 = $cls['semesters']['semester_2'] ?? null;
                                    $cn = $cls['semesters']['full_year'] ?? null;
                                    $attCount = (int) ($cls['attachments_count'] ?? 0);
                                @endphp
                                <tr>
                                    <!-- Cột Lớp -->
                                    <td class="fw-bold text-start ps-3">
                                        <span class="badge bg-light text-dark fs-12 px-2 py-1">
                                            {{ $cname }}
                                        </span>
                                    </td>

                                    <!-- Cột Học kỳ 1 -->
                                    <td>
                                        @if($hk1 && ($hk1['academic_performance'] || $hk1['average_score'] !== null))
                                            @php
                                                $rVal = $hk1['academic_performance'] ?? '';
                                                $bInfo = $ratingBadges[$rVal] ?? ['badge' => 'bg-secondary text-white', 'label' => $rVal ?: 'Chưa XL'];
                                                $sVal = $hk1['calculation_status'] ?? 'empty';
                                                $sInfo = $statusBadges[$sVal] ?? ['badge' => 'badge-outline text-muted', 'label' => $sVal];
                                            @endphp
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge {{ $bInfo['badge'] }} px-2 py-1">{{ $bInfo['label'] }}</span>
                                                    @if($hk1['average_score'] !== null)
                                                        <span class="badge bg-light text-dark fw-bold">{{ number_format($hk1['average_score'], 1) }}</span>
                                                    @endif
                                                </div>
                                                <span class="badge {{ $sInfo['badge'] }} fs-10 px-1 py-0">{{ $sInfo['label'] }}</span>
                                                <button type="button" class="btn btn-ghost-primary btn-sm px-1 py-0 fs-11 btn-debug-report-card"
                                                    data-child-id="{{ $children->id }}"
                                                    data-class-id="{{ $cid }}"
                                                    data-semester="semester_1"
                                                    data-class-name="{{ $cname }}"
                                                    data-semester-label="Học kỳ 1"
                                                    title="{{ __('Xem chẩn đoán chi tiết HK1') }}">
                                                    <i class="ti ti-zoom-check me-1"></i>Chẩn đoán
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-muted fs-11">
                                                <span>--</span>
                                                <div>
                                                    <button type="button" class="btn btn-ghost-secondary btn-sm px-1 py-0 fs-10 btn-debug-report-card"
                                                        data-child-id="{{ $children->id }}"
                                                        data-class-id="{{ $cid }}"
                                                        data-semester="semester_1"
                                                        data-class-name="{{ $cname }}"
                                                        data-semester-label="Học kỳ 1">
                                                        <i class="ti ti-zoom-check me-1"></i>Kiểm tra
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Cột Học kỳ 2 -->
                                    <td>
                                        @if($hk2 && ($hk2['academic_performance'] || $hk2['average_score'] !== null))
                                            @php
                                                $rVal = $hk2['academic_performance'] ?? '';
                                                $bInfo = $ratingBadges[$rVal] ?? ['badge' => 'bg-secondary text-white', 'label' => $rVal ?: 'Chưa XL'];
                                                $sVal = $hk2['calculation_status'] ?? 'empty';
                                                $sInfo = $statusBadges[$sVal] ?? ['badge' => 'badge-outline text-muted', 'label' => $sVal];
                                            @endphp
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge {{ $bInfo['badge'] }} px-2 py-1">{{ $bInfo['label'] }}</span>
                                                    @if($hk2['average_score'] !== null)
                                                        <span class="badge bg-light text-dark fw-bold">{{ number_format($hk2['average_score'], 1) }}</span>
                                                    @endif
                                                </div>
                                                <span class="badge {{ $sInfo['badge'] }} fs-10 px-1 py-0">{{ $sInfo['label'] }}</span>
                                                <button type="button" class="btn btn-ghost-primary btn-sm px-1 py-0 fs-11 btn-debug-report-card"
                                                    data-child-id="{{ $children->id }}"
                                                    data-class-id="{{ $cid }}"
                                                    data-semester="semester_2"
                                                    data-class-name="{{ $cname }}"
                                                    data-semester-label="Học kỳ 2"
                                                    title="{{ __('Xem chẩn đoán chi tiết HK2') }}">
                                                    <i class="ti ti-zoom-check me-1"></i>Chẩn đoán
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-muted fs-11">
                                                <span>--</span>
                                                <div>
                                                    <button type="button" class="btn btn-ghost-secondary btn-sm px-1 py-0 fs-10 btn-debug-report-card"
                                                        data-child-id="{{ $children->id }}"
                                                        data-class-id="{{ $cid }}"
                                                        data-semester="semester_2"
                                                        data-class-name="{{ $cname }}"
                                                        data-semester-label="Học kỳ 2">
                                                        <i class="ti ti-zoom-check me-1"></i>Kiểm tra
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Cột Cả Năm -->
                                    <td>
                                        @if($cn && ($cn['academic_performance'] || $cn['average_score'] !== null))
                                            @php
                                                $rVal = $cn['academic_performance'] ?? '';
                                                $bInfo = $ratingBadges[$rVal] ?? ['badge' => 'bg-secondary text-white', 'label' => $rVal ?: 'Chưa XL'];
                                                $sVal = $cn['calculation_status'] ?? 'empty';
                                                $sInfo = $statusBadges[$sVal] ?? ['badge' => 'badge-outline text-muted', 'label' => $sVal];
                                            @endphp
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge {{ $bInfo['badge'] }} px-2 py-1">{{ $bInfo['label'] }}</span>
                                                    @if($cn['average_score'] !== null)
                                                        <span class="badge bg-light text-dark fw-bold">{{ number_format($cn['average_score'], 1) }}</span>
                                                    @endif
                                                </div>
                                                <span class="badge {{ $sInfo['badge'] }} fs-10 px-1 py-0">{{ $sInfo['label'] }}</span>
                                                <button type="button" class="btn btn-ghost-primary btn-sm px-1 py-0 fs-11 btn-debug-report-card"
                                                    data-child-id="{{ $children->id }}"
                                                    data-class-id="{{ $cid }}"
                                                    data-semester="full_year"
                                                    data-class-name="{{ $cname }}"
                                                    data-semester-label="Cả năm"
                                                    title="{{ __('Xem chẩn đoán chi tiết Cả năm') }}">
                                                    <i class="ti ti-zoom-check me-1"></i>Chẩn đoán
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-muted fs-11">
                                                <span>--</span>
                                                <div>
                                                    <button type="button" class="btn btn-ghost-secondary btn-sm px-1 py-0 fs-10 btn-debug-report-card"
                                                        data-child-id="{{ $children->id }}"
                                                        data-class-id="{{ $cid }}"
                                                        data-semester="full_year"
                                                        data-class-name="{{ $cname }}"
                                                        data-semester-label="Cả năm">
                                                        <i class="ti ti-zoom-check me-1"></i>Kiểm tra
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Cột Ảnh Học Bạ -->
                                    <td>
                                        @if($attCount > 0)
                                            <span class="badge bg-azure-lt text-azure px-2 py-1 fs-11">
                                                <i class="ti ti-paperclip me-1"></i>{{ $attCount }} {{ __('ảnh') }}
                                            </span>
                                        @else
                                            <span class="text-muted fs-11">{{ __('0 ảnh') }}</span>
                                        @endif
                                    </td>

                                    <!-- Cột Thao Tác -->
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle px-2 py-1 fs-12" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-settings me-1"></i>{{ __('Tính Lại') }}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm fs-12">
                                                <li>
                                                    <a class="dropdown-item btn-recalculate-report-card" href="javascript:void(0)"
                                                        data-child-id="{{ $children->id }}"
                                                        data-class-id="{{ $cid }}"
                                                        data-semester="semester_1"
                                                        data-class-name="{{ $cname }}"
                                                        data-semester-label="Học kỳ 1">
                                                        <i class="ti ti-calculator text-primary me-2"></i>{{ __('Tính lại Học kỳ 1') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item btn-recalculate-report-card" href="javascript:void(0)"
                                                        data-child-id="{{ $children->id }}"
                                                        data-class-id="{{ $cid }}"
                                                        data-semester="semester_2"
                                                        data-class-name="{{ $cname }}"
                                                        data-semester-label="Học kỳ 2">
                                                        <i class="ti ti-calculator text-primary me-2"></i>{{ __('Tính lại Học kỳ 2') }}
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <a class="dropdown-item btn-recalculate-report-card" href="javascript:void(0)"
                                                        data-child-id="{{ $children->id }}"
                                                        data-class-id="{{ $cid }}"
                                                        data-semester="full_year"
                                                        data-class-name="{{ $cname }}"
                                                        data-semester-label="Cả năm">
                                                        <i class="ti ti-refresh text-success me-2"></i>{{ __('Tính lại Cả Năm') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Modal Chẩn Đoán Học Bạ -->
@include('admin.children.partials.report-card-debug-modal', ['children' => $children])
