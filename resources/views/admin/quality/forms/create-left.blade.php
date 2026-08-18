<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thêm phẩm chất mới') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('name') }}: <span class="text-danger">*</span></label>
                    <x-input type="text" name="name" :value="old('name')" :required="true" />
                </div>
            </div>
        </div>
    </div>
</div>
