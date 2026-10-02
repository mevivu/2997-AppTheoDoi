@php
    $typeVal = ($post_type instanceof \App\Enums\Post\PostType) ? $post_type->value : (int) ($post_type ?? 1);
@endphp
@if($typeVal == \App\Enums\Post\PostType::Knowledge->value)
    <span class="badge bg-purple-lt"><i class="ti ti-book me-1"></i>{{ __('Kiến thức chăm con') }}</span>
@else
    <span class="badge bg-blue-lt"><i class="ti ti-news me-1"></i>{{ __('Bài viết') }}</span>
@endif
