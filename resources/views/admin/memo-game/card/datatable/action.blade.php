<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.memo-game.card.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.memo-game.card.delete', $id)" />
</x-admin.datatable.action-group>
