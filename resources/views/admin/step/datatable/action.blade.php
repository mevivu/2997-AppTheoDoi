<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.step.edit', ['developGuideId' => request()->route('developGuideId'), 'id' => $id])" />
    <x-admin.datatable.action-delete :route="route('admin.step.delete', $id)" />
</x-admin.datatable.action-group>
