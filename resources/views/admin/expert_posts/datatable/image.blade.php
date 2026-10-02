@if(!empty($image))
    <img src="{{ asset($image) }}"
         class="rounded mx-auto d-block"
         style="width: 50px; height: 50px; object-fit: cover;"
         alt="{{ $title }}">
@else
    <span class="text-muted fs-12">—</span>
@endif
