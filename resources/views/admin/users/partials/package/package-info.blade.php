@php
    use Carbon\Carbon;
    $currentPackage = $user->userPackages()->where('status', 'active')->first();
    $isExpired = false;
    $daysLeft = 0;
    if ($currentPackage && $currentPackage->end_date) {
        $endDate = Carbon::parse($currentPackage->end_date);
        $daysLeft = (int) now()->diffInDays($endDate, false);
        if ($daysLeft < 0) {
            $isExpired = true;
        }
    }
@endphp

<div class="row g-4">
    <!-- Current Package Banner -->
    <div class="col-12">
        <div class="current-package-banner">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-white bg-opacity-10 rounded-3 text-warning fs-1">
                        <i class="ti ti-crown"></i>
                    </div>
                    <div>
                        <div class="text-white-50 fs-12 fw-bold text-uppercase">{{ __('Gói Dịch Vụ Đang Kích Hoạt') }}</div>
                        <h3 class="mb-1 text-white fw-bold">
                            {{ $currentPackage?->package?->name ?? __('Chưa có gói hoạt động') }}
                        </h3>
                        @if($currentPackage)
                            <div class="text-white-50 fs-13">
                                <span><i class="ti ti-calendar me-1"></i> {{ format_date($currentPackage->start_date, 'd/m/Y') }} - {{ format_date($currentPackage->end_date, 'd/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    @if($currentPackage)
                        @if(!$isExpired)
                            <span class="package-badge-status badge-active">
                                <i class="ti ti-circle-check me-1"></i> {{ __('Đang hoạt động (Còn ') . max(0, $daysLeft) . __(' ngày)') }}
                            </span>
                        @else
                            <span class="package-badge-status badge-expired">
                                <i class="ti ti-alert-triangle me-1"></i> {{ __('Đã hết hạn') }}
                            </span>
                        @endif
                    @else
                        <span class="package-badge-status bg-secondary text-white">
                            {{ __('Chưa kích hoạt gói') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Package Upgrade / Configuration Form -->
    <div class="col-12">
        <div class="card border rounded-3 p-4 bg-light-subtle">
            <h5 class="mb-3 fw-bold text-dark d-flex align-items-center">
                <i class="ti ti-edit-circle text-primary me-2 fs-4"></i>
                {{ __('Thay Đổi / Gia Hạn Gói Dịch Vụ') }}
            </h5>

            <div class="row g-3">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="ti ti-package text-primary me-1"></i>
                            {{ __('Chọn Gói Dịch Vụ') }}:
                        </label>
                        <select class="form-select" name="package_id" id="packageSelect">
                            <option value="">{{ __('-- Chọn gói dịch vụ nếu muốn đổi --') }}</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}"
                                        data-days="{{ $package->days }}"
                                        @if($currentPackage?->package_id == $package->id) selected @endif>
                                    {{ $package->name }} ({{ $package->days }} {{ __('ngày') }} - {{ number_format($package->price) }} VNĐ) {{ $package->status == \App\Enums\Package\PackageStatus::Draft ? '- [Bản Nháp]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="ti ti-calendar-event text-primary me-1"></i>
                            {{ __('Ngày Bắt Đầu') }}:
                        </label>
                        <input type="date"
                               class="form-control"
                               name="start_date"
                               id="startDate"
                               value="{{ $currentPackage?->start_date ? Carbon::parse($currentPackage->start_date)->format('Y-m-d') : date('Y-m-d') }}">
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="ti ti-calendar-due text-primary me-1"></i>
                            {{ __('Ngày Kết Thúc') }}:
                        </label>
                        <input type="date"
                               class="form-control"
                               name="end_date"
                               id="endDate"
                               value="{{ $currentPackage?->end_date ? Carbon::parse($currentPackage->end_date)->format('Y-m-d') : '' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom-js')
    <script>
        $(document).ready(function() {
            $('#packageSelect').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const days = parseInt(selectedOption.data('days'));
                const startDateVal = $('#startDate').val();

                if (days && startDateVal) {
                    const start = new Date(startDateVal);
                    start.setDate(start.getDate() + days);
                    const yyyy = start.getFullYear();
                    const mm = String(start.getMonth() + 1).padStart(2, '0');
                    const dd = String(start.getDate()).padStart(2, '0');
                    $('#endDate').val(`${yyyy}-${mm}-${dd}`);
                }
            });

            $('#startDate').on('change', function() {
                const selectedOption = $('#packageSelect').find(':selected');
                const days = parseInt(selectedOption.data('days'));
                const startDateVal = $(this).val();

                if (days && startDateVal) {
                    const start = new Date(startDateVal);
                    start.setDate(start.getDate() + days);
                    const yyyy = start.getFullYear();
                    const mm = String(start.getMonth() + 1).padStart(2, '0');
                    const dd = String(start.getDate()).padStart(2, '0');
                    $('#endDate').val(`${yyyy}-${mm}-${dd}`);
                }
            });
        });
    </script>
@endpush
