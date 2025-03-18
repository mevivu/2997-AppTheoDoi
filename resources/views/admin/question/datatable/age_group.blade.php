<span @class(['badge', App\Enums\Question\AgeGroup::from($age_group)->badge()])>
    {{ \App\Enums\Question\AgeGroup::getDescription($age_group) }}</span>
