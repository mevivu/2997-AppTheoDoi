<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.ratingPQ.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.ratingPQ.delete', $id)" />
</x-admin.datatable.action-group>
