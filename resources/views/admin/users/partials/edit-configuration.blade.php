<div class="card">
    <div class="card-header">
        <h4>{{ __('Thông tin Cấu hình sắp xếp C-Ride/Car') }}</h4>
    </div>
    <div class="card-body row">
        <!-- Cost Preference -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">@lang('cost_preference'):</label>
                <x-select name="cost_preference" :required="true">
                    @foreach ($cost_preferences as $key => $value)
                        <x-select-option :option="$user->cost_preference->value" :value="$key" :title="__($value)"/>
                    @endforeach
                </x-select>
            </div>
        </div>

        <!-- Vehicle Change Preference -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">@lang('car_life'):</label>
                <x-select name="car_life" :required="true">
                    @foreach ($car_lives as $key => $value)
                        <x-select-option :option="$user->car_life->value" :value="$key" :title="__($value)"/>
                    @endforeach
                </x-select>
            </div>
        </div>

        <!-- Rating Preference -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">@lang('rating_preference'):</label>
                <x-select name="rating_preference" :required="true">
                    @foreach ($rating_preferences as $key => $value)
                        <x-select-option :option="$user->rating_preference->value" :value="$key" :title="__($value)"/>
                    @endforeach
                </x-select>
            </div>
        </div>

        <!-- Discount Preference -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">@lang('discount_preference'):</label>
                <x-select name="discount_preference" :required="true">
                    @foreach ($discount_preferences as $key => $value)
                        <x-select-option :option="$user->discount_preference->value" :value="$key" :title="__($value)"/>
                    @endforeach
                </x-select>
            </div>
        </div>

        <!-- Distance Preference -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">@lang('distance_preference'):</label>
                <x-select name="distance_preference" :required="true">
                    @foreach ($distance_preferences as $key => $value)
                        <x-select-option :option="$user->distance_preference->value" :value="$key" :title="__($value)"/>
                    @endforeach
                </x-select>
            </div>
        </div>

        <!-- vehicle_type -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">@lang('Loại xe'):</label>
                <x-select name="vehicle_type" :required="true">
                    @foreach ($vehicle_types as $key => $value)
                        <x-select-option :option="$user->vehicle_type->value" :value="$key" :title="__($value)"/>
                    @endforeach
                </x-select>
            </div>
        </div>

        {{-- price_setting --}}
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="control-label">{{ __('price_setting_c_car') }}:</label>
                <x-input-price name="price_setting_c_car"
                               id="price_setting_c_car"
                               :value="$user->price_setting_c_car"
                               :required="true"
                               :placeholder="__('price_setting_c_car')"/>
            </div>
        </div>
    </div>

</div>

