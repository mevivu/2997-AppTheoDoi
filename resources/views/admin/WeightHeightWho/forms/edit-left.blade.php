<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Chiều cao cân nặng theo chuẩn who') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-weight"></span>
                        {{ __('Cân nặng') }}(kg): <span class="text-danger">*</span></label>
                    <x-input type="number"
                             min="1"
                             name="weight"
                             placeholder="Nhập cân nặng(kg) của bạn"
                             :value="intval($response->weight)"
                             :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-building-lighthouse"></span>
                        {{ __('Chiều cao') }}(cm): <span class="text-danger">*</span></label>
                    <x-input type="number"
                             min="1"
                             name="height"
                             placeholder="Nhập chiều cao(cm) của bạn"
                             :value="intval($response->height)"
                             :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-old"></span>
                        {{ __('Tuổi') }}:</label>
                    <x-input type="number"
                             min="0"
                             name="age"
                             disabled
                             placeholder="Nhập Tuổi của bạn"
                             :value="$response->age"
                             :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-calendar-stats"></span>
                        {{ __('Tháng') }}:</label>
                    <x-input type="number"
                             min="1"
                             max="12"
                             disabled
                             name="month"
                             placeholder="Nhập Tháng của bạn"
                             :value="$response->month"
                             :required="true"/>
                </div>
            </div>
            <!-- New field for weight change -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-exchange-vertical"></span>
                        {{ __('Tăng cân so với kỳ trước') }}(Kg):</label>
                    <x-input type="number"
                             step="0.01"
                             name="weight_change"
                             :value="$response->weight_change"
                             placeholder="Nhập thay đổi cân nặng(kg)"
                             :required="true"/>
                </div>
            </div>

            <!-- New field for height change -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-exchange-vertical"></span>
                        {{ __('Tăng chiều cao so với kỳ trước') }}(cm):</label>
                    <x-input type="number"
                             step="0.01"
                             name="height_change"
                             :value="$response->height_change"
                             placeholder="Nhập thay đổi chiều cao(cm)"
                             :required="true"/>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-gender-genderfluid"></span>
                        {{ __('Giới tính') }}: <span class="text-danger">*</span></label>
                    <x-select name="gender" :required="true">
                        @foreach ($gender as $key => $value)
                            <x-select-option :value="$key" :title="$value" :option="$response->gender->value"/>
                        @endforeach
                    </x-select>
                </div>
            </div>


        </div>
    </div>
</div>
