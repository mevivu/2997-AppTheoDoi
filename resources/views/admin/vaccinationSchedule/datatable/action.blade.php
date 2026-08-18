<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.vaccination.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.vaccination.delete', $id)" />
</x-admin.datatable.action-group>
