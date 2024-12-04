<span @class([
    'badge',
    App\Enums\Package\PackageType::from($type)->badge(),
])>{{ \App\Enums\Package\PackageType::getDescription($type) }}</span>
