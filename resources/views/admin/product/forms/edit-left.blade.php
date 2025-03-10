<div class="col-12 col-md-9">
    <div class="card custom-shadow">
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
                    <x-input name="name" :value="$product->name" :required="true" placeholder="{{ __('Tên sản phẩm') }}" />
                </div>
            </div>

            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-message"></span>
                        {{ __('Mô tả') }}:</label>
                    <textarea name="description" class="ckeditor visually-hidden">{{ $product->description }}</textarea>
                </div>
            </div>
            <!-- Link -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-tag"></span>
                        {{ __('Link') }}:</label>
                    <x-input name="link" :value="$product->link" :required="true" placeholder="{{ __('Link') }}" />
                </div>
            </div>

            <!-- Brand -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-package"></span>
                        {{ __('Thương hiệu') }}:</label>
                    <select class="form-control" id="brand_id" name="brand_id">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected($brand->id == $product->brand_id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <i class="ti ti-photo me-2"></i>
                        {{ __('Thư viện ảnh') }}
                    </div>
                    <div class="card-body row">
                        <!-- longitude -->
                        <div class="col-12">
                            <div class="card-body p-2">
                                <x-input-gallery-ckfinder required name="gallery[]" type="multiple"
                                                          :value="json_decode($product->gallery)"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
