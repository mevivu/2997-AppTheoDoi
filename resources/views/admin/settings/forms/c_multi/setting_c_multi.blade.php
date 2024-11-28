<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h2 class="mb-0">
            <i class="ti ti-timeline me-2"></i> {{ __('Khung giờ cao điểm') }}
        </h2>
    </div>
    <div class="card-body">
        <h4 class="text-secondary mb-3">{{ __('Buổi sáng') }}</h4>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label text-danger fw-bold">
                        <i class="ti ti-clock me-2"></i>{{ __('Giờ bắt đầu') }}:
                    </label>
                    <input type="time" 
                        name="bike_C_Multi_morning_start" 
                        class="form-control" 
                        id="bike_C_Multi_morning_start"
                        value="{{ $bikeC_Multi_Rush_Hour['bike_C_Multi_morning_start'] }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label text-danger fw-bold">
                        <i class="ti ti-clock me-2"></i>{{ __('Giờ kết thúc') }}:
                    </label>
                    <input type="time" 
                        name="bike_C_Multi_morning_end" 
                        class="form-control" 
                        id="bike_C_Multi_morning_end"
                        value="{{ $bikeC_Multi_Rush_Hour['bike_C_Multi_morning_end'] }}">
                </div>
            </div>
        </div>

        <h4 class="text-secondary mb-3">{{ __('Buổi chiều') }}</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label text-danger fw-bold">
                        <i class="ti ti-clock me-2"></i>{{ __('Giờ bắt đầu') }}:
                    </label>
                    <input type="time" 
                        name="bike_C_Multi_afternoon_start" 
                        class="form-control" 
                        id="bike_C_Multi_afternoon_start"
                        value="{{ $bikeC_Multi_Rush_Hour['bike_C_Multi_afternoon_start'] }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label text-danger fw-bold">
                        <i class="ti ti-clock me-2"></i>{{ __('Giờ kết thúc') }}:
                    </label>
                    <input type="time" 
                        name="bike_C_Multi_afternoon_end" 
                        class="form-control" 
                        id="bike_C_Multi_afternoon_end"
                        value="{{ $bikeC_Multi_Rush_Hour['bike_C_Multi_afternoon_end'] }}">
                </div>
            </div>
        </div>
    </div>
</div>