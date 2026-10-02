@if(!empty($avatar))
    <img src="{{ asset($avatar) }}"
         class="rounded-circle mx-auto d-block"
         style="width: 48px; height: 48px; object-fit: cover;"
         alt="{{ $name }}">
@else
    <span class="avatar rounded-circle bg-blue-lt text-uppercase fw-bold">{{ mb_substr($name, 0, 1) }}</span>
@endif
