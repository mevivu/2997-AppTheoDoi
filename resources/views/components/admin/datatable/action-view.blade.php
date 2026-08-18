{{-- Action View/Detail Button --}}
@props(['href'])

<a href="{{ $href }}" {{ $attributes->class(['dt-action-btn dt-action-view']) }} title="@lang('detail')">
    <i class="ti ti-eye"></i>
</a>
