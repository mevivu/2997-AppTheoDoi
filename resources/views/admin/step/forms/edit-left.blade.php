<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">

            <div class="col-12">
                <div class="mb-3">
                    <div class="step-item border rounded p-3 mb-3" id=" ">
                        <div class="mb-3">
                            <label class="control-label d-block text-start">@lang('Tiêu đề tháng')</label>
                            <x-input :required="true"
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
                            <label class="control-label d-block text-start">@lang('Thứ tự')</label>
                            <x-input type="number" name="order" :required="true"
                                     class="step-order" value="{{$instance->order}}"/>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
