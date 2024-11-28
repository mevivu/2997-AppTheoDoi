<span @class([
    'badge',
    App\Enums\Permission\PermissionType::from($type)->badge(),
])>
    {{ \App\Enums\Permission\PermissionType::getDescription($type) }}</span>
