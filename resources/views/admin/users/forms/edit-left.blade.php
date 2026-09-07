<div class="col-12 col-lg-8 col-xl-9">
    <div class="card border-0 custom-shadow rounded-3">
        <div class="card-header bg-white border-bottom p-3">
            <!-- Modern Nav tabs -->
            <ul class="nav nav-pills custom-profile-tabs" id="userProfileTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active"
                            id="basic-info-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#basicInfo"
                            type="button"
                            role="tab"
                            aria-controls="basicInfo"
                            aria-selected="true">
                        <i class="ti ti-user-circle fs-4"></i>
                        <span>{{ __('Thông Tin Cơ Bản') }}</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                            id="parent-info-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#parentInfo"
                            type="button"
                            role="tab"
                            aria-controls="parentInfo"
                            aria-selected="false">
                        <i class="ti ti-heart-handshake fs-4"></i>
                        <span>{{ __('Thông Tin Phụ Huynh') }}</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                            id="package-info-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#packageInfo"
                            type="button"
                            role="tab"
                            aria-controls="packageInfo"
                            aria-selected="false">
                        <i class="ti ti-package fs-4"></i>
                        <span>{{ __('Gói Dịch Vụ') }}</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                            id="children-info-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#childrenInfo"
                            type="button"
                            role="tab"
                            aria-controls="childrenInfo"
                            aria-selected="false">
                        <i class="ti ti-baby-carriage fs-4"></i>
                        <span>{{ __('Danh Sách Trẻ Em') }}</span>
                        <span class="badge bg-primary-lt ms-1">{{ $user->children()->count() }}</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                            id="devices-info-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#devicesInfo"
                            type="button"
                            role="tab"
                            aria-controls="devicesInfo"
                            aria-selected="false">
                        <i class="ti ti-devices fs-4"></i>
                        <span>{{ __('Thiết Bị Đang Dùng') }}</span>
                        <span class="badge bg-azure-lt ms-1">{{ $user->activeDevices()->count() }}/{{ $user->getMaxDevicesAllowed() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <!-- Tab Content -->
            <div class="tab-content" id="userProfileTabContent">
                <!-- Tab 1: Thông tin cơ bản -->
                <div class="tab-pane fade show active"
                     id="basicInfo"
                     role="tabpanel"
                     aria-labelledby="basic-info-tab">
                    @include('admin.users.partials.edit-info-user')
                </div>

                <!-- Tab 2: Thông tin phụ huynh -->
                <div class="tab-pane fade"
                     id="parentInfo"
                     role="tabpanel"
                     aria-labelledby="parent-info-tab">
                    @include('admin.users.partials.edit-info-parent')
                </div>

                <!-- Tab 3: Gói dịch vụ -->
                <div class="tab-pane fade"
                     id="packageInfo"
                     role="tabpanel"
                     aria-labelledby="package-info-tab">
                    @include('admin.users.partials.package.package-info')
                </div>

                <!-- Tab 4: Danh sách trẻ em -->
                <div class="tab-pane fade"
                     id="childrenInfo"
                     role="tabpanel"
                     aria-labelledby="children-info-tab">
                    @include('admin.users.partials.children-info')
                </div>

                <!-- Tab 5: Thiết bị đang dùng -->
                <div class="tab-pane fade"
                     id="devicesInfo"
                     role="tabpanel"
                     aria-labelledby="devices-info-tab">
                    @include('admin.users.partials.user-devices')
                </div>
            </div>
        </div>
    </div>
</div>
