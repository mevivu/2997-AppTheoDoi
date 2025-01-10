<div class="col-12 col-md-3">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin Sản phẩm') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-upload"></span>
                        {{ __('Hành động') }}:</label>
                    <div class="w-100 d-flex align-items-center h-100 gap-2">
                        <x-button.submit :title="__('save')" name="submitter" value="save"
                                         class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                        <x-link :href="route('admin.product.index')" class="w-50 btn btn-outline"
                                :title="'Quay lại'" />
                    </div>
                </div>
            </div>

            <!-- Product Catalog -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-clipboard"></span>
                        {{ __('Danh mục Sản phẩm') }}:
                    </label>
                    <div>
                        @foreach($productCatalogs as $catalog)
                            <x-input-checkbox
                                name="product_catalog_id[]"
                                :label="$catalog->name"
                                :value="$catalog->id"
                                :checked="in_array($catalog->id, old('product_catalog_id', $selectedProductCatalogIds)) ? [$catalog->id] : []"
                            />
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="card mb-3">
                <div class="card-header">
                    @lang('Trạng thái')
                </div>
                <div class="card-body p-2">
                    <x-select name="status" :required="true">
                        @foreach(\App\Enums\Brand\BrandStatus::asSelectArray() as $key => $value)
                            <x-select-option
                                value="{{ $key }}"
                                title="{{ $value }}"
                                :selected="(string) old('status', $product->status->value ?? '') === (string) $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <span class="ti ti-photo me-1"></span>
                    {{ __('Ảnh đại diện') }}
                </div>
                <div class="card-body p-2">
                    <x-input-image-ckfinder name="image" showImage="image" :value="$product->image" />
                </div>
            </div>
        </div>
    </div>
</div>
