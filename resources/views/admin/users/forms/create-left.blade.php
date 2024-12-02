<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin khách hàng') }}</h2>
        </div>
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-info-tab" data-bs-toggle="tab" data-bs-target="#basicInfo"
                        type="button" role="tab" aria-controls="basicInfo" aria-selected="true">
                        <i class="ti ti-user-check"></i>
                        {{ __('Thông Tin Cơ Bản') }}
                    </button>
                </li>

            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="basicInfo" role="tabpanel" aria-labelledby="basic-info-tab">
                    <!-- Include Basic Info Content Here -->
                    @include('admin.users.partials.create-info-user')
                </div>

            </div>
        </div>
    </div>
</div>
