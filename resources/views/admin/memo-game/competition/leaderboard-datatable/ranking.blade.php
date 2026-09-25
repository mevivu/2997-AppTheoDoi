@if ($ranking === 1)
    <span class="rank-badge rank-1" title="Quán Quân">🥇</span>
@elseif ($ranking === 2)
    <span class="rank-badge rank-2" title="Á Quân">🥈</span>
@elseif ($ranking === 3)
    <span class="rank-badge rank-3" title="Quý Quân">🥉</span>
@elseif ($ranking)
    <span class="rank-badge rank-other">#{{ $ranking }}</span>
@else
    <span class="text-muted fs-12">--</span>
@endif
