<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.video_category.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.video_category.delete', $id)" />
</x-admin.datatable.action-group>
