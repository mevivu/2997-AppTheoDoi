@php
    use App\Traits\RouteAdminSystem;
    $targetUrl = RouteAdminSystem::childDetailUrl($children);
@endphp
<x-link target="_blank" :href="$targetUrl" :title="$children->fullname" />
