@php
    $catName = is_array($category ?? null) ? ($category['name'] ?? '—') : ($category?->name ?? '—');
    $catKey = is_array($category ?? null) ? ($category['key'] ?? null) : ($category?->key ?? null);
    $catKeyVal = $catKey instanceof \BackedEnum ? $catKey->value : (string) ($catKey ?? '');
@endphp
<div class="d-flex flex-column align-items-center">
    <span class="fw-semibold text-dark">{{ $catName }}</span>
    @if(!empty($catKeyVal))
        <span class="badge bg-light text-muted fs-11 mt-1">{{ $catKeyVal }}</span>
    @endif
</div>
