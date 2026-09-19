@php
    use App\Traits\RouteAdminSystem;
    $targetUrl = RouteAdminSystem::childDetailUrl($children ?? (object)['id' => $id, 'user_id' => $user_id ?? null], $user_id ?? null);
@endphp
<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="$targetUrl" />
    <x-admin.datatable.action-delete :route="route(RouteAdminSystem::CHILDREN_DELETE, $id)" />
</x-admin.datatable.action-group>
