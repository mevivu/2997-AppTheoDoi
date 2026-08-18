<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.quality.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.quality.delete', $id)" />
</x-admin.datatable.action-group>
