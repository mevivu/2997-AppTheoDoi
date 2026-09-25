<div class="d-flex flex-wrap gap-1 align-items-center justify-content-center" style="max-width: 260px; margin: 0 auto;">
    @php
        $items = $competition_themes ?? ($competitionThemes ?? []);
    @endphp
    @forelse ($items as $ct)
        @php
            $order = is_array($ct) ? ($ct['game_order'] ?? '') : ($ct->game_order ?? '');
            $themeName = is_array($ct) ? ($ct['theme']['name'] ?? __('Chủ đề')) : ($ct->theme?->name ?? __('Chủ đề'));
        @endphp
        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1 px-2" style="font-size: 11px;">
            <span class="badge bg-primary text-white rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 16px; height: 16px; font-size: 10px;">{{ $order }}</span>
            <span>{{ $themeName }}</span>
        </span>
    @empty
        <span class="text-muted fs-11 fst-italic">{{ __('Chưa chọn chủ đề') }}</span>
    @endforelse
</div>
