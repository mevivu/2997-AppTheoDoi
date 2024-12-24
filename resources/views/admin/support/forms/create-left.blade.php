<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Hỗ trợ khách hàng') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('title') }}:</label>
                    <x-input type="text" name="title" :value="old('title')" :required="true" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('content') }}</label>
                    <textarea name="content" class="ckeditor visually-hidden">{{ old('content') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
