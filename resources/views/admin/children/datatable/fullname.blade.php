@php
    use App\Traits\RouteAdminSystem;
    $targetUrl = RouteAdminSystem::childDetailUrl($children ?? (object)['id' => $id, 'user_id' => $user_id ?? null], $user_id ?? null);
@endphp
<x-link target="_blank" :href="$targetUrl" :title="$fullname" />
