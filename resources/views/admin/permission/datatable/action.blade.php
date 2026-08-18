<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.permission.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.permission.delete', $id)" />
</x-admin.datatable.action-group>