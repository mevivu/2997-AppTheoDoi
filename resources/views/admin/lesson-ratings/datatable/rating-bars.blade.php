@php
    $total = (int) ($total_ratings ?? 0);
    $easy = (int) ($easy_count ?? 0);
    $help = (int) ($with_help_count ?? 0);
    $hard = (int) ($hard_count ?? 0);

    $easyPct = $total > 0 ? round(($easy / $total) * 100) : 0;
    $helpPct = $total > 0 ? round(($help / $total) * 100) : 0;
    $hardPct = $total > 0 ? (100 - $easyPct - $helpPct) : 0;
    if ($hardPct < 0) $hardPct = 0;
@endphp

@if($total > 0)
    <div style="min-width: 170px;">
        <div class="progress progress-sm rounded-pill overflow-hidden shadow-none bg-light mb-1" style="height: 8px;">
            @if($easyPct > 0)
                <div class="progress-bar bg-success" style="width: {{ $easyPct }}%;" data-bs-toggle="tooltip" title="Dễ: {{ $easyPct }}% ({{ $easy }})"></div>
            @endif
            @if($helpPct > 0)
                <div class="progress-bar bg-warning" style="width: {{ $helpPct }}%;" data-bs-toggle="tooltip" title="Có trợ giúp: {{ $helpPct }}% ({{ $help }})"></div>
            @endif
            @if($hardPct > 0)
                <div class="progress-bar bg-danger" style="width: {{ $hardPct }}%;" data-bs-toggle="tooltip" title="Khó: {{ $hardPct }}% ({{ $hard }})"></div>
            @endif
        </div>
        <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 11px;">
            <span class="text-success fw-medium">😊 {{ $easyPct }}%</span>
            <span class="text-warning fw-medium">👋 {{ $helpPct }}%</span>
            <span class="text-danger fw-medium">🔥 {{ $hardPct }}%</span>
        </div>
    </div>
@else
    <span class="text-muted fst-italic fs-12">Chưa có đánh giá</span>
@endif
