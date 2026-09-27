<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.lesson_category.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.lesson_category.delete', $id)" />
</x-admin.datatable.action-group>
