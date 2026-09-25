@php
    $childObj = $child ?? ($entry?->child ?? null);
    $userObj = is_object($childObj) ? ($childObj->user ?? null) : ($childObj['user'] ?? null);
    $childName = is_object($childObj) ? ($childObj->fullname ?? 'Thí sinh') : ($childObj['fullname'] ?? 'Thí sinh');
    $avatar = is_object($childObj) ? ($childObj->avatar ? asset($childObj->avatar) : asset('images/avatar-default.png')) : asset('images/avatar-default.png');
    $parentName = is_object($userObj) ? ($userObj->fullname ?? '--') : ($userObj['fullname'] ?? '--');
    $rawPhone = is_object($userObj) ? ($userObj->decrypted_phone ?? $userObj->phone) : ($userObj['phone'] ?? null);
    $cleanPhone = $rawPhone ? preg_replace('/[^0-9]/', '', $rawPhone) : '';
    $formattedPhone = (strlen($cleanPhone) === 10) ? substr($cleanPhone, 0, 4) . ' ' . substr($cleanPhone, 4, 3) . ' ' . substr($cleanPhone, 7) : $rawPhone;

    $roundsCollection = $rounds ?? ($entry?->rounds ?? collect());
    $roundsData = is_iterable($roundsCollection) ? (is_object($roundsCollection) && method_exists($roundsCollection, 'sortBy') ? $roundsCollection->sortBy('game_number')->values() : collect($roundsCollection)->sortBy('game_number')->values()) : collect();
    $roundsList = [];
    foreach ($roundsData as $rnd) {
        $theme = is_object($rnd) ? ($rnd->theme ?? null) : ($rnd['theme'] ?? null);
        $themeName = is_object($theme) ? ($theme->name ?? 'Chủ đề') : ($theme['name'] ?? 'Chủ đề');
        $themeIcon = is_object($theme) ? ($theme->icon ? asset($theme->icon) : null) : (!empty($theme['icon']) ? asset($theme['icon']) : null);
        $isWon = is_object($rnd) ? (bool) $rnd->is_won : (bool) ($rnd['is_won'] ?? false);
        $duration = is_object($rnd) ? ($rnd->duration_spent ?? 0) : ($rnd['duration_spent'] ?? 0);
        $moves = is_object($rnd) ? ($rnd->total_moves ?? 0) : ($rnd['total_moves'] ?? 0);
        $mistakes = is_object($rnd) ? ($rnd->mistakes ?? 0) : ($rnd['mistakes'] ?? 0);
        $gameNumber = is_object($rnd) ? ($rnd->game_number ?? 1) : ($rnd['game_number'] ?? 1);

        $roundsList[] = [
            'game_number' => $gameNumber,
            'theme_name' => $themeName,
            'theme_icon' => $themeIcon,
            'is_won' => $isWon,
            'duration_spent' => $duration,
            'total_moves' => $moves,
            'mistakes' => $mistakes,
        ];
    }
    $roundsJson = json_encode($roundsList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $timeVal = $total_time ?? ($entry?->total_time ?? 0);
    $movesVal = $total_moves ?? ($entry?->total_moves ?? 0);
    $attemptVal = $attempt_number ?? ($entry?->attempt_number ?? 1);
    $rankVal = $ranking ?? ($entry?->ranking ?? null);
@endphp
<button type="button" 
        class="btn btn-sm btn-outline-primary rounded-pill px-2.5 btn-view-rounds d-inline-flex align-items-center gap-1 shadow-none"
        style="border-radius: 50px !important;"
        data-child="{{ $childName }}"
        data-avatar="{{ $avatar }}"
        data-parent-name="{{ $parentName }}"
        data-parent-phone="{{ $formattedPhone ?: ($rawPhone ?: '--') }}"
        data-rank="{{ $rankVal ?: '--' }}"
        data-time="{{ $timeVal }}s ({{ gmdate('i:s', (int)$timeVal) }})"
        data-moves="{{ $movesVal }}"
        data-attempt="{{ $attemptVal }}"
        data-rounds="{{ $roundsJson }}">
    <i class="ti ti-eye"></i>
    <span>{{ __('Chi tiết') }}</span>
</button>
