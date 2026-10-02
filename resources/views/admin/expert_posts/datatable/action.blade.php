<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.expert_post.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.expert_post.delete', $id)" />
</x-admin.datatable.action-group>
