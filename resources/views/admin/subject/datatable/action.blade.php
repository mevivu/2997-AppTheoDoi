<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.subject.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.subject.delete', $id)" />
</x-admin.datatable.action-group>
