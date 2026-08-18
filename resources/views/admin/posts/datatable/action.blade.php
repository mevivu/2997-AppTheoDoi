<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.post.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.post.delete', $id)" />
</x-admin.datatable.action-group>