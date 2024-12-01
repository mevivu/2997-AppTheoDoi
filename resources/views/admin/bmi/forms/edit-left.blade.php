<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin BMI tiêu chuẩn') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tuổi') }}:</label>
                    <x-input type="number" min="0" name="age" :value="$response->age" :required="true" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Giới tính') }}:</label>
                    <x-select name="gender" :required="true">
                        @foreach ($gender as $key => $value)
                            <x-select-option :value="$key" :title="$value" :option="$response->gender->value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Chỉ số BMI tiêu chuẩn') }}:</label>
                    <x-input type="number" min="0" step="0.01" name="bmi" :value="$response->bmi"
                        :required="true" />
                </div>
            </div>
        </div>
    </div>
</div>
