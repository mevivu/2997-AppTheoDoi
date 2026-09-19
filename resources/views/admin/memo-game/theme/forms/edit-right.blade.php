@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4">
    {{-- Card Cài đặt & Trạng thái --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h4 class="card-title mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-settings text-primary me-2 fs-4"></i>
                {{ __('Cài đặt & Trạng thái') }}
            </h4>
        </div>
        <div class="card-body p-4">
            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Thứ tự sắp xếp') }}:</label>
                <x-input type="number" id="theme_position_input" name="position" :value="$response->position" min="0" />
                <small class="text-muted fs-12">{{ __('Tự động đồng bộ theo vị trí trong danh sách kéo thả bên dưới') }}</small>
            </div>

            <div class="mb-0">
                <label class="form-label fw-bold">{{ __('Trạng thái') }}: <span class="text-danger">*</span></label>
                <x-select name="status" :required="true">
                    @foreach ($status as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="$response->status->value == $key" />
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>

    {{-- Card Kéo thả sắp xếp thứ tự các Chủ đề --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-sort-ascending text-primary me-2 fs-4"></i>
                {{ __('Thứ tự hiển thị các chủ đề') }}
            </h5>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-12 fw-bold">Kéo thả</span>
        </div>
        <div class="card-body p-3">
            <div class="alert alert-info border-0 bg-primary-subtle text-primary p-2.5 rounded-3 mb-3 fs-12 d-flex align-items-center gap-2">
                <i class="ti ti-info-circle fs-4 flex-shrink-0"></i>
                <div>
                    {{ __('Nắm kéo biểu tượng') }} <i class="ti ti-arrows-sort text-dark mx-0.5 fs-5 align-middle"></i> {{ __('để đổi thứ tự xuất hiện của các Chủ đề.') }}
                </div>
            </div>

            <ul class="list-group list-group-flush sortable-theme-list p-0" id="sortable-themes-list" style="max-height: 480px; overflow-y: auto;">
                @if(isset($allThemes) && count($allThemes) > 0)
                    @foreach($allThemes as $index => $item)
                        @php $isCurrent = ($item->id == $response->id); @endphp
                        <li class="list-group-item sortable-theme-item border rounded-3 p-2 mb-2 bg-white d-flex align-items-center justify-content-between shadow-2xs {{ $isCurrent ? 'border-primary bg-primary-subtle-light' : '' }}"
                            data-id="{{ $item->id }}"
                            draggable="true">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <span class="drag-handle text-secondary cursor-grab p-1" title="Kéo để đổi vị trí">
                                    <i class="ti ti-arrows-sort fs-4 text-primary"></i>
                                </span>
                                <span class="theme-badge-pos badge {{ $isCurrent ? 'bg-primary text-white' : 'bg-secondary-subtle text-dark' }} rounded-pill px-2 py-1 fs-12 font-monospace">
                                    #{{ $index + 1 }}
                                </span>
                                @php
                                    $itemImg = (!empty($item->icon) && $item->icon != \App\Traits\ImageSystem::DEFAULT_IMAGE)
                                        ? $item->icon
                                        : ((!empty($item->card_back) && $item->card_back != \App\Traits\ImageSystem::DEFAULT_IMAGE) ? $item->card_back : null);
                                @endphp
                                @if($itemImg)
                                    <img src="{{ asset($itemImg) }}" alt="{{ $item->name }}" class="rounded-2 border object-fit-contain bg-white flex-shrink-0" style="width: 32px; height: 32px;">
                                @else
                                    <div class="rounded-2 border bg-light d-flex align-items-center justify-content-center flex-shrink-0 text-primary" style="width: 32px; height: 32px;">
                                        @if($item->code == 'vehicles')
                                            <i class="ti ti-car fs-4"></i>
                                        @elseif($item->code == 'flowers')
                                            <i class="ti ti-flower fs-4"></i>
                                        @elseif($item->code == 'numbers')
                                            <i class="ti ti-numbers fs-4"></i>
                                        @elseif($item->code == 'flags')
                                            <i class="ti ti-flag fs-4"></i>
                                        @else
                                            <i class="ti ti-palette fs-4"></i>
                                        @endif
                                    </div>
                                @endif
                                <div class="d-flex flex-column overflow-hidden">
                                    <span class="theme-name text-truncate fs-13 fw-semibold {{ $isCurrent ? 'text-primary' : 'text-dark' }}" title="{{ $item->name }}">
                                        {{ $item->name }}
                                    </span>
                                    <span class="text-muted fs-11">
                                        <i class="ti ti-cards text-secondary me-0.5"></i>{{ $item->cards_count ?? 0 }} thẻ
                                    </span>
                                </div>
                            </div>
                            @if($isCurrent)
                                <span class="badge bg-primary text-white rounded-pill px-2 py-0.5 fs-11 fw-normal ms-1 flex-shrink-0">
                                    Đang sửa
                                </span>
                            @endif
                        </li>
                    @endforeach
                @else
                    <li class="list-group-item text-center text-muted py-3 border-0">
                        {{ __('Chưa có chủ đề nào.') }}
                    </li>
                @endif
            </ul>

            <div class="mt-3 pt-3 border-top">
                <button type="button" id="btn-save-theme-order" class="btn-save-order-custom w-100" disabled>
                    <i class="ti ti-device-floppy me-1.5 fs-5"></i>
                    <span class="btn-text">{{ __('Chưa có thay đổi thứ tự') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Thanh thao tác chung --}}
    <x-admin.form-actions
        :submit-title="__('Cập nhật chủ đề')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::MEMO_THEME_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
