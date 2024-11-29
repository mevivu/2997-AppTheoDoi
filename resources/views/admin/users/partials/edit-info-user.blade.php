@php use App\AES\AESHelper; @endphp
<div class="card">
    <div class="card-header justify-content-center">
        <h2 class="mb-0">{{ __('Thông tin Thành viên') }}</h2>

    </div>
    <div class="row card-body">
        <!-- Fullname -->
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    {{ __('Họ và tên') }}:</label>
                <x-input name="fullname" :value="$user->fullname" :required="true" placeholder="{{ __('Họ và tên') }}"/>
            </div>
        </div>
        <!-- email -->
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-mail"></span>
                    {{ __('Email') }}:</label>
                <x-input-email name="email" :value="AESHelper::decrypt($user->email)" :required="true"/>
            </div>
        </div>
        <!-- new password -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-lock"></span>
                    @lang('password'):</label>
                <x-input-password name="password"/>
            </div>
        </div>
        <!-- new password confirmation-->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-lock"></span>
                    @lang('passwordConfirm'):</label>
                <x-input-password name="password_confirmation" data-parsley-equalto="input[name='password']"
                                  data-parsley-equalto-message="{{ __('Mật khẩu không khớp.') }}"/>
            </div>
        </div>
        <!-- phone -->
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-phone"></span>
                    {{ __('Số điện thoại') }}:</label>
                <x-input-phone name="phone" :value="AESHelper::decrypt($user->phone)" :required="true"/>
            </div>
        </div>
        <!-- birthday -->
        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-calendar"></span>
                    @lang('birthday'):</label>
                <x-input type="date" name="birthday"
                         :value="isset($user->birthday) ? format_date($user->birthday, 'Y-m-d') : null"
                         required="true"/>
            </div>
        </div>

        <!-- gender -->
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-user-circle"></span>
                    {{ __('Giới tính') }}:</label>
                <x-select name="gender" :required="true">
                    <x-select-option value="" :title="__('Chọn Giới tính')"/>
                    @foreach ($gender as $key => $value)
                        <x-select-option :option="$user->gender->value" :value="$key" :title="__($value)"/>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="mb-3">
                <label class="control-label">
                    <span class="ti ti-map"></span>
                    @lang('Địa chỉ gợi ý'):</label>
                <x-input name="name" :value="old('Địa chỉ gợi ý')" :placeholder="__('Địa chỉ gợi ý')"/>
            </div>
        </div>
        <!-- address -->
        <div class="col-12">
            <div class="mb-3">
                <x-input-pick-address :label="trans('address')" name="address"
                                      :value="AESHelper::decrypt($user->address)"
                                      :placeholder="trans('pickAddress')"
                                      :required="true"/>
                <x-input type="hidden" name="lat" :value="old('lat')"/>
                <x-input type="hidden" name="lng" :value="old('lng')"/>
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
                           id="check{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                    <label class="form-check-label" for="flexCheckDefault">
                        {{ $role->title }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
