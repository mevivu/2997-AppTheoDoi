<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.memo-game.config.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.memo-game.config.delete', $id)" />
</x-admin.datatable.action-group>
