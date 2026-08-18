<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">

            <!-- Tên thương hiệu -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên thương hiệu') <span class="text-danger">*</span></label>
                    <x-input type="text" name="name" :value="old('name')" :required="true"
                             :placeholder="__('Tên thương hiệu')"/>
                </div>
            </div>


            <!-- Quốc gia -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Quốc gia')</label>
                    <x-input type="text" name="country" :value="old('country')" :required="false"
                             :placeholder="__('Quốc gia thương hiệu')"/>
                </div>
            </div>

            <!-- Mô tả -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Mô tả') }}:</label>
                    <textarea name="description" class="ckeditor visually-hidden">{{old('description')}}</textarea>
                </div>
            </div>

        </div>
    </div>
</div>
