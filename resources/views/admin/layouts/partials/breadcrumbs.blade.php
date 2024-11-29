@if (isset($breadcrumbs) && !empty($breadcrumbs->getBreadcrumbs()))
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb" id="breadcrumbs-one">
                            @foreach ($breadcrumbs = $breadcrumbs->getBreadcrumbs() as $item)
                                @if (!$loop->last)
                                    <li class="">
                                        @if ($item['url'])
                                            <a href="{{ $item['url'] }}"
                                                class="text-muted-custom">{{ $item['label'] }}</a>
                                        @else
                                            <span class="text-muted-custom">{{ $item['label'] }}</span>
                                        @endif
                                    </li>
                                @else
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <a href="#">{{ $item['label'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endif
