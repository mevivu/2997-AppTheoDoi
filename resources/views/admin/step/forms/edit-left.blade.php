<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">
            <div class="card-header justify-content-center">
                <h2 class="mb-0">{{ __('Thông tin bước phát triển:') }} <x-link :href="route('admin.guide.edit', $guide->id)">{{ $guide->title }}</x-link></h2>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <div class="step-item border rounded p-3 mb-3" id=" ">
                        <div class="mb-3">
                            <label class="control-label d-block text-start">@lang('Tiêu đề tháng') <span class="text-danger">*</span></label>
                            <x-input :required="true"
                                        name="title" type="text"
                                     :placeholder="__('Tiêu đề bước')" class="w-100" value="{{$instance->title}}"/>
                        </div>

                        <div class="mb-3">
                            <label class="control-label d-block text-start">@lang('Mô tả tháng')</label>
                            <textarea name="description"
                                      class="form-control ckeditor"
                                      placeholder="@lang('Mô tả tháng')">{{$instance->description}}
                            </textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="control-label d-block text-start">@lang('Thứ tự') <span class="text-danger">*</span></label>
                            <x-input type="number" name="order" :required="true"
                                     class="step-order" value="{{$instance->order}}"/>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
