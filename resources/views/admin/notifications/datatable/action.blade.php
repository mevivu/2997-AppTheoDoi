<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.notification.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.notification.delete', $id)" />
</x-admin.datatable.action-group>
