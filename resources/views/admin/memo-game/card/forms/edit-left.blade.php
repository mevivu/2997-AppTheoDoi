<div class="col-12 col-lg-8">
    <div class="card custom-shadow">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('Thông tin Thẻ bài') }}</h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Chủ đề') }}: <span class="text-danger">*</span></label>
                        <x-select name="memo_theme_id" :required="true">
                            @foreach ($themes as $id => $name)
                                <x-select-option :value="$id" :title="$name" :selected="$response->memo_theme_id == $id" />
                            @endforeach
                        </x-select>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Tên thẻ / Đối tượng') }}: <span class="text-danger">*</span></label>
                        <x-input type="text" name="name" :value="$response->name" :required="true" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Hình ảnh mặt trước') }}:</label>
                        <x-input-image name="image" :value="$response->image" sub="{{ __('Tải ảnh thẻ bài (khuyến nghị vuông 1:1, tối đa 5MB). Nếu để trống sẽ dùng ảnh mặc định hệ thống.') }}" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Âm thanh / Phát âm (Tùy chọn)') }}:</label>
                        <x-input-file name="audio" :value="$response->audio" accept="audio/*,audio/mp3,audio/wav,audio/ogg,audio/m4a" :is-audio="true" sub="{{ __('Tải file audio phát khi bé ghép đúng cặp thẻ (hỗ trợ nghe thử trực tiếp)') }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
