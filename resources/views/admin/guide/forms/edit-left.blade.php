<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tiêu đề hướng dẫn')</label>
                    <x-input type="text" name="title" :value="$instance->title" :required="true" :placeholder="__('Tiêu đề hướng dẫn')" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Mô tả') }}:</label>
                    <textarea name="description" class="ckeditor visually-hidden">{{ $instance->description }}</textarea>
                </div>
            </div>

        </div>
    </div>
</div>
