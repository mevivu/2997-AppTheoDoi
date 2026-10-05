{{-- Cấp học: quyết định cách đánh giá hợp lệ và công thức xếp loại (TT27 / TT22) --}}
<div class="card border-0 custom-shadow rounded-3 mb-4">
    <div class="card-header bg-white border-bottom px-4 py-3">
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
            <i class="ti ti-school text-primary me-2 fs-4"></i>
            {{ __('Cấp học') }} <span class="text-danger ms-1">*</span>
        </h5>
    </div>
    <div class="card-body p-4">
        <select name="education_level" id="education_level" class="form-select" required>
            @foreach ($educationLevels as $key => $label)
                <option value="{{ $key }}" @selected($currentEducationLevel === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="form-hint mt-2">
            {{ __('Đổi cấp học sẽ đổi công thức xếp loại học bạ của lớp này.') }}
        </div>
    </div>
</div>
