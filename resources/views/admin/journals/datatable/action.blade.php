<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.journal.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.journal.delete', $id)" />
</x-admin.datatable.action-group>
