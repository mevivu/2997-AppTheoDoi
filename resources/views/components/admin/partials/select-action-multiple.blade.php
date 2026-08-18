{{-- Floating Bulk Action Bar Component (Modern CMS Style) --}}
@props(['actionMultiple' => []])

<div class="select-action-multiple floating-bulk-actions" id="floatingBulkActions" style="display: none;">
    <div class="floating-bulk-wrapper">
        {{-- Selected Count Indicator --}}
        <div class="bulk-count-badge">
            <span class="badge-dot"></span>
            <span>{{ __('Đã chọn') }} <strong id="selectedRowsCount">0</strong> {{ __('bản ghi') }}</span>
        </div>

        <div class="bulk-divider"></div>

        {{-- Action Select / Form Controls --}}
        <div class="bulk-controls">
            <select name="action" id="bulkActionSelect" class="form-select form-select-sm bulk-action-select" required>
                <option value="">{{ __('-- Chọn hành động --') }}</option>
                @foreach($actionMultiple as $key => $value)
                    <option value="{{ $key }}">{{ __($value) }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary btn-sm btn-bulk-apply" id="btnApplyBulkAction">
                <i class="ti ti-check"></i>
                <span>{{ __('Áp dụng') }}</span>
            </button>
        </div>

        {{-- Deselect All Button --}}
        <button type="button" class="btn-bulk-close" id="btnDeselectAll" title="{{ __('Bỏ chọn tất cả') }}">
            <i class="ti ti-x"></i>
        </button>
    </div>
</div>
