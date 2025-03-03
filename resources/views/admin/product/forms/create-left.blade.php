<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin sản phẩm') }}</h2>
        </div>
        <div class="row card-body">
            <!-- Product Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-tag"></span>
                        {{ __('Tên sản phẩm') }}:</label>
                    <x-input name="name" :value="old('name')" :required="true" placeholder="{{ __('Tên sản phẩm') }}" />
                </div>
            </div>

            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-message"></span>
                        {{ __('description') }}:</label>
                    <textarea name="description" class="ckeditor visually-hidden">
                        {{ old('content') }}
                    </textarea>
                </div>
            </div>
            <!-- Link -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-tag"></span>
                        {{ __('Link') }}:</label>
                    <x-input name="link" :value="old('link')" :required="true" placeholder="{{ __('Link') }}" />
                </div>
            </div>

            <div class="col-md-6 col-12 mb-3">
                <label class="form-label fw-bold">@lang('brand')</label>
                <select class="form-select" id="brand_id" name="brand_id" required>
                    <option value="" disabled selected>Chọn Thương Hiệu</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 mt-3">
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="ti ti-photo me-2"></i>
                        {{ __('Thư viện ảnh') }}
                    </div>
                    <div class="card-body p-2">
                        <x-input-gallery-ckfinder required name="gallery[]" type="multiple" />
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
