<span @class(['badge', App\Enums\Exercise\ExerciseTopic::from($topic)->badge()])>
    {{ \App\Enums\Exercise\ExerciseTopic::getDescription($topic) }}
</span>
