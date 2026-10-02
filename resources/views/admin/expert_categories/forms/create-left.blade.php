<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin Chuyên mục') }}</h2>
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-category"></span>
                        {{ __('Tên danh mục chuyên đề') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input name="name" :value="old('name')" :required="true" placeholder="{{ __('VD: Dinh dưỡng & Phát triển, Vận động & Chiều cao, Tâm lý...') }}" />
                </div>
            </div>

            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-file-text"></span>
                        {{ __('Mô tả chuyên mục') }}:
                    </label>
                    <textarea class="form-control" name="description" rows="4" placeholder="{{ __('Mô tả ngắn về chủ đề chuyên môn...') }}">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
