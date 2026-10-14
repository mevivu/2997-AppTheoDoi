<div class="col-12 col-lg-4 col-xl-3">
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" :option="$response->status->value" />
                @endforeach
            </x-select>
        </div>
    </div>

    <!-- Quick Formula Card -->
    <div class="card border-0 custom-shadow rounded-3 mb-4 bg-purple-lt">
        <div class="card-body p-3">
            <h6 class="fw-bold text-purple mb-2"><i class="ti ti-info-circle me-1"></i>Công thức tính IQ:</h6>
            <div class="fs-12 text-dark font-monospace mb-2">
                IQ = 100 + 15 * (X - {{ $response->mean_score }}) / {{ $response->sd_score }}
            </div>
            <p class="fs-11 text-muted mb-0">
                X là điểm thô bài test của trẻ. Điểm IQ tính ra sẽ được làm tròn số nguyên và gán mức phân loại tương ứng.
            </p>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu thay đổi')"
        submit-icon="ti ti-device-floppy"
        :back-route="route('admin.iq_norm.index')"
        :back-title="__('Quay lại')"
    />
</div>
