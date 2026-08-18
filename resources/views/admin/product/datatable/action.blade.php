<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.product.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.product.delete', $id)" />
</x-admin.datatable.action-group>
