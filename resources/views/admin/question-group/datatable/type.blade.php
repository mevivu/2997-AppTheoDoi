<span @class(['badge', App\Enums\Group\GroupType::from($type)->badge()])>
    {{ \App\Enums\Group\GroupType::getDescription($type) }}</span>
