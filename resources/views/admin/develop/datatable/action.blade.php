<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.develop.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.develop.delete', $id)" />
</x-admin.datatable.action-group>
