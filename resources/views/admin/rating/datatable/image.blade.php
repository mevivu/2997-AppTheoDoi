@if(!empty($image))
    <img src="{{ asset($image) }}"
         class="rounded mx-auto d-block"
         style="width: 100px"
         alt="{{ __('Ảnh bài viết') }}">
@else
    <span>N/A</span>
@endif
