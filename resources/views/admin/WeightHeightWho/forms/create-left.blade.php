<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Chiều cao cân nặng theo chuẩn who') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Cân nặng') }}:</label>
                    <x-input type="number" min="1" name="weight" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Chiều Cao') }}:</label>
                    <x-input type="number" min="1" name="height" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tuổi') }}:</label>
                    <x-input type="number" min="1" name="age" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tháng') }}:</label>
                    <x-input type="number" min="1" max="12" name="month" :required="true"/>
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

        </div>
    </div>
</div>
