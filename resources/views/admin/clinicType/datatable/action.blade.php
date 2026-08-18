<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.clinicType.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.clinicType.delete', $id)" />
</x-admin.datatable.action-group>
