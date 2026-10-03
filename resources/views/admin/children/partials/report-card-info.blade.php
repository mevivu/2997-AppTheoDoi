@php
    $stages = collect($reportCardSummary['stages'] ?? [])->keyBy('education_level');
    $highlights = $reportCardSummary['highlights'] ?? [];
    $stageMeta = [
        'primary' => ['title' => 'Tiểu học', 'range' => 'Lớp 1 – 5', 'rule' => 'TT27/2020', 'icon' => 'ti-backpack'],
        'lower_secondary' => ['title' => 'Trung học cơ sở', 'range' => 'Lớp 6 – 9', 'rule' => 'TT22/2021', 'icon' => 'ti-school'],
        'upper_secondary' => ['title' => 'Trung học phổ thông', 'range' => 'Lớp 10 – 12', 'rule' => 'TT22/2021', 'icon' => 'ti-certificate'],
    ];
    $ratingLabels = [
        'xuat_sac' => 'Xuất sắc', 'gioi' => 'Giỏi', 'kha' => 'Khá', 'dat' => 'Đạt', 'chua_dat' => 'Chưa đạt',
        'hoan_thanh_xuat_sac' => 'Hoàn thành xuất sắc', 'hoan_thanh_tot' => 'Hoàn thành tốt',
        'hoan_thanh' => 'Hoàn thành', 'chua_hoan_thanh' => 'Chưa hoàn thành',
    ];
    $ratingClasses = [
        'xuat_sac' => 'rc-result-excellent', 'hoan_thanh_xuat_sac' => 'rc-result-excellent',
        'gioi' => 'rc-result-good', 'hoan_thanh_tot' => 'rc-result-good',
        'kha' => 'rc-result-fair', 'dat' => 'rc-result-fair', 'hoan_thanh' => 'rc-result-fair',
        'chua_dat' => 'rc-result-alert', 'chua_hoan_thanh' => 'rc-result-alert',
    ];
    $formatSemester = function ($semester) use ($ratingLabels, $ratingClasses) {
        if (!$semester || ($semester['average_score'] === null && empty($semester['academic_performance']))) return null;
        $rating = $semester['academic_performance'] ?? null;
        return [
            'score' => $semester['average_score'],
            'label' => $ratingLabels[$rating] ?? ($rating ?: 'Đã cập nhật'),
            'class' => $ratingClasses[$rating] ?? 'rc-result-neutral',
        ];
    };
@endphp

