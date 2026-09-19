@php
    use App\Traits\RouteAdminSystem;
    $targetUrl = RouteAdminSystem::childDetailUrl($child);
@endphp
<x-link target="_blank" :href="$targetUrl" :title="$child->fullname" />
