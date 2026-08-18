@php use App\Traits\RouteAdminSystem; @endphp
<x-link target="_blank" :href="route(RouteAdminSystem::CHILDREN_EDIT, $id)" :title="$code" />
<i class="ti ti-copy copy-btn" style="font-size: 18px; cursor: pointer;" data-value="{{ $code }}"></i>
<i class="ti ti-check check-icon" style="display:none;font-size: 18px"></i>
