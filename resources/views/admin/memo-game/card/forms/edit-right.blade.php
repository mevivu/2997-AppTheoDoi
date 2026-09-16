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

    {{-- Card Kéo thả sắp xếp thứ tự các Thẻ bài trong Chủ đề --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-sort-ascending text-primary me-2 fs-4"></i>
                {{ __('Thứ tự hiển thị các thẻ bài') }}
            </h5>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-12 fw-bold">Kéo thả</span>
        </div>
        <div class="card-body p-3">
            <div class="alert alert-info border-0 bg-primary-subtle text-primary p-2.5 rounded-3 mb-3 fs-12 d-flex align-items-center gap-2">
                <i class="ti ti-info-circle fs-4 flex-shrink-0"></i>
                <div>
                    {{ __('Nắm kéo biểu tượng') }} <i class="ti ti-arrows-sort text-dark mx-0.5 fs-5 align-middle"></i> {{ __('để đổi thứ tự xuất hiện của các Thẻ bài trong chủ đề.') }}
                </div>
            </div>

            <ul class="list-group list-group-flush sortable-card-list p-0" id="sortable-cards-list" style="max-height: 480px; overflow-y: auto;">
                @if(isset($themeCards) && count($themeCards) > 0)
                    @foreach($themeCards as $index => $item)
                        @php $isCurrent = ($item->id == $response->id); @endphp
                        <li class="list-group-item sortable-card-item border rounded-3 p-2 mb-2 bg-white d-flex align-items-center justify-content-between shadow-2xs {{ $isCurrent ? 'border-primary bg-primary-subtle-light' : '' }}"
                            data-id="{{ $item->id }}"
                            draggable="true">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <span class="drag-handle text-secondary cursor-grab p-1" title="Kéo để đổi vị trí">
                                    <i class="ti ti-arrows-sort fs-4 text-primary"></i>
                                </span>
                                <span class="card-badge-pos badge {{ $isCurrent ? 'bg-primary text-white' : 'bg-secondary-subtle text-dark' }} rounded-pill px-2 py-1 fs-12 font-monospace">
                                    #{{ $index + 1 }}
                                </span>
                                <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="rounded-2 border object-fit-contain bg-white flex-shrink-0" style="width: 28px; height: 28px;">
                                <span class="card-name text-truncate fs-13 fw-semibold {{ $isCurrent ? 'text-primary' : 'text-dark' }}" title="{{ $item->name }}">
                                    {{ $item->name }}
                                </span>
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
                        {{ __('Chưa có thẻ bài nào trong chủ đề này.') }}
                    </li>
                @endif
            </ul>

            <div class="mt-3 pt-3 border-top">
                <button type="button" id="btn-save-card-order" class="btn-save-order-custom w-100" disabled>
                    <i class="ti ti-device-floppy me-1.5 fs-5"></i>
                    <span class="btn-text">{{ __('Chưa có thay đổi thứ tự') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Thanh thao tác chung --}}
    <x-admin.form-actions
        :submit-title="__('Cập nhật thẻ')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::MEMO_CARD_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
