<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Nội dung bài viết chuyên gia') }}</h2>
        </div>
        <div class="row card-body">
            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-heading"></span>
                        {{ __('Tiêu đề bài viết') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input name="title" :value="old('title')" :required="true" placeholder="{{ __('VD: Bổ sung Vitamin D3 & K2 đúng cách cho trẻ sơ sinh...') }}" />
                </div>
            </div>

            <!-- expert_quote -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label text-primary fw-bold">
                        <span class="ti ti-quote"></span>
                        {{ __('Lời khuyên / Thông điệp nổi bật từ Chuyên gia') }}:
                    </label>
                    <textarea class="form-control border-primary" name="expert_quote" rows="3" placeholder="{{ __('Câu trích dẫn ngắn gọn, đúc kết chuyên môn sẽ được hiển thị dạng Quote đóng khung nổi bật ở đầu bài trên App...') }}">{{ old('expert_quote') }}</textarea>
                    <small class="text-muted">{{ __('Hiển thị trang trọng đầu bài viết cùng ảnh và tên của Bác sĩ biên soạn.') }}</small>
                </div>
            </div>

            <!-- excerpt -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-file-description"></span>
                        {{ __('Tóm tắt bài viết') }}:
                    </label>
                    <textarea class="form-control" name="excerpt" rows="3" placeholder="{{ __('Mô tả ngắn gọn nội dung bài viết...') }}">{{ old('excerpt') }}</textarea>
                </div>
            </div>

            <!-- content -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-file-text"></span>
                        {{ __('Nội dung chi tiết bài viết (soạn thảo phong phú)') }}:
                    </label>
                    <textarea name="content" class="ckeditor visually-hidden">{{ old('content') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
