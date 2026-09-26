<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.video.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.video.delete', $id)" />
</x-admin.datatable.action-group>
