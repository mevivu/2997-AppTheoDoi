<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.slider.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.slider.delete', $id)" />
</x-admin.datatable.action-group>