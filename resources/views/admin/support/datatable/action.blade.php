<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.support.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.support.delete', $id)" />
</x-admin.datatable.action-group>
