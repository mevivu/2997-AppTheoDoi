@foreach ($product_catalogs as $catalog)
    <span>{{ $catalog->name }}</span>
    @if (!$loop->last)
        ,
    @endif
@endforeach
