<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.brand.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.brand.delete', $id)" />
</x-admin.datatable.action-group>
