<span @class(['badge', App\Enums\Video\VideoAccessType::from($access_type)->badge()])>
    {{ \App\Enums\Video\VideoAccessType::getDescription($access_type) }}
</span>
