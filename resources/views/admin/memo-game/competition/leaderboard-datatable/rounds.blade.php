@php
    $roundsCollection = $rounds ?? ($entry?->rounds ?? collect());
    $roundsData = is_iterable($roundsCollection) ? (is_object($roundsCollection) && method_exists($roundsCollection, 'sortBy') ? $roundsCollection->sortBy('game_number')->values() : collect($roundsCollection)->sortBy('game_number')->values()) : collect();
    $gamesWonVal = $games_won ?? ($entry?->games_won ?? 0);
@endphp
<div class="mini-rounds-strip justify-content-center">
    @for ($g = 1; $g <= 4; $g++)
        @php
            $r = is_object($roundsData) && method_exists($roundsData, 'firstWhere') 
                ? $roundsData->firstWhere('game_number', $g)
                : collect($roundsData)->firstWhere('game_number', $g);
            $isWon = is_object($r) ? (bool) $r->is_won : (bool) ($r['is_won'] ?? false);
            $duration = is_object($r) ? ($r->duration_spent ?? 0) : ($r['duration_spent'] ?? 0);
            $moves = is_object($r) ? ($r->total_moves ?? 0) : ($r['total_moves'] ?? 0);
        @endphp
        @if ($r && $isWon)
            <span class="mini-round-chip round-won" title="Ván {{ $g }}: Thắng ({{ $duration }}s, {{ $moves }} lật)">
                ✓
            </span>
        @elseif ($r)
            <span class="mini-round-chip round-lost" title="Ván {{ $g }}: Chưa hoàn thành">
                ✕
            </span>
        @else
            <span class="mini-round-chip round-pending" title="Ván {{ $g }}: Chưa thi">
                {{ $g }}
            </span>
        @endif
    @endfor
</div>
<div class="text-muted mt-1 fw-medium" style="font-size: 11px;">
    {{ $gamesWonVal }}/4 ván thắng
</div>
