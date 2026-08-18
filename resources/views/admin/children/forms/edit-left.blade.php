<div class="col-12 col-lg-8 col-xl-9">
    <div class="card border-0 custom-shadow rounded-3">
        <!-- Navigation Pills Tabs -->
        <div class="card-header bg-white border-bottom p-3">
            <ul class="nav nav-pills custom-profile-tabs card-header-pills w-100" id="childProfileTabs" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link active w-100 text-center py-2" id="tab-child-info" data-bs-toggle="tab" data-bs-target="#content-child-info" type="button" role="tab" aria-selected="true">
                        <i class="ti ti-baby-carriage me-1 fs-5"></i>
                        <span>{{ __('Thông Tin & Phụ Huynh') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 text-center py-2" id="tab-child-assessment" data-bs-toggle="tab" data-bs-target="#content-child-assessment" type="button" role="tab" aria-selected="false">
                        <i class="ti ti-chart-dots me-1 fs-5"></i>
                        <span>{{ __('Thống Kê Đánh Giá (IQ, EQ, AQ, PQ)') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 text-center py-2" id="tab-child-vaccination" data-bs-toggle="tab" data-bs-target="#content-child-vaccination" type="button" role="tab" aria-selected="false">
                        <i class="ti ti-vaccine me-1 fs-5"></i>
                        <span>{{ __('Lịch Tiêm Chủng') }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content Panes -->
        <div class="card-body p-4">
            <div class="tab-content" id="childProfileTabsContent">
                <!-- Tab 1: Thông tin cơ bản & Phụ huynh -->
                <div class="tab-pane fade show active" id="content-child-info" role="tabpanel" aria-labelledby="tab-child-info">
                    @include('admin.children.partials.child-info', ['children' => $children])
                </div>

                <!-- Tab 2: Thống kê đánh giá -->
                <div class="tab-pane fade" id="content-child-assessment" role="tabpanel" aria-labelledby="tab-child-assessment">
                    @include('admin.children.partials.assessment-info', ['children' => $children])
                </div>

                <!-- Tab 3: Tiêm chủng -->
                <div class="tab-pane fade" id="content-child-vaccination" role="tabpanel" aria-labelledby="tab-child-vaccination">
                    @include('admin.children.partials.vaccination-info', ['children' => $children])
                </div>
            </div>
        </div>
    </div>
</div>
