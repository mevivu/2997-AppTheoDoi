<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Nhật ký') }}</h2>
        </div>
        <div class="row card-body">

            <!-- Title -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-article"></span>
                        {{ __('Tiêu đề') }}:</label>
                    <x-input name="title" :value="old('title')" :required="true" placeholder="{{ __('Tiêu đề') }}"/>
                </div>
            </div>

            <!-- children -->
            <div class="col-md-6 col-sm-12">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    @lang('Trẻ em'):</label>
                <x-select class="select2-bs5-ajax" name="child_id" id="child_id"
                          :data-url="route('admin.search.select.children')">
                </x-select>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-box-padding"></span>
                        {{ __('Nội dung') }}:</label>
                    <textarea name="content" class="ckeditor visually-hidden">{{ old('content') }}</textarea>
                </div>
            </div>

            {{--  image--}}
            <div>
                <div class="col-12">
                    <div class="card-body p-2">
                        <x-input-gallery-ckfinder name="image[]"
                                                  type="multiple"
                                                  label="Hình ảnh"/>


                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
