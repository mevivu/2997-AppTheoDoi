<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.introduction.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.introduction.delete', $id)" />
</x-admin.datatable.action-group>
