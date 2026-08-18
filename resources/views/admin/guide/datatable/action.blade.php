<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.guide.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.guide.delete', $id)" />
</x-admin.datatable.action-group>
