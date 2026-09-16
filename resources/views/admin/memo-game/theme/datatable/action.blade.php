<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.memo-game.theme.edit', $id)" />
    <a href="{{ route('admin.memo-game.card.index', ['theme_id' => $id]) }}" class="dt-action-btn dt-action-view" title="{{ __('Xem danh sách thẻ bài') }}">
        <i class="ti ti-cards"></i>
    </a>
    <x-admin.datatable.action-delete :route="route('admin.memo-game.theme.delete', $id)" />
</x-admin.datatable.action-group>
