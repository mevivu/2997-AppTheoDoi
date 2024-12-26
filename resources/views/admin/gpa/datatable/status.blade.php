<span @class([
    'badge',
    App\Enums\ClassGrade\ClassGradeStatus::from($status)->badge(),
])>
    {{ \App\Enums\ClassGrade\ClassGradeStatus::getDescription($status) }}</span>
