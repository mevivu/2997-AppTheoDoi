<span @class([
    'badge',
    App\Enums\Post\PostStatus::from($status)->badge(),
])>{{ \App\Enums\Post\PostStatus::getDescription($status) }}</span>
