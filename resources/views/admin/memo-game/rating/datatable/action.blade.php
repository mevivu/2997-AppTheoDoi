<x-admin.datatable.action-group>
    <x-admin.datatable.action-view :href="route('admin.memo-game.rating.show', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.memo-game.rating.delete', $id)" />
</x-admin.datatable.action-group>
