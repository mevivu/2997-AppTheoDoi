<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.journal_milestone.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.journal_milestone.delete', $id)" />
</x-admin.datatable.action-group>
