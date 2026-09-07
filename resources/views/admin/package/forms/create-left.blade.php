<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên') <span class="text-danger">*</span></label>
                    <x-input name="name" :value="old('name')" :required="true" :placeholder="__('name')" />
                </div>
            </div>

            <!-- Code -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Mã gói') <span class="text-danger">*</span></label>
                    <x-input type="text" name="code" :value="old('code')" :required="true" :placeholder="__('Mã gói')" />
                    <div class="alert alert-warning mt-2 d-flex align-items-start gap-2" role="alert">
                        <i class="ti ti-alert-triangle fs-5 mt-1"></i>
                        <div>
                            <strong>@lang('Lưu ý quan trọng:')</strong>
                            <p class="mb-0">@lang('Mã gói này phải khớp chính xác với Product ID trong Google Play Console và App Store Connect. Nếu không khớp, thanh toán sẽ thất bại.')</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- price -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('price') <span class="text-danger">*</span></label>
                    <x-input-price name="price" :value="old('price')" :required="true" :placeholder="__('price')" />
                </div>
            </div>

            <!-- Discount Section -->
            <div class="col-12">
                <div class="card border rounded-3 mb-3 p-3" style="background-color: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b !important;">
                    <h6 class="fw-bold text-primary mb-3 d-flex align-items-center">
                        <i class="ti ti-discount me-2 fs-3"></i> @lang('Chính sách Giảm giá & Khuyến mãi')
                    </h6>
                    <div class="row g-3">
                        <!-- discount_type -->
                        <div class="col-md-6 col-12">
                            <label class="control-label fw-semibold text-dark">@lang('Loại giảm giá')</label>
                            <select name="discount_type" id="package_discount_type" class="form-select text-dark fw-medium" style="color: #0f172a !important; background-color: #ffffff !important;">
                                @foreach ($discount_types as $key => $title)
                                    <option value="{{ $key }}" {{ old('discount_type', 'none') === $key ? 'selected' : '' }} style="color: #0f172a !important; background-color: #ffffff !important;">
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- discount_value -->
                        <div class="col-md-6 col-12" id="discount_value_wrapper">
                            <label class="control-label fw-semibold text-dark" id="discount_value_label">
                                @lang('Mức giảm giá')
                            </label>
                            <div class="input-group" id="discount_percent_group" style="display: none;">
                                <input type="number" 
                                       name="discount_value_percent" 
                                       id="package_discount_percent_input" 
                                       class="form-control text-dark" 
                                       min="0" 
                                       max="100" 
                                       step="1"
                                       value="{{ old('discount_type') === 'percent' ? old('discount_value', 0) : '' }}" 
                                       placeholder="Ví dụ: 20">
                                <span class="input-group-text fw-bold text-primary">%</span>
                            </div>
                            <div id="discount_fixed_group" style="display: none;">
                                <input type="text"
                                       id="package_discount_fixed_display"
                                       class="form-control text-dark"
                                       value="{{ old('discount_type') === 'fixed' ? old('discount_value', 0) : '' }}"
                                       placeholder="Ví dụ: 50.000">
                                <span class="text-muted small d-block mt-1">Đơn vị: VNĐ</span>
                            </div>
                            <input type="hidden" name="discount_value" id="package_discount_value_hidden" value="{{ old('discount_value', 0) }}">
                        </div>

                        <!-- discount_code -->
                        <div class="col-12">
                            <label class="control-label fw-semibold text-dark">
                                @lang('Mã khuyến mãi / Voucher') <span class="text-muted fw-normal">(@lang('Tùy chọn'))</span>
                            </label>
                            <input type="text" 
                                   name="discount_code" 
                                   id="package_discount_code" 
                                   class="form-control text-uppercase text-dark" 
                                   value="{{ old('discount_code') }}" 
                                   placeholder="Ví dụ: SALE2026, VIPNEW" 
                                   maxlength="50">
                            <small class="text-muted">@lang('Mã áp dụng cho gói này (nếu có chương trình ưu đãi đặc biệt).')</small>
                        </div>

                        <!-- Live Price Preview Card -->
                        <div class="col-12 mt-3" id="price_preview_card">
                            <div class="p-3 bg-white rounded border d-flex flex-wrap justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small">@lang('Giá niêm yết (Gốc):')</span>
                                    <span class="fw-bold text-dark fs-5" id="preview_original_price">0 đ</span>
                                </div>
                                <div class="text-center" id="preview_discount_badge_wrap" style="display: none;">
                                    <span class="text-muted d-block small">@lang('Mức giảm:')</span>
                                    <span class="badge bg-danger-lt fs-6" id="preview_discount_text">-0%</span>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted d-block small">@lang('Giá thanh toán thực tế:')</span>
                                    <span class="fw-bold text-success fs-3" id="preview_final_price">0 đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('type') <span class="text-danger">*</span></label>
                    <x-select name="type" :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- days -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Số ngày') <span class="text-danger">*</span></label>
                    <x-input name="days"
                             type="number"
                             :value="old('days')"
                             :required="true"
                             :placeholder="__('Số ngày')" />
                </div>
            </div>

            <!-- max_devices -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Số thiết bị tối đa') <span class="text-danger">*</span></label>
                    <x-input name="max_devices"
                             id="max_devices_input"
                             type="number"
                             min="1"
                             :value="old('max_devices', 1)"
                             :required="true"
                             :placeholder="__('Số thiết bị tối đa cho phép đăng nhập')" />
                    <div class="mt-2 d-flex align-items-center gap-1 flex-wrap">
                        <span class="text-muted small me-1"><i class="ti ti-hand-click"></i> Chọn nhanh:</span>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-preset-devices" data-value="1">1 máy</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-preset-devices" data-value="2">2 máy</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-preset-devices" data-value="3">3 máy</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-preset-devices" data-value="5">5 máy</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-preset-devices" data-value="10">10 máy</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-preset-devices" data-value="999">Không giới hạn</button>
                    </div>
                    <small class="text-muted d-block mt-1">
                        <i class="ti ti-info-circle"></i> @lang('Chính sách hệ thống: Gói Free tối đa 01 thiết bị, VIP 1 năm tối đa 05 thiết bị.')
                    </small>
                </div>
            </div>

            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="control-label">@lang('description')</label>
                        <p class="text-primary" style="cursor: pointer;" id="add-description">
                            <i class="ti ti-plus"></i> Thêm mô tả
                        </p>
                    </div>
                    <div class="d-flex flex-column gap-2" id="description-container">
                        <textarea name="description[]" class="form-control" rows="2" placeholder="{{ __('description') }}">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
