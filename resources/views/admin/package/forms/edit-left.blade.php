<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên') <span class="text-danger">*</span></label>
                    <x-input type="text" name="name" :value="$instance->name" :required="true" :placeholder="__('name')" />
                </div>
            </div>

            <!-- Code -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Mã gói') <span class="text-danger">*</span></label>
                    <x-input type="text" name="code" :value="$instance->code" :required="true" :placeholder="__('Mã gói')" />
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
                    <x-input-price name="price" :value="$instance->price" :required="true" :placeholder="__('price')" />
                </div>
            </div>

            <!-- Discount Section -->
            @php
                $currentDiscountType = old('discount_type', $instance->discount_type?->value ?? 'none');
                $currentDiscountVal = old('discount_value', $instance->discount_value ?? 0);
                $currentDiscountCode = old('discount_code', $instance->discount_code ?? '');
            @endphp
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
                                    <option value="{{ $key }}" {{ $currentDiscountType === $key ? 'selected' : '' }} style="color: #0f172a !important; background-color: #ffffff !important;">
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
                                       value="{{ $currentDiscountType === 'percent' ? $currentDiscountVal : '' }}" 
                                       placeholder="Ví dụ: 20">
                                <span class="input-group-text fw-bold text-primary">%</span>
                            </div>
                            <div id="discount_fixed_group" style="display: none;">
                                <input type="text"
                                       id="package_discount_fixed_display"
                                       class="form-control text-dark"
                                       value="{{ $currentDiscountType === 'fixed' ? $currentDiscountVal : '' }}"
                                       placeholder="Ví dụ: 50.000">
                                <span class="text-muted small d-block mt-1">Đơn vị: VNĐ</span>
                            </div>
                            <input type="hidden" name="discount_value" id="package_discount_value_hidden" value="{{ $currentDiscountVal }}">
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
                                   value="{{ $currentDiscountCode }}" 
                                   placeholder="Ví dụ: SALE2026, VIPNEW" 
                                   maxlength="50">
                            <small class="text-muted">@lang('Mã áp dụng cho gói này (nếu có chương trình ưu đãi đặc biệt).')</small>
                        </div>

                        <!-- Live Price Preview Card -->
                        <div class="col-12 mt-3" id="price_preview_card">
                            <div class="p-3 bg-white rounded border d-flex flex-wrap justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small">@lang('Giá niêm yết (Gốc):')</span>
                                    <span class="fw-bold text-dark fs-5" id="preview_original_price">{{ format_price($instance->price) }}</span>
                                </div>
                                <div class="text-center" id="preview_discount_badge_wrap" style="{{ $instance->has_discount ? '' : 'display: none;' }}">
                                    <span class="text-muted d-block small">@lang('Mức giảm:')</span>
                                    <span class="badge bg-danger-lt fs-6" id="preview_discount_text">{{ $instance->discount_display }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted d-block small">@lang('Giá thanh toán thực tế:')</span>
                                    <span class="fw-bold text-success fs-3" id="preview_final_price">{{ format_price($instance->final_price) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- type & is_auto_renew -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Loại gói') <span class="text-danger">*</span></label>
                    <x-select name="type" :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$instance->type->value == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Hình thức gia hạn') <span class="text-danger">*</span></label>
                    <select name="is_auto_renew" class="form-select" required>
                        <option value="0" {{ old('is_auto_renew', (int)($instance->is_auto_renew ? 1 : 0)) == 0 ? 'selected' : '' }}>
                            @lang('Một lần (Không tự động gia hạn)')
                        </option>
                        <option value="1" {{ old('is_auto_renew', (int)($instance->is_auto_renew ? 1 : 0)) == 1 ? 'selected' : '' }}>
                            @lang('Tự động gia hạn (Subscription)')
                        </option>
                    </select>
                    <small class="text-muted d-block mt-1">
                        <i class="ti ti-info-circle"></i> @lang('Gói tự động trừ phí định kỳ qua Google Play / App Store hoặc chỉ mua dùng 1 lần.')
                    </small>
                </div>
            </div>

            <!-- is_sale -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Phân loại gói') <span class="text-danger">*</span></label>
                    <select name="is_sale" class="form-select" required>
                        <option value="0" {{ old('is_sale', (int)($instance->is_sale ? 1 : 0)) == 0 ? 'selected' : '' }}>
                            @lang('Gói thường (Tiêu chuẩn)')
                        </option>
                        <option value="1" {{ old('is_sale', (int)($instance->is_sale ? 1 : 0)) == 1 ? 'selected' : '' }}>
                            @lang('Gói sale (Ưu đãi / Khuyến mãi)')
                        </option>
                    </select>
                    <small class="text-muted d-block mt-1">
                        <i class="ti ti-info-circle"></i> @lang('Phân biệt gói tiêu chuẩn thường hoặc gói ưu đãi sale cho chiến dịch.')
                    </small>
                </div>
            </div>

            @php
                $isSaleSelected = (int) old('is_sale', (int)($instance->is_sale ? 1 : 0)) === 1;
                $currentSaleTitle = old('sale_title', $instance->sale_title ?? '');
                $currentSaleStart = old('sale_start_at', $instance->sale_start_at ? $instance->sale_start_at->format('Y-m-d\TH:i') : '');
                $currentSaleEnd = old('sale_end_at', $instance->sale_end_at ? $instance->sale_end_at->format('Y-m-d\TH:i') : '');
            @endphp
            <!-- Sale Date Range & Title (Hiển thị khi chọn Gói sale) -->
            <div class="col-12" id="sale_dates_wrapper" style="{{ $isSaleSelected ? '' : 'display: none;' }}">
                <div class="card border rounded-3 mb-3 p-3" style="background-color: #f0fdf4; border-color: #bbf7d0 !important;">
                    <h6 class="fw-bold text-success mb-2 d-flex align-items-center">
                        <i class="ti ti-calendar-time me-2 fs-3"></i> @lang('Cấu hình Khuyến mãi & Flash Sale')
                    </h6>
                    <p class="text-muted small mb-3">@lang('Chỉ định tiêu đề nổi bật và thời gian áp dụng chương trình sale cho gói dịch vụ này.')</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="control-label fw-semibold text-dark">
                                @lang('Tiêu đề Flash Sale')
                            </label>
                            <input type="text" 
                                   name="sale_title" 
                                   id="sale_title" 
                                   class="form-control text-dark @error('sale_title') is-invalid @enderror" 
                                   value="{{ $currentSaleTitle }}" 
                                   maxlength="255"
                                   placeholder="Ví dụ: ⚡ FLASH SALE ⚡ hoặc KHUYẾN MÃI ĐẶC BIỆT">
                            @error('sale_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">@lang('Tiêu đề hiển thị trên banner hoặc màn hình Flash Sale của ứng dụng (tối đa 255 ký tự).')</small>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="control-label fw-semibold text-dark">
                                @lang('Sale từ ngày') <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" 
                                   name="sale_start_at" 
                                   id="sale_start_at" 
                                   class="form-control @error('sale_start_at') is-invalid @enderror" 
                                   value="{{ $currentSaleStart }}">
                            @error('sale_start_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="control-label fw-semibold text-dark">
                                @lang('Sale đến ngày') <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" 
                                   name="sale_end_at" 
                                   id="sale_end_at" 
                                   class="form-control @error('sale_end_at') is-invalid @enderror" 
                                   value="{{ $currentSaleEnd }}">
                            @error('sale_end_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- days -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Số ngày') <span class="text-danger">*</span></label>
                    <x-input name="days"
                             type="number"
                             :value="$instance->days"
                             :required="true"
                             :placeholder="__('Số ngày')" />
                </div>
            </div>

            <!-- max_devices -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Số thiết bị tối đa') <span class="text-danger">*</span></label>
                    <x-input name="max_devices"
                             id="max_devices_input"
                             type="number"
                             min="1"
                             :value="old('max_devices', $instance->max_devices ?? 1)"
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
                @php
                    $description = json_decode($instance->description);
                @endphp
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="control-label">@lang('description')</label>
                        <p class="text-primary" style="cursor: pointer;" id="add-description">
                            <i class="ti ti-plus"></i> Thêm mô tả
                        </p>
                    </div>
                    <div class="d-flex flex-column gap-2" id="description-container">
                        @if ($description)
                            @foreach ($description as $item)
                                <div class="d-flex align-items-strech gap-1">
                                    <textarea name="description[]" class="form-control" rows="2" placeholder="{{ __('description') }}">{{ $item }}</textarea>
                                    @if (!$loop->first)
                                        <button type="button" class="btn btn-danger remove-description">
                                            <i class="ti ti-x fs-2"></i>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
