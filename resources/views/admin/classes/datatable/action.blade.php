<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.classes.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.classes.delete', $id)" />
</x-admin.datatable.action-group>
