@if(!empty($audio))
    <audio controls style="height: 28px; width: 140px; vertical-align: middle;" preload="none">
        <source src="{{ asset($audio) }}">
    </audio>
@else
    <span class="text-muted fs-12">{{ __('Không có') }}</span>
@endif
