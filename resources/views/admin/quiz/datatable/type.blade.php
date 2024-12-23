<span @class([
    'badge',
    App\Enums\Question\QuestionType::from($type)->badge(),
])>{{ \App\Enums\Question\QuestionType::getDescription($type) }}</span>
