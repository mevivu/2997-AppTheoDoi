{{-- Admin Page Header Banner Component (Modern CMS Style) --}}
@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'breadcrumbs' => null,
    'addRoute' => null,
    'addTitle' => null,
    'backRoute' => null,
    'backTitle' => null,
])

<div {{ $attributes->class(['page-header-custom', 'has-icon' => !empty($icon)]) }}>
    <div class="header-content d-flex justify-content-between align-items-center flex-wrap gap-2 gap-md-3">
        <div class="header-title-box d-flex align-items-center gap-2 gap-md-3">
            @if ($icon)
                <div class="ph-icon-box flex-shrink-0">
                    <i class="ti {{ str_starts_with($icon, 'ti-') ? $icon : 'ti-' . $icon }}"></i>
                </div>
            @endif

            <div class="header-text-group min-w-0 flex-grow-1">
                @if ($breadcrumbs && is_array($breadcrumbs))
                    <div class="ph-breadcrumbs">
                        <a href="{{ route('admin.dashboard') }}"><i class="ti ti-home"></i></a>
                        @foreach ($breadcrumbs as $item)
                            <span class="sep"><i class="ti ti-chevron-right"></i></span>
                            @if (!$loop->last && isset($item['url']) && $item['url'])
                                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                            @else
                                <span class="active">{{ is_array($item) ? $item['label'] : $item }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif

                <h2 class="mb-0 text-dark fw-bold">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="header-subtitle mb-0 mt-1 text-muted">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="header-actions-box d-flex align-items-center flex-wrap gap-2">
            {{-- Extra actions slot --}}
            @if (isset($actions))
                {{ $actions }}
            @endif

            {{-- Add Button --}}
            @if ($addRoute)
                <x-link :href="$addRoute" class="btn btn-primary btn-add-custom">
                    <i class="ti ti-plus"></i>
                    <span>{{ $addTitle ?? __('Thêm') }}</span>
                </x-link>
            @endif

            {{-- Back Button --}}
            @if ($backRoute)
                <x-link :href="$backRoute" class="btn btn-header-back">
                    <i class="ti ti-arrow-left"></i>
                    <span>{{ $backTitle ?? __('Quay lại') }}</span>
                </x-link>
            @endif
        </div>
    </div>
</div>
