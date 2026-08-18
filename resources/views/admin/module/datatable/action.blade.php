<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.module.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.module.delete', $id)" />
</x-admin.datatable.action-group>