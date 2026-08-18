<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.vaccinationType.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.vaccinationType.delete', $id)" />
</x-admin.datatable.action-group>
