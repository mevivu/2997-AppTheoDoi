@php
    use App\AES\AESHelper;
    use Carbon\Carbon;
    use App\Enums\Child\BornStatus;

    $birthday = $children->birthday ? Carbon::parse($children->birthday)->format('Y-m-d') : '';
    $due_date = $children->due_date ? Carbon::parse($children->due_date)->format('Y-m-d') : '';
    $parentUser = $children->user;
    $decryptedParentEmail = $parentUser?->email ? AESHelper::decrypt($parentUser->email) : '';
    $decryptedParentPhone = $parentUser?->phone ? AESHelper::decrypt($parentUser->phone) : '';
    $parentPackage = $parentUser?->userPackages()->where('status', 'active')->first();
@endphp

<div class="row g-4">
    <!-- Block 1: Thông tin của bé -->
    <div class="col-12">
        <div class="d-flex align-items-center mb-3">
            <span class="badge bg-primary-lt p-2 me-2 rounded-2">
                <i class="ti ti-baby-carriage fs-4"></i>
            </span>
            <div>
                <h5 class="mb-0 fw-bold text-dark">{{ __('Thông Tin Cá Nhân Của Trẻ') }}</h5>
                <small class="text-muted">{{ __('Các thông tin định danh và ngày sinh của bé') }}</small>
            </div>
        </div>

        <div class="row g-3">
            <!-- Fullname -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-user text-primary me-1"></i>
                        {{ __('Họ và tên trẻ') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input name="fullname" :required="true" placeholder="{{ __('Nhập họ và tên bé') }}" :value="$children->fullname" />
                </div>
            </div>

            <!-- Gender -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-gender-intergender text-primary me-1"></i>
                        {{ __('Giới tính') }}: <span class="text-danger">*</span>
                    </label>
                    <x-select name="gender" :required="true">
                        @foreach ($gender as $key => $value)
                            <x-select-option :option="$children->gender?->value ?? $children->gender" :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- Birthday / Due date -->
            <div class="col-12 col-md-6">
                <div class="mb-3 {{ $children->is_born == BornStatus::Born ? '' : 'd-none' }}" id="date_birthday">
                    <label class="form-label fw-bold">
                        <i class="ti ti-calendar-event text-primary me-1"></i>
                        {{ __('Ngày sinh') }}:
                    </label>
                    <x-input type="date" name="birthday" placeholder="{{ __('Ngày sinh') }}" :value="$birthday" />
                </div>

                <div class="mb-3 {{ $children->is_born == BornStatus::Unborn ? '' : 'd-none' }}" id="due_date">
                    <label class="form-label fw-bold">
                        <i class="ti ti-calendar-due text-warning me-1"></i>
                        {{ __('Ngày dự sinh') }}:
                    </label>
                    <x-input type="date" name="due_date" placeholder="{{ __('Ngày dự sinh') }}" :value="$due_date" />
                </div>
            </div>

            <!-- Age & Month -->
            <div class="col-12 col-md-6">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold">
                            <i class="ti ti-clock text-info me-1"></i>
                            {{ __('Tuổi (Năm)') }}:
                        </label>
                        <x-input name="age" disabled placeholder="{{ __('Tuổi') }}" :value="$children->age ?? 0" />
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">
                            <i class="ti ti-hourglass-low text-info me-1"></i>
                            {{ __('Tháng tuổi') }}:
                        </label>
                        <x-input name="month" disabled placeholder="{{ __('Tháng') }}" :value="$children->month ?? 0" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Block 2: Thông tin Phụ huynh / Khách hàng liên kết -->
    <div class="col-12">
        <div class="d-flex align-items-center mb-3">
            <span class="badge bg-success-lt p-2 me-2 rounded-2">
                <i class="ti ti-users fs-4"></i>
            </span>
            <div>
                <h5 class="mb-0 fw-bold text-dark">{{ __('Thông Tin Phụ Huynh / Khách Hàng') }}</h5>
                <small class="text-muted">{{ __('Tài khoản cha mẹ và thông tin gia đình liên kết') }}</small>
            </div>
        </div>

        <div class="row g-3">
            <!-- Select User Parent -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-user-search text-primary me-1"></i>
                        {{ __('Chọn Tài Khoản Cha / Mẹ') }}: <span class="text-danger">*</span>
                    </label>
                    <x-select class="select2-bs5-ajax" name="user_id" id="user_id" :data-url="route('admin.search.select.user')">
                        @if($children->user)
                            <x-select-option :option="$children->user_id" :value="$children->user_id" :title="$children->user->fullname . ' - ' . ($decryptedParentPhone ?: 'Chưa có SĐT')" :selected="true" />
                        @endif
                    </x-select>
                </div>
            </div>

            <!-- Parent Profile Summary Card -->
            @if($parentUser)
                <div class="col-12">
                    <div class="child-parent-summary-card">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary text-white p-2 rounded-3 fs-5">
                                    <i class="ti ti-home-heart"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark fs-14">{{ $parentUser->fullname }} <span class="badge bg-light text-muted">#{{ $parentUser->id }}</span></h6>
                                    <small class="text-muted">
                                        <i class="ti ti-mail me-1"></i>{{ $decryptedParentEmail ?: 'Chưa cập nhật' }} | 
                                        <i class="ti ti-phone me-1"></i>{{ $decryptedParentPhone ?: 'Chưa cập nhật' }}
                                    </small>
                                </div>
                            </div>
                            <a href="{{ route('admin.user.edit', $parentUser->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="ti ti-external-link me-1"></i> {{ __('Xem Hồ Sơ Khách Hàng') }}
                            </a>
                        </div>

                        <div class="row g-3 pt-2 border-top">
                            <!-- Father Info -->
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-white rounded-3 border">
                                    <div class="fw-bold text-primary fs-13 mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-user-check"></i> {{ __('Thông tin Bố:') }}
                                    </div>
                                    <div class="fs-13 text-dark mb-1">
                                        <strong>{{ __('Họ tên:') }}</strong> {{ $parentUser->father_name ?: __('Chưa cập nhật') }}
                                    </div>
                                    <div class="fs-12 text-muted">
                                        <span><i class="ti ti-ruler-2 me-1"></i>{{ __('Chiều cao:') }} {{ $parentUser->father_height ? $parentUser->father_height . ' cm' : __('Chưa rõ') }}</span>
                                        <span class="ms-3"><i class="ti ti-calendar me-1"></i>{{ __('Ngày sinh:') }} {{ $parentUser->father_birthday ? format_date($parentUser->father_birthday, 'd/m/Y') : __('Chưa rõ') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Mother Info -->
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-white rounded-3 border">
                                    <div class="fw-bold text-danger fs-13 mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-user-heart"></i> {{ __('Thông tin Mẹ:') }}
                                    </div>
                                    <div class="fs-13 text-dark mb-1">
                                        <strong>{{ __('Họ tên:') }}</strong> {{ $parentUser->mother_name ?: __('Chưa cập nhật') }}
                                    </div>
                                    <div class="fs-12 text-muted">
                                        <span><i class="ti ti-ruler-2 me-1"></i>{{ __('Chiều cao:') }} {{ $parentUser->mother_height ? $parentUser->mother_height . ' cm' : __('Chưa rõ') }}</span>
                                        <span class="ms-3"><i class="ti ti-calendar me-1"></i>{{ __('Ngày sinh:') }} {{ $parentUser->mother_birthday ? format_date($parentUser->mother_birthday, 'd/m/Y') : __('Chưa rõ') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
