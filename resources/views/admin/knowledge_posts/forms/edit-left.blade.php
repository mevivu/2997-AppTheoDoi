<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Nội dung Kiến thức chăm con') }}</h2>
        </div>
        <div class="row card-body">
            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-tag"></span>
                        {{ __('Tiêu đề') }}: <span class="text-danger">*</span></label>
                    <x-input name="title" :value="$post->title" :required="true" placeholder="{{ __('Tiêu đề') }}" />
                </div>
            </div>

            <!-- excerpt -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-notes"></span>
                        {{ __('Mô tả ngắn (Tóm tắt)') }}:</label>
                    <textarea class="form-control" name="excerpt" rows="4">{{ $post->excerpt }}</textarea>
                </div>
            </div>

            <!-- content -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-article"></span>
                        {{ __('Nội dung chi tiết') }}:</label>
                    <textarea name="content" class="ckeditor visually-hidden">
                        {{ $post->content }}
                    </textarea>
                </div>
            </div>
        </div>
    </div>
</div>
