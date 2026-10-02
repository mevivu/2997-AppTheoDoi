@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    <!-- Chọn Bác sĩ / Chuyên gia cố vấn -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-user-check text-primary me-2 fs-4"></i>
                {{ __('Chuyên gia / Tác giả') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name="expert_id">
                <x-select-option value="" :title="__('-- Chọn Bác sĩ / Chuyên gia --')" />
                @foreach ($experts as $id => $name)
                    <x-select-option :value="$id" :title="$name" />
                @endforeach
            </x-select>
        </div>
    </div>

    <!-- Chuyên mục & Độ tuổi -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-category text-primary me-2 fs-4"></i>
                {{ __('Phân loại chuyên môn') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="mb-3">
                <label class="control-label mb-1">{{ __('Chuyên mục') }}:</label>
                <x-select name="category_id">
                    <x-select-option value="" :title="__('-- Chọn Chuyên mục --')" />
                    @foreach ($categories as $id => $name)
                        <x-select-option :value="$id" :title="$name" />
                    @endforeach
                </x-select>
            </div>

            <div class="mb-0">
                <label class="control-label mb-1">{{ __('Nhóm độ tuổi của trẻ') }}:</label>
                <x-select name="age_group_id">
                    <x-select-option value="" :title="__('-- Mọi lứa tuổi --')" />
                    @foreach ($age_groups as $id => $name)
                        <x-select-option :value="$id" :title="$name" />
                    @endforeach
                </x-select>
                <small class="text-muted fs-11">{{ __('Chọn độ tuổi để App gợi ý đúng bài viết cho con của phụ huynh.') }}</small>
            </div>
        </div>
    </div>

    <!-- Thông số bài viết -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-clock text-primary me-2 fs-4"></i>
                {{ __('Thông số & Đề xuất') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="mb-3">
                <label class="control-label mb-1">{{ __('Thời gian đọc') }}:</label>
                <x-input name="reading_time" :value="old('reading_time', '3 phút đọc')" placeholder="VD: 3 phút đọc" />
            </div>

            <div class="mb-3">
                <label class="control-label mb-1">{{ __('Thứ tự sắp xếp') }}:</label>
                <x-input type="number" name="sort_order" :value="old('sort_order', 0)" placeholder="0" />
            </div>

            <div class="mb-0">
                <x-input-switch name="is_featured" value="1"
                    :label="__('Ghim bài viết nổi bật lên đầu App')" />
            </div>
        </div>
    </div>

    <!-- Ảnh đại diện -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-photo text-primary me-2 fs-4"></i>
                {{ __('Ảnh đại diện bài viết') }}
            </h5>
        </div>
        <div class="card-body p-4 text-center">
            <div class="settings-logo-upload-wrapper text-center my-2 p-3">
                <x-input-image-ckfinder name="image" showImage="image" />
            </div>
            <p class="text-muted fs-12 mb-0">{{ __('Ảnh bìa tỷ lệ 16:9, tối ưu chất lượng hiển thị trên mobile.') }}</p>
        </div>
    </div>

    <!-- Trạng thái & Ngày đăng -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Xuất bản') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="mb-3">
                <label class="control-label mb-1">{{ __('Trạng thái') }}:</label>
                <x-select name="status" :required="true">
                    @foreach ($status as $key => $value)
                        <x-select-option :value="$key" :title="$value" />
                    @endforeach
                </x-select>
            </div>

            <div class="mb-0">
                <label class="control-label mb-1">{{ __('Ngày giờ đăng') }}:</label>
                <x-input type="datetime-local" name="posted_at" :value="old('posted_at', now()->format('Y-m-d\TH:i'))" />
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu bài viết')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::EXPERT_POST_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
