<div class="card">
    <div class="cart-header">
        <div class="card-header">
            <h4>{{ __('Thông tin phụ huynh') }}</h4>
        </div>
    </div>
    <div class="row card-body">
        <!-- father_name -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    {{ __('father_name') }}:</label>
                <x-input name="father_name"
                         :value="$user->father_name"
                         placeholder="{{ __('father_name') }}" />
            </div>
        </div>

        <!-- Mother's Name -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    {{ __('Tên của mẹ') }}:</label>
                <x-input name="mother_name"
                         :value="$user->mother_name"
                         placeholder="{{ __('Tên của mẹ') }}" />
            </div>
        </div>

        <!-- Father's Height -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-ruler-alt"></span>
                    {{ __('Chiều cao của bố (cm)') }}:</label>
                <x-input type="number"
                         name="father_height"
                         :value="$user->father_height"
                         placeholder="{{ __('Chiều cao của bố') }}" />
            </div>
        </div>


        <!-- Mother's Height -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-ruler-alt"></span>
                    {{ __('Chiều cao của mẹ (cm)') }}:</label>
                <x-input type="number"
                         name="mother_height"
                         :value="$user->mother_height"
                         placeholder="{{ __('Chiều cao của mẹ') }}" />
            </div>
        </div>

        <!-- Father's Birthday -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-calendar"></span>
                    {{ __('Ngày sinh của bố') }}:</label>
                <x-input type="date" name="father_birthday"
                         :value="$user->father_birthday"
                         placeholder="{{ __('Ngày sinh của bố') }}" />
            </div>
        </div>


        <!-- Mother's Birthday -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-calendar"></span>
                    {{ __('Ngày sinh của mẹ') }}:</label>
                <x-input type="date" name="mother_birthday"
                         :value="$user->mother_birthday"
                         placeholder="{{ __('Ngày sinh của mẹ') }}" />
            </div>
        </div>

    </div>
</div>
