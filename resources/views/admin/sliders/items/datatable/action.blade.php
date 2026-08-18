<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.slider.item.edit', ['slider_id' => $slider_id, 'id' => $id])" />
    <x-admin.datatable.action-delete :route="route('admin.slider.item.delete', ['slider_id' => $slider_id, 'id' => $id])" />
</x-admin.datatable.action-group>