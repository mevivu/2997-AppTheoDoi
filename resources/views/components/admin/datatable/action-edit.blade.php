{{-- Action Edit Button --}}
@props(['href'])

<a href="{{ $href }}" {{ $attributes->class(['dt-action-btn dt-action-edit']) }} title="@lang('edit')">
    <i class="ti ti-pencil"></i>
</a>
