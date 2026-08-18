<div class="col-12 col-md-9">
    <div class="card custom-shadow">
          <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin bước phát triển:') }} <x-link :href="route('admin.guide.edit', $guide->id)">{{ $guide->title }}</x-link></h2>
        </div>
        <div class="row card-body">

            <div class="col-12">
                <div class="mb-3">
                    <div class="step-item border rounded p-3 mb-3" id=" ">
                        <div class="mb-3">
                            <label class="control-label d-block text-start">@lang('Tiêu đề tháng') <span class="text-danger">*</span></label>
                            <x-input :required="true" name="title"
                                     :placeholder="__('Tiêu đề bước')" class="w-100" value="{{old('title')}}"/>
                        </div>

                        <div class="mb-3">
                            <label class="control-label d-block text-start">@lang('Mô tả tháng')</label>
                            <textarea name="description"
                                      class="form-control ckeditor"
                                      placeholder="@lang('Mô tả tháng')">{{old('description')}}
                            </textarea>
                        </div>

                        <input type="hidden" name="guide_id" value="{{request()->route('developGuideId')}}"/>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
