<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.clinic.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.clinic.delete', $id)" />
</x-admin.datatable.action-group>
