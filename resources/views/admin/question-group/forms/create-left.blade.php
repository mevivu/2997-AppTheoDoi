<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin nhóm câu hỏi') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tên nhóm câu hỏi') }}:</label>
                    <x-input type="text" name="name" :value="old('name')" :required="true" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Mô tả') }}:</label>
                    <textarea name="description" class="ckeditor visually-hidden">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
