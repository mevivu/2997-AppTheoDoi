<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.post_category.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.post_category.delete', $id)" />
</x-admin.datatable.action-group>