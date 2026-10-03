<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.fetal-growth-standard.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.fetal-growth-standard.delete', $id)" />
</x-admin.datatable.action-group>
