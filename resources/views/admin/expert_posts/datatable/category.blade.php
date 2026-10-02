@php
    $catName = is_array($category ?? null) ? ($category['name'] ?? null) : ($category->name ?? null);
@endphp

@if(!empty($catName))
    <span class="badge bg-blue-lt">{{ $catName }}</span>
@else
    <span class="text-muted fs-12">—</span>
@endif
