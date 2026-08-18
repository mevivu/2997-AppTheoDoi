    <div class="col-12 col-lg-4 col-xl-3">
    <!-- Card Trạng thái -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.02rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-3">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" :selected="old('status', \App\Enums\ActiveStatus::Active->value) == $key"/>
                @endforeach
            </x-select>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Tạo bài kiểm tra')"
        submit-icon="ti ti-device-floppy"
        :back-route="route($route)"
        :back-title="__('Quay lại')"
    />
</div>
