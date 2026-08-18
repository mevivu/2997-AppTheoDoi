<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.exercise.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.exercise.delete', $id)" />
</x-admin.datatable.action-group>
