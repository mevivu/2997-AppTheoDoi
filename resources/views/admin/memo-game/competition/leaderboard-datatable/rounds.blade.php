@php
    $roundsCollection = $rounds ?? ($entry?->rounds ?? collect());
    $roundsData = is_iterable($roundsCollection) 
        ? (is_object($roundsCollection) && method_exists($roundsCollection, 'sortBy') 
            ? $roundsCollection->sortBy('game_number')->values() 
            : collect($roundsCollection)->sortBy('game_number')->values()) 
        : collect();

    // Đếm số ván thắng thực tế từ danh sách rounds
    $actualWonCount = $roundsData->filter(function ($r) {
        $wonVal = is_object($r) ? $r->is_won : ($r['is_won'] ?? false);
        return filter_var($wonVal, FILTER_VALIDATE_BOOLEAN);
    })->count();

    $storedWon = (int) ($games_won ?? ($entry?->games_won ?? 0));
    $displayWon = max($storedWon, $actualWonCount);
@endphp
<div class="mini-rounds-strip justify-content-center">
    @for ($g = 1; $g <= 4; $g++)
        @php
            $r = is_object($roundsData) && method_exists($roundsData, 'firstWhere') 
                ? $roundsData->firstWhere('game_number', $g)
                : collect($roundsData)->firstWhere('game_number', $g);
            
            $hasRound = !empty($r);
            $rawWon = is_object($r) ? $r->is_won : ($r['is_won'] ?? false);
            $isWon = $hasRound && filter_var($rawWon, FILTER_VALIDATE_BOOLEAN);
            $duration = is_object($r) ? ($r->duration_spent ?? 0) : ($r['duration_spent'] ?? 0);
            $moves = is_object($r) ? ($r->total_moves ?? 0) : ($r['total_moves'] ?? 0);
        @endphp
        @if ($hasRound && $isWon)
            <span class="mini-round-chip round-won" 
                  data-bs-toggle="tooltip" 
                  title="Ván {{ $g }}: Thắng ({{ $duration }}s, {{ $moves }} lật)">
                ✓
            </span>
        @elseif ($hasRound)
            <span class="mini-round-chip round-lost" 
                  data-bs-toggle="tooltip" 
                  title="Ván {{ $g }}: Thất bại/Chưa hoàn thành ({{ $duration }}s, {{ $moves }} lật)">
                ✕
            </span>
        @else
            <span class="mini-round-chip round-pending" 
                  data-bs-toggle="tooltip" 
                  title="Ván {{ $g }}: Chưa thi">
                {{ $g }}
            </span>
        @endif
    @endfor
</div>
<div class="text-muted mt-1 fw-medium" style="font-size: 11px;">
    {{ $displayWon }}/4 ván thắng
</div>

