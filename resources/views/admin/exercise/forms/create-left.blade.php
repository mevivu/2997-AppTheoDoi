<div class="col-12 col-md-9">
    {{-- Card thông tin cơ bản --}}
    <div class="card custom-shadow mb-4">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin bài tập') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tên bài tập') }}: <span class="text-danger">*</span></label>
                    <x-input type="text" name="name" :value="old('name')" :required="true" placeholder="Ví dụ: Ném bóng trúng đích, Nhận diện hình khối..." />
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Phân loại bài tập cũ') }}: <span class="text-danger">*</span></label>
                    <x-select name="exercise_type" :required="true">
                        @foreach ($types as $key => $value)
                            <x-select-option :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Danh mục giáo dục (Chủ đề / Nhóm tuổi)') }}:</label>
                    <x-select name="exercise_category_id">
                        <x-select-option value="" title="-- Chưa gán danh mục mới --" />
                        @foreach ($categories as $cat)
                            <x-select-option :value="$cat->id" :title="$cat->name . ' [' . \App\Enums\Exercise\ExerciseTopic::getDescription($cat->topic->value) . ' - ' . ($cat->ageGroup?->name ?? 'Tất cả') . ']'" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Mức độ khó') }}:</label>
                    <x-select name="difficulty">
                        <x-select-option value="" title="-- Chọn mức khó --" />
                        @foreach ($difficulties as $key => $title)
                            <x-select-option :value="$key" :title="$title" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tần suất tập luyện') }}:</label>
                    <x-input type="text" name="frequency" :value="old('frequency')" placeholder="Ví dụ: 3 lần/tuần, Mỗi ngày 15 phút..." />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Mô tả ngắn gọn') }}:</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Tóm tắt ngắn về bài tập...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Lợi ích phát triển') }}:</label>
                    <textarea name="benefit" class="form-control" rows="3" placeholder="Ví dụ: Rèn luyện phối hợp tay - mắt, tăng khả năng tập trung...">{{ old('benefit') }}</textarea>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Dụng cụ cần chuẩn bị') }}:</label>
                    <textarea name="tools" class="form-control" rows="2" placeholder="Ví dụ: 1 quả bóng nhỏ, rổ đựng đồ chơi...">{{ old('tools') }}</textarea>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nội dung hướng dẫn chi tiết các bước') }}:</label>
                    <textarea name="content" class="ckeditor visually-hidden">{{ old('content') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Media minh họa --}}
    <div class="card custom-shadow mb-4">
        <div class="card-header justify-content-between align-items-center">
            <h3 class="mb-0"><i class="ti ti-photo-video text-primary me-2"></i>{{ __('Media minh họa (Hình ảnh / Video)') }}</h3>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn_add_media_row">
                <i class="ti ti-plus me-1"></i>{{ __('Thêm file') }}
            </button>
        </div>
        <div class="card-body">
            <div id="media_upload_container">
                <div class="media-row row g-3 align-items-center mb-3 p-3 bg-light rounded border">
                    <div class="col-md-5 col-12">
                        <label class="form-label fs-12 fw-bold text-muted">{{ __('Chọn tệp hình ảnh / video') }}</label>
                        <input type="file" name="media_files[]" class="form-control form-control-sm" accept="image/*,video/*" />
                    </div>
                    <div class="col-md-4 col-8">
                        <label class="form-label fs-12 fw-bold text-muted">{{ __('Loại media') }}</label>
                        <select name="media_types[]" class="form-select form-select-sm">
                            <option value="image">{{ __('Hình ảnh') }}</option>
                            <option value="video">{{ __('Video') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-4 text-end pt-3">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-media-row" style="visibility: hidden;">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <small class="text-muted d-block">{{ __('Hỗ trợ: jpg, png, webp, mp4 (Tối đa 20MB/tệp). Có thể thêm nhiều tệp.') }}</small>
        </div>
    </div>
</div>

@push('custom-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('media_upload_container');
        const btnAdd = document.getElementById('btn_add_media_row');

        if (btnAdd && container) {
            btnAdd.addEventListener('click', function () {
                const firstRow = container.querySelector('.media-row');
                if (firstRow) {
                    const newRow = firstRow.cloneNode(true);
                    newRow.querySelector('input[type="file"]').value = '';
                    const removeBtn = newRow.querySelector('.btn-remove-media-row');
                    removeBtn.style.visibility = 'visible';
                    removeBtn.addEventListener('click', function () {
                        newRow.remove();
                    });
                    container.appendChild(newRow);
                }
            });
        }
    });
</script>
@endpush
