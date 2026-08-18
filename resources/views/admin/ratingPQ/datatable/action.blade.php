@php use App\Traits\RouteAdminSystem; @endphp
<x-admin.datatable.action-group>
    <x-admin.datatable.action-delete :route="route(RouteAdminSystem::RATING_PQ_DELETE, $id)" />
</x-admin.datatable.action-group>
