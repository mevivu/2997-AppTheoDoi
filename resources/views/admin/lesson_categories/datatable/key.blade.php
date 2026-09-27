@php
    $keyEnum = is_string($key) ? \App\Enums\Lesson\LessonCategoryKey::tryFrom($key) : $key;
@endphp
<div class="d-flex flex-column align-items-center">
    <span class="badge bg-blue-lt fw-bold px-2">{{ $keyEnum ? $keyEnum->label() : $key }}</span>
    <code class="text-muted fs-11 mt-1">{{ is_string($key) ? $key : ($key?->value ?? '') }}</code>
</div>
