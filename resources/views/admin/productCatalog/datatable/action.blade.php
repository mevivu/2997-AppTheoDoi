<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.category.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.category.delete', $id)" />
</x-admin.datatable.action-group>
