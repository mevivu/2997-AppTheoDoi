<x-admin.datatable.action-group>
    <x-admin.datatable.action-edit :href="route('admin.user.edit', $id)" />
    <x-admin.datatable.action-delete :route="route('admin.user.delete', $id)" title="{{ __('Khóa / Ngưng hoạt động') }}" />
    <x-admin.datatable.action-force-delete :route="route('admin.user.forceDelete', $id)" />

</x-admin.datatable.action-group>
