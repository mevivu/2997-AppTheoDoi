<span @class([
    'badge',
    App\Enums\Guide\GuideType::from($type->value)->badge(),
])>{{ \App\Enums\Guide\GuideType::getDescription($type->value) }}
</span>