@push('libs-css')
<style>
    .rc-shell { --rc-blue:#1769aa; --rc-teal:#0f8f83; --rc-ink:#172033; --rc-muted:#667085; }
    .rc-intro { background:linear-gradient(135deg,#eaf5ff 0%,#eefbf9 100%); border:1px solid #d8eaf3; border-radius:16px; padding:20px; }
    .rc-intro-icon { width:48px; height:48px; display:grid; place-items:center; border-radius:14px; color:#fff; background:linear-gradient(135deg,var(--rc-blue),var(--rc-teal)); font-size:25px; box-shadow:0 8px 18px rgba(23,105,170,.18); }
    .rc-stage { border:1px solid #e5eaf0; border-radius:14px; box-shadow:0 4px 14px rgba(16,24,40,.04); }
    .rc-stage-icon { width:40px; height:40px; display:grid; place-items:center; border-radius:11px; background:#eaf5ff; color:var(--rc-blue); font-size:20px; }
    .rc-progress { height:6px; background:#edf1f5; border-radius:9px; overflow:hidden; }
    .rc-progress > span { display:block; height:100%; background:linear-gradient(90deg,var(--rc-blue),var(--rc-teal)); border-radius:9px; }
    .rc-highlight { border:1px solid #e5eaf0; border-radius:14px; background:#fff; }
    .rc-subject { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:9px; background:#f3f8fc; color:#255477; font-size:12px; font-weight:600; }
    .rc-table-card { border:1px solid #e5eaf0; border-radius:14px; overflow:hidden; background:#fff; }
    .rc-table thead th { background:#f6f9fb; color:#667085; font-size:11px; letter-spacing:.04em; text-transform:uppercase; border-bottom:1px solid #e5eaf0; padding:12px; }
    .rc-table tbody td { padding:13px 12px; border-color:#edf0f3; }
    .rc-table tbody tr:hover { background:#f8fbfd; }
    .rc-class { display:flex; align-items:center; gap:10px; min-width:130px; }
    .rc-class-number { width:34px; height:34px; display:grid; place-items:center; border-radius:10px; background:#eaf5ff; color:var(--rc-blue); font-weight:700; }
    .rc-result { display:inline-flex; flex-direction:column; align-items:center; min-width:92px; padding:6px 9px; border-radius:9px; line-height:1.2; }
    .rc-result strong { font-size:14px; } .rc-result small { font-size:10px; margin-top:3px; }
    .rc-result-excellent { background:#e8f7f3; color:#08796e; } .rc-result-good { background:#e9f3ff; color:#1769aa; }
    .rc-result-fair { background:#fff6df; color:#9a6700; } .rc-result-alert { background:#fff0f0; color:#c13a3a; }
    .rc-result-neutral { background:#f2f4f7; color:#475467; } .rc-empty { color:#b0b8c4; font-size:12px; }
    @media(max-width:767px){ .rc-intro{padding:16px}.rc-table{min-width:720px} }
</style>
@endpush

<div class="rc-shell">
    <div class="rc-intro mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rc-intro-icon"><i class="ti ti-report"></i></div>
            <div>
                <h4 class="mb-1" style="color:var(--rc-ink)">Học bạ điện tử</h4>
                <div class="text-muted">Tổng hợp kết quả học tập từ lớp 1 đến lớp 12 của {{ $children->fullname }}.</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        @foreach($stageMeta as $key => $meta)
            @php
                $stage = $stages->get($key, []); $done = (int)($stage['classes_with_full_year'] ?? 0);
                $total = (int)($stage['classes_total'] ?? count($stage['classes'] ?? []));
                $percent = $total ? min(100, round($done * 100 / $total)) : 0; $latest = $stage['latest_rating'] ?? null;
            @endphp
            <div class="col-12 col-md-4">
                <div class="rc-stage h-100 p-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rc-stage-icon"><i class="ti {{ $meta['icon'] }}"></i></div>
                        <div><div class="fw-bold">{{ $meta['title'] }}</div><small class="text-muted">{{ $meta['range'] }} · {{ $meta['rule'] }}</small></div>
                    </div>
                    <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Đã hoàn thành</span><strong>{{ $done }}/{{ $total }} lớp</strong></div>
                    <div class="rc-progress mb-3"><span style="width:{{ $percent }}%"></span></div>
                    <div class="small text-muted">Kết quả gần nhất</div>
                    <div class="fw-semibold mt-1">{{ $latest ? $latest['class'].' · '.($ratingLabels[$latest['value']] ?? $latest['label']) : 'Chưa có dữ liệu' }}</div>
                </div>
            </div>
        @endforeach
    </div>

    @if(!empty($highlights['strong_subjects']) || !empty($highlights['need_attention']))
        <div class="rc-highlight p-3 mb-3">
            <div class="row g-3">
                <div class="col-md-6"><div class="fw-semibold mb-2"><i class="ti ti-rosette text-success me-1"></i>Môn học nổi bật</div><div class="d-flex flex-wrap gap-2">@forelse($highlights['strong_subjects'] ?? [] as $subject)<span class="rc-subject">{{ $subject['name'] }} <strong>{{ number_format($subject['value'],1) }}</strong></span>@empty<span class="text-muted small">Chưa đủ dữ liệu</span>@endforelse</div></div>
                <div class="col-md-6"><div class="fw-semibold mb-2"><i class="ti ti-bulb text-warning me-1"></i>Môn cần quan tâm</div><div class="d-flex flex-wrap gap-2">@forelse($highlights['need_attention'] ?? [] as $subject)<span class="rc-subject">{{ $subject['name'] }} <strong>{{ number_format($subject['value'],1) }}</strong></span>@empty<span class="text-muted small">Không có môn dưới ngưỡng khuyến nghị</span>@endforelse</div></div>
            </div>
        </div>
    @endif

    <div class="rc-table-card">
        <div class="p-3 border-bottom"><h5 class="mb-1">Kết quả theo năm học</h5><div class="text-muted small">Thông tin chỉ dùng để xem và đối chiếu.</div></div>
        <div class="table-responsive">
            <table class="table table-vcenter rc-table mb-0 text-center">
                <thead><tr><th class="text-start">Lớp</th><th>Học kỳ 1</th><th>Học kỳ 2</th><th>Cả năm</th><th>Ảnh học bạ</th></tr></thead>
                <tbody>
                @foreach($stages as $stage)
                    @foreach($stage['classes'] ?? [] as $class)
                        @php
                            $classId = $class['class_id']; $name = $class['name'] ?? ('Lớp '.$classId);
                            $semester1 = $formatSemester($class['semesters']['semester_1'] ?? null);
                            $semester2 = $formatSemester($class['semesters']['semester_2'] ?? null);
                            $fullYear = $formatSemester($class['semesters']['full_year'] ?? null);
                        @endphp
                        <tr>
                            <td><div class="rc-class"><span class="rc-class-number">{{ $classId }}</span><div class="text-start"><strong>{{ $name }}</strong><div class="text-muted small">Khối {{ $classId }}</div></div></div></td>
                            @foreach([$semester1,$semester2,$fullYear] as $result)
                                <td>@if($result)<span class="rc-result {{ $result['class'] }}">@if($result['score'] !== null)<strong>{{ number_format($result['score'],1) }}</strong>@endif<small>{{ $result['label'] }}</small></span>@else<span class="rc-empty">Chưa cập nhật</span>@endif</td>
                            @endforeach
                            <td>@if(($class['attachments_count'] ?? 0)>0)<span class="badge bg-azure-lt text-azure"><i class="ti ti-photo me-1"></i>{{ $class['attachments_count'] }} ảnh</span>@else<span class="rc-empty">Chưa có ảnh</span>@endif</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
