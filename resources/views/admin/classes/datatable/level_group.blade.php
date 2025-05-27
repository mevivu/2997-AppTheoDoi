<span @class([
    'badge',
    App\Enums\Class\LevelGroup::from($level_group)->badge(),
])>{{ \App\Enums\Class\LevelGroup::getDescription($level_group) }}</span>
