<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Nội dung bài giới thiệu') }}</h2>
        </div>
        <div class="row card-body">
            <!-- title -->
            <div class="col-12 col-md-8">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-heading"></span>
                        {{ __('Tiêu đề') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input name="title" :value="old('title', $instance->title)" :required="true" placeholder="{{ __('Tiêu đề') }}" />
                </div>
            </div>

            <!-- section_type -->
            <div class="col-12 col-md-4">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-category"></span>
                        {{ __('Phân loại phần giới thiệu') }}: <span class="text-danger">*</span>
                    </label>
                    <x-select name="section_type" :required="true">
                        @foreach ($section_types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="old('section_type', $instance->section_type->value ?? $instance->section_type) == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- icon -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-icons"></span>
                        {{ __('Mã Icon / Tên biểu tượng (tùy chọn)') }}:
                    </label>
                    <x-input name="icon" :value="old('icon', $instance->icon)" placeholder="{{ __('VD: shield-check, heart-handshake, star, award...') }}" />
                </div>
            </div>

            <!-- excerpt -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-quote"></span>
                        {{ __('Mô tả tóm tắt / Lời dẫn ngắn') }}:
                    </label>
                    <textarea class="form-control" name="excerpt" rows="3">{{ old('excerpt', $instance->excerpt) }}</textarea>
                </div>
            </div>

            <!-- content -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-file-text"></span>
                        {{ __('Nội dung chi tiết (soạn thảo phong phú)') }}:
                    </label>
                    <textarea name="content" class="ckeditor visually-hidden">{{ old('content', $instance->content) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
