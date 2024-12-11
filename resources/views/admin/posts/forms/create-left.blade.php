<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin bài viết') }}</h2>
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-tag"></span>
                        {{ __('Tiêu đề') }}:</label>
                    <x-input name="title" :value="old('title')" :required="true" placeholder="{{ __('Tiêu đề') }}" />
                </div>
            </div>
    
            <!-- desc -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-message"></span>
                        {{ __('Mô tả') }}:</label>
                    <textarea name="content" class="ckeditor visually-hidden">
                        {{ old('content') }}
                    </textarea>
                </div>
            </div>
            <!-- excerpt -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-message"></span>
                        {{ __('Mô tả ngắn') }}:</label>
                    <textarea class="form-control" name="excerpt" rows="5">
                        {{ old('excerpt') }}
                    </textarea>
                </div>
            </div>
        </div>
    </div>
</div>
