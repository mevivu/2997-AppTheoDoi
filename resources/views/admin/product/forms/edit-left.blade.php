<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin Sản phẩm') }}</h2>
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

            <!-- Brand -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-package"></span>
                        {{ __('Thương hiệu') }}:</label>
                    <select class="form-control" name="brand_id">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected($brand->id == $product->brand_id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
