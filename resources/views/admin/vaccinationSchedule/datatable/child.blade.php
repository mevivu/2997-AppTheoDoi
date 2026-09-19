@php
    use App\Traits\RouteAdminSystem;
@endphp
@if ($child)
    <x-link target="_blank" :href="RouteAdminSystem::childDetailUrl($child)" :title="$child->fullname"/>
@else
    <span>N/A</span>
@endif
