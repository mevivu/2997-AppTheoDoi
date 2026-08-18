<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.package.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.package.delete', $id)" />
</x-admin.datatable.action-group>
