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
                    <x-input type="text" name="name" :value="$response->name" :required="true" placeholder="Ví dụ: Ném bóng trúng đích, Nhận diện hình khối..." />
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Phân loại bài tập cũ') }}: <span class="text-danger">*</span></label>
                    <x-select name="exercise_type" :required="true">
                        @foreach ($types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$response->exercise_type->value == $key" />
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
                            <x-select-option :value="$cat->id" :title="$cat->name . ' [' . \App\Enums\Exercise\ExerciseTopic::getDescription($cat->topic->value) . ' - ' . ($cat->ageGroup?->name ?? 'Tất cả') . ']'" :selected="$response->exercise_category_id == $cat->id" />
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
                            <x-select-option :value="$key" :title="$title" :selected="($response->difficulty?->value ?? '') == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tần suất tập luyện') }}:</label>
                    <x-input type="text" name="frequency" :value="$response->frequency" placeholder="Ví dụ: 3 lần/tuần, Mỗi ngày 15 phút..." />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Mô tả ngắn gọn') }}:</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Tóm tắt ngắn về bài tập...">{{ $response->description }}</textarea>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Lợi ích phát triển') }}:</label>
                    <textarea name="benefit" class="form-control" rows="3" placeholder="Ví dụ: Rèn luyện phối hợp tay - mắt, tăng khả năng tập trung...">{{ $response->benefit }}</textarea>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Dụng cụ cần chuẩn bị') }}:</label>
                    <textarea name="tools" class="form-control" rows="2" placeholder="Ví dụ: 1 quả bóng nhỏ, rổ đựng đồ chơi...">{{ $response->tools }}</textarea>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nội dung hướng dẫn chi tiết các bước') }}:</label>
                    <textarea name="content" class="ckeditor visually-hidden">{{ $response->content }}</textarea>
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
            {{-- Danh sách media hiện có --}}
            @if ($response->media && $response->media->isNotEmpty())
                <h6 class="fw-bold mb-3 text-secondary">{{ __('Media đã tải lên:') }}</h6>
                <div class="row g-3 mb-4">
                    @foreach ($response->media as $media)
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="border rounded p-2 h-100 d-flex flex-column justify-content-between bg-light">
                                <div class="text-center mb-2">
                                    @if ($media->media_type == \App\Enums\Exercise\ExerciseMediaType::IMAGE)
                                        <img src="{{ $media->media_file_url }}" alt="media" class="img-fluid rounded" style="max-height: 120px; object-fit: cover;" />
                                    @else
                                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                                            <video src="{{ $media->media_file_url }}" controls style="max-height: 120px;"></video>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="badge bg-secondary">{{ $media->media_type == \App\Enums\Exercise\ExerciseMediaType::IMAGE ? 'Hình ảnh' : 'Video' }}</span>
                                    <div class="form-check text-danger">
                                        <input class="form-check-input" type="checkbox" name="delete_media_ids[]" value="{{ $media->id }}" id="del_media_{{ $media->id }}">
                                        <label class="form-check-label fs-12 fw-bold" for="del_media_{{ $media->id }}">
                                            {{ __('Xóa') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <hr />
            @endif

            {{-- Thêm tệp mới --}}
            <h6 class="fw-bold mb-2 text-secondary">{{ __('Tải lên thêm tệp:') }}</h6>
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
