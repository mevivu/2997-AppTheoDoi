@php
    $diffEnum = is_string($difficulty) ? \App\Enums\Lesson\LessonDifficulty::tryFrom($difficulty) : $difficulty;
@endphp
@if($diffEnum)
    <span class="badge {{ $diffEnum->badge() }} px-2 py-1">
        {{ $diffEnum->label() }}
    </span>
@else
    <span class="badge bg-secondary-lt">{{ $difficulty }}</span>
@endif
