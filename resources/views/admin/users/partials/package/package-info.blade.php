@php
    use Carbon\Carbon;$currentPackage = $user->userPackages()->where('status', 'active')->first();
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Chọn Gói Dịch Vụ') }} <span class="text-danger">*</span></label>
                    <select class="form-select" name="package_id" id="packageSelect" required>
                        <option value="">{{ __('-- Chọn gói dịch vụ --') }}</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}"
                                    data-days="{{ $package->days }}"
                                    @if($currentPackage?->package_id == $package->id) selected @endif>
                                {{ $package->name }}
                                ({{ $package->days }} ngày - {{ number_format($package->price) }} VNĐ)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Ngày Bắt Đầu') }} <span class="text-danger">*</span></label>
                    <input type="date"
                           class="form-control"
                           name="start_date"
                           id="startDate"
                           value="{{ $currentPackage?->start_date ? Carbon::parse($currentPackage->start_date)->format('Y-m-d') : date('Y-m-d') }}"
                           required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Ngày Kết Thúc') }}</label>
                    <input type="date"
                           class="form-control"
                           name="end_date"
                           id="endDate"
                           value="{{ $currentPackage?->end_date ? Carbon::parse($currentPackage->end_date)->format('Y-m-d') : '' }}"
                           required>
                </div>
            </div>

        </div>
    </div>
</div>
