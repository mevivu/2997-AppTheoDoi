<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.quiz.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.quiz.delete', $id)" />
</x-admin.datatable.action-group>
