@php use App\AES\AESHelper; @endphp
<div class="row g-4">
    <!-- Section 1: Thông tin cá nhân & Liên hệ -->
    <div class="col-12">
        <div class="d-flex align-items-center mb-3">
            <span class="badge bg-primary-lt p-2 me-2 rounded-2">
                <i class="ti ti-id fs-4"></i>
            </span>
            <div>
                <h5 class="mb-0 fw-bold text-dark">{{ __('Thông tin định danh & Liên hệ') }}</h5>
                <small class="text-muted">{{ __('Các thông tin cơ bản để nhận diện và liên hệ với khách hàng') }}</small>
            </div>
        </div>

        <div class="row g-3">
            <!-- Fullname -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-user text-primary me-1"></i>
                        {{ __('Họ và tên') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input name="fullname" :value="$user->fullname" :required="true" placeholder="{{ __('Nhập họ và tên khách hàng') }}" />
                </div>
            </div>

            <!-- Email -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-mail text-primary me-1"></i>
                        {{ __('Địa chỉ Email') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input-email name="email" :value="$user->email ? AESHelper::decrypt($user->email) : ''" :required="true" placeholder="{{ __('example@gmail.com') }}" />
                </div>
            </div>

            <!-- Phone -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-phone text-primary me-1"></i>
                        {{ __('Số điện thoại') }}:
                    </label>
                    <x-input-phone name="phone" :value="$user->phone ? AESHelper::decrypt($user->phone) : ''" :required="false" placeholder="{{ __('Nhập số điện thoại') }}" />
                </div>
            </div>

            <!-- Birthday -->
            <div class="col-12 col-md-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-calendar text-primary me-1"></i>
                        {{ __('Ngày sinh') }}:
                    </label>
                    <x-input type="date" name="birthday" :value="isset($user->birthday) ? format_date($user->birthday, 'Y-m-d') : null" />
                </div>
            </div>

            <!-- Gender -->
            <div class="col-12 col-md-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-gender-intergender text-primary me-1"></i>
                        {{ __('Giới tính') }}:
                    </label>
                    <select name="gender" class="form-select">
                        <option value="" {{ is_null($user->gender) ? 'selected' : '' }}>{{ __('-- Chọn Giới tính --') }}</option>
                        @foreach ($gender as $key => $value)
                            <option value="{{ $key }}" {{ !is_null($user->gender) && $user->gender->value == $key ? 'selected' : '' }}>{{ __($value) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Địa chỉ & Vị trí -->
    <div class="col-12">
        <div class="d-flex align-items-center mb-3">
            <span class="badge bg-success-lt p-2 me-2 rounded-2">
                <i class="ti ti-map-pin fs-4"></i>
            </span>
            <div>
                <h5 class="mb-0 fw-bold text-dark">{{ __('Địa chỉ & Vị trí') }}</h5>
                <small class="text-muted">{{ __('Địa chỉ chi tiết và tọa độ trên bản đồ') }}</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <x-input-pick-address :label="trans('address')" name="address" :value="$user->address ? AESHelper::decrypt($user->address) : ''" :placeholder="trans('pickAddress')" />
                    <x-input type="hidden" name="lat" :value="$user->lat ?? old('lat')" />
                    <x-input type="hidden" name="lng" :value="$user->lng ?? old('lng')" />
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Bảo mật & Thay đổi mật khẩu -->
    <div class="col-12">
        <div class="card border rounded-3 bg-light-subtle p-3">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-warning-lt p-2 me-2 rounded-2">
                    <i class="ti ti-shield-lock fs-4"></i>
                </span>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">{{ __('Bảo mật & Đổi mật khẩu') }}</h5>
                    <small class="text-muted">{{ __('Chỉ nhập vào 2 ô dưới đây nếu bạn muốn đặt lại mật khẩu mới cho tài khoản') }}</small>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label fw-bold">
                            <i class="ti ti-lock text-warning me-1"></i>
                            {{ __('Mật khẩu mới') }}:
                        </label>
                        <x-input-password name="password" placeholder="{{ __('Nhập mật khẩu mới (nếu muốn đổi)') }}" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label fw-bold">
                            <i class="ti ti-lock-check text-warning me-1"></i>
                            {{ __('Xác nhận mật khẩu mới') }}:
                        </label>
                        <x-input-password name="password_confirmation"
                                          placeholder="{{ __('Nhập lại mật khẩu mới') }}"
                                          data-parsley-equalto="input[name='password']"
                                          data-parsley-equalto-message="{{ __('Mật khẩu xác nhận không khớp.') }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
