<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Tên thương hiệu -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên thương hiệu')</label>
                    <x-input name="name" :value="old('name')" :required="true" :placeholder="__('Tên thương hiệu')" />
                </div>
            </div>

            <!-- Mô tả -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Mô tả')</label>
                    <x-input name="description" :value="old('description')" :required="false" :placeholder="__('Mô tả thương hiệu')" />
                </div>
            </div>

            <!-- Quốc gia -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Quốc gia')</label>
                    <x-input name="country" :value="old('country')" :required="false" :placeholder="__('Quốc gia thương hiệu')" />
                </div>
            </div>

            <!-- Trạng thái -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Trạng thái')</label>
                    <x-select name="status" :required="true">
                        @foreach(\App\Enums\Brand\BrandStatus::asSelectArray() as $key => $value)
                            <x-select-option value="{{ $key }}" title="{{ $value }}" />
                        @endforeach
                    </x-select>
                </div>
            </div>

        </div>
    </div>
</div>
