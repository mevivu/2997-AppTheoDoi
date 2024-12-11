<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin chiều cao cân nặng dự kiến') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tuổi') }}:</label>
                    <x-input type="number" min="0" name="age" :value="old('age')" :required="true" />
                </div>
            </div>

            <div class="col-md-6 mb-3"></div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Chiều cao dự kiến (cm)') }}:</label>
                    <x-input type="number" min="1" step="0.01" name="height_expected" :value="old('height_expected')"
                        :required="true" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Cân nặng dự kiến (kg)') }}:</label>
                    <x-input type="number" min="1" step="0.01" name="weight_expected" :value="old('weight_expected')"
                        :required="true" />
                </div>
            </div>
        </div>
    </div>
</div>
