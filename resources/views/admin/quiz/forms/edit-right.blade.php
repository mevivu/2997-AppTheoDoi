@php use App\Enums\Question\QuestionType; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    @if($instance->type == QuestionType::AQ || $instance->type == QuestionType::EQ)
        <div class="card border-0 custom-shadow rounded-3 mb-4">
            <div class="card-header bg-white border-bottom px-4 py-3">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                    <i class="ti ti-category text-primary me-2 fs-4"></i>
                    {{ __('Nhóm độ tuổi') }} <span class="text-danger ms-1">*</span>
                </h5>
            </div>
            <div class="card-body p-4">
                <x-select name="age_group" :required="true">
                    <x-select-option value="" title="Chọn loại" :selected="$instance->age_group === null"/>
                    @foreach ($age_group as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="!is_null($instance->age_group) && $instance->age_group->value == $key"/>
                    @endforeach
                </x-select>
            </div>
        </div>
    @endif

    @if($instance->type == QuestionType::EQ)
        <div class="card border-0 custom-shadow rounded-3 mb-4">
            <div class="card-header bg-white border-bottom px-4 py-3">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                    <i class="ti ti-arrows-shuffle text-primary me-2 fs-4"></i>
                    {{ __('Hiển thị ngẫu nhiên') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <x-select name="random">
                    <x-select-option value="" title="Chọn ngẫu nhiên" :selected="is_null(old('random'))"/>
                    @foreach ($random as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="old('random') == $key"/>
                    @endforeach
                </x-select>
            </div>
        </div>
    @endif

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
                    <x-select-option :value="$key" :title="$value" :selected="$instance->status->value == $key"/>
                @endforeach
            </x-select>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu thay đổi')"
        submit-icon="ti ti-device-floppy"
        :back-route="route($route)"
        :back-title="__('Quay lại')"
    />
</div>
