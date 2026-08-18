<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.pregnancy.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.pregnancy.delete', $id)" />
</x-admin.datatable.action-group>
