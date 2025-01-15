<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin BMI tiêu chuẩn') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tuổi') }}:</label>
                    <x-input type="number" min="0" name="age" :value="old('age')" :required="true"/>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Giới tính') }}:</label>
                    <x-select name="gender" :required="true">
                        @foreach ($gender as $key => $value)
                            <x-select-option :value="$key" :title="$value"/>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="mb-3">
                    <label class="control-label">{{ __('Z-score -3') }}:</label>
                    <x-input type="number"
                             min="0" step="0.01"
                             name="z_score_minus_3"
                             :value="old('z_score_minus_3')"
                             :required="true"/>
                </div>
            </div>

            <div class="col-md-3">
                <div class="mb-3">
                    <label class="control-label">{{ __('Z-score -2') }}:</label>
                    <x-input type="number"
                             step="0.01"
                             name="z_score_minus_2"
                             :value="old('z_score_minus_2')"
                             :required="true"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="control-label">{{ __('Z-score -1') }}:</label>
                    <x-input type="number"
                             step="0.01"
                             name="z_score_minus_1"
                             :value="old('z_score_minus_1')"
                             :required="true"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="control-label">{{ __('Z-score 0') }}:</label>
                    <x-input type="number"
                             step="0.01"
                             name="z_score_0"
                             :value="old('z_score_0')"
                             :required="true"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="control-label">{{ __('Z-score +1') }}:</label>
                    <x-input type="number"
                             step="0.01"
                             name="z_score_plus_1"
                             :value="old('z_score_plus_1')"
                             :required="true"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="control-label">{{ __('Z-score +2') }}:</label>
                    <x-input type="number"
                             step="0.01"
                             name="z_score_plus_2"
                             :value="old('z_score_plus_2')"
                             :required="true"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="control-label">{{ __('Z-score +3') }}:</label>
                    <x-input type="number"
                             step="0.01"
                             name="z_score_plus_3"
                             :value="old('z_score_plus_3')"
                             :required="true"/>
                </div>
            </div>
        </div>
    </div>
</div>
