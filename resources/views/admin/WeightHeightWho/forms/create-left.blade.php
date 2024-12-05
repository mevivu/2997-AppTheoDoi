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
                        {{ __('Cân nặng') }}(Kg):</label>
                    <x-input type="number" min="1" name="weight" placeholder="Nhập Cân nặng(kg) của bạn" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-building-lighthouse"></span>
                        {{ __('Chiều Cao') }}(cm):</label>
                    <x-input type="number" min="1" name="height" placeholder="Nhập chiều cao(cm) của bạn" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-old"></span>
                        {{ __('Tuổi') }}:</label>
                    <x-input type="number" min="1" name="age" placeholder="Nhập tuổi của bạn" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-calendar-stats"></span>
                        {{ __('Tháng') }}:</label>
                    <x-input type="number" min="1" max="12" placeholder="Nhập tháng của bạn" name="month" :required="true"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label"><span class="ti ti-gender-genderfluid"></span>
                        {{ __('Giới tính') }}:</label>
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
