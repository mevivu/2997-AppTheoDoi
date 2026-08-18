<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.role.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.role.delete', $id)" />
</x-admin.datatable.action-group>
