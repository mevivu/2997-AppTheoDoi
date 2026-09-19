@php
    use App\Traits\RouteAdminSystem;
    $targetUrl = RouteAdminSystem::childDetailUrl($children ?? (object)['id' => $id, 'user_id' => $user_id ?? null], $user_id ?? null);
@endphp
<x-link target="_blank" :href="$targetUrl" :title="$code" />
<i class="ti ti-copy copy-btn" style="font-size: 18px; cursor: pointer;" data-value="{{ $code }}"></i>
<i class="ti ti-check check-icon" style="display:none;font-size: 18px"></i>
