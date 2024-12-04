<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Chiều cao cân nặng theo chuẩn who') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Cân nặng') }}:</label>
                    <x-input type="number" min="1" name="weight" :value="intval($response->weight)" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Chiều cao') }}:</label>
                    <x-input type="number" min="1" name="height" :value="intval($response->height)" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tuổi') }}:</label>
                    <x-input type="number" min="0" name="age" :value="$response->age" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tháng') }}:</label>
                    <x-input type="number" min="1" max="12" name="month" :value="$response->month" :required="true"/>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Giới tính') }}:</label>
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
