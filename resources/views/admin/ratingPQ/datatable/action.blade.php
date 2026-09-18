@php use App\Traits\RouteAdminSystem; @endphp
<x-admin.datatable.action-group>
    @if(isset($child_id) && $child_id)
        <a href="{{ route(RouteAdminSystem::CHILDREN_EDIT, $child_id) }}#tab-child-height-pred" 
           class="dt-action-btn"
           style="background: #e0f2fe; color: #0284c7; border-color: #bae6fd;"
           title="{{ __('Xem Phác đồ tăng trưởng chiều cao') }}">
            <i class="ti ti-chart-line"></i>
        </a>
    @endif
    <x-admin.datatable.action-delete :route="route(RouteAdminSystem::RATING_PQ_DELETE, $id)" />
</x-admin.datatable.action-group>
