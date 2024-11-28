<div class="card">
    <div class="cart-header">
        <div class="card-header">
            <h4>{{ __('Thông tin Khách hàng') }}</h4>
        </div>
    </div>
    <div class="row card-body">
        <!-- Fullname -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    {{ __('Họ và tên') }}:</label>
                <x-input name="fullname" :value="old('fullname')" :required="true" placeholder="{{ __('Họ và tên') }}" />
            </div>
        </div>
        <!-- email -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-mail"></span>
                    {{ __('Email') }}:</label>
                <x-input-email name="email" :value="old('email')" :required="true" />
            </div>
        </div>
        <!-- new password -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-lock"></span>
                    @lang('password'):</label>
                <x-input-password name="password" :required="true" />
            </div>
        </div>
        <!-- new password confirmation-->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-lock"></span>
                    @lang('passwordConfirm'):</label>
                <x-input-password name="password_confirmation" :required="true"
                    data-parsley-equalto="input[name='password']"
                    data-parsley-equalto-message="{{ __('passwordMismatch') }}" />
            </div>
        </div>
        <!-- phone -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-phone"></span>
                    {{ __('Số điện thoại') }}:</label>
                <x-input-phone name="phone" :value="old('phone')" :required="true" />
            </div>
        </div>
        <!-- birthday -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-calendar"></span>
                    @lang('birthday'):</label>
                <x-input type="date" name="birthday" :value="old('birthday')" />
            </div>
        </div>

        {{--        --}}{{-- Bank Name --}}
        {{--        <div class="col-md-6 col-12"> --}}
        {{--            <div class="mb-3"> --}}
        {{--                <label class="control-label"> --}}
        {{--                    <span class="ti ti-wallet"></span> --}}
        {{--                    @lang('bank_name'):</label> --}}
        {{--                <x-select name="bank_id" :required="true"> --}}
        {{--                    <x-select-option value="" :title="__('choose')" :selected="!old('bank_id')" /> --}}
        {{--                    @foreach ($banks as $bank) --}}
        {{--                        <x-select-option :value="$bank->id" :title="__($bank->name)" :selected="$bank->id == old('bank_id')" /> --}}
        {{--                    @endforeach --}}
        {{--                </x-select> --}}
        {{--            </div> --}}
        {{--        </div> --}}

        {{--        --}}{{-- bank_account_number --}}
        {{--        <div class="col-md-6 col-12"> --}}
        {{--            <div class="mb-3"> --}}
        {{--                <label class="control-label"> --}}
        {{--                    <span class="ti ti-credit-card"></span> --}}
        {{--                    @lang('bank_account_number'):</label> --}}
        {{--                <x-input name="bank_account_number" :value="old('bank_account_number')" :placeholder="__('bank_account_number')" /> --}}
        {{--            </div> --}}
        {{--        </div> --}}
        <!-- gender -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-user-circle"></span>
                    {{ __('Giới tính') }}:</label>
                <x-select name="gender" :required="true">
                    @foreach ($gender as $key => $value)
                        <x-select-option :value="$key" :title="__($value)" />
                    @endforeach
                </x-select>
            </div>
        </div>
        {{-- address_name --}}
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-map"></span>
                    @lang('Địa chỉ gợi ý'):</label>
                <x-input name="name" :value="old('Địa chỉ gợi ý')" :placeholder="__('Địa chỉ gợi ý')" />
            </div>
        </div>
        <!-- address -->
        <div class="col-12">
            <div class="mb-3">
                <x-input-pick-address :label="trans('address')" name="address" :value="old('address')" :placeholder="trans('pickAddress')"
                    :required="true" />
                <x-input type="hidden" name="lat" :value="old('lat')" />
                <x-input type="hidden" name="lng" :value="old('lng')" />
            </div>
        </div>
    </div>
    <!-- Role -->
    <div class="card-footer">
        <label class="control-label mb-3 fw-medium">
            <i class="ti ti-shield"></i>
            {{ __('Vai trò') }}:</label>
        <div class="d-flex align-items-center justify-start gap-4">
            @foreach ($roles as $role)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $role->name }}" name="roles[]"
                        id="check{{ $role->id }}">
                    <label class="form-check-label" for="flexCheckDefault">
                        {{ $role->title }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
