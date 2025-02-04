<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Tiêu đề hướng dẫn -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tiêu đề')</label>
                    <x-input name="title" :value="old('title')" :required="true" :placeholder="__('Tiêu đề')" />
                </div>
            </div>

            <!-- Mô tả -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Mô tả')</label>
                    <textarea name="description" class="ckeditor">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Các tháng phát triển -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>@lang('Các tháng phát triển')</h4>
                        <button type="button" id="add-step-btn" class="btn btn-primary">@lang('Thêm tháng mới')</button>
                    </div>
                    <div class="card-body">
                        <div id="steps-container">
                            <div id="steps-list">
                                <!-- Các bước được thêm vào đây -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
