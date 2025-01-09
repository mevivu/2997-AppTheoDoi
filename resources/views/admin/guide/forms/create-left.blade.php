<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Tiêu đề hướng dẫn -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tiêu đề')</label>
                    <x-input name="title" :value="old('title')" :required="true" :placeholder="__('Tiêu đề ')" />
                </div>
            </div>

            <!-- Mô tả -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Mô tả')</label>
                    <textarea name="description" class="ckeditor">{{ old('description') }}</textarea>
                </div>
            </div>


        </div>
    </div>
</div>
