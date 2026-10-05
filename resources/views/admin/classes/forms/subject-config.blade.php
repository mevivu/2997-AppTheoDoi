{{-- Cấu hình cách đánh giá từng môn trong lớp (class_subject pivot). Dữ liệu dựng bằng JS ở scripts.blade.php --}}
<div class="col-12 mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <label class="form-label fw-bold mb-0">
            <i class="ti ti-adjustments-horizontal text-primary me-1"></i>
            {{ __('Cấu hình đánh giá môn học') }}
        </label>
        <span class="badge bg-blue-lt" id="subject-config-level-badge"></span>
    </div>

    <div class="table-responsive border rounded-3">
        <table class="table table-vcenter card-table mb-0" id="subject-config-table">
            <thead>
            <tr>
                <th class="w-1">#</th>
                <th>{{ __('Môn học') }}</th>
                <th style="min-width: 240px;">{{ __('Cách đánh giá') }}</th>
                <th class="text-center" style="width: 110px;">{{ __('Bắt buộc') }}</th>
                <th style="width: 110px;">{{ __('Thứ tự') }}</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div id="subject-config-empty" class="text-center text-muted small py-4">
            <i class="ti ti-arrow-up me-1"></i>{{ __('Chọn môn ở ô phía trên để cấu hình cách đánh giá.') }}
        </div>
    </div>

    <div class="form-hint mt-2">
        {{ __('Tiểu học: Mức đạt được (T/H/C) hoặc Mức đạt được & Điểm KTĐK. THCS/THPT: Điểm số hoặc Đạt (Đ) / Chưa đạt (CĐ).') }}
        {{ __('Môn "Tự chọn" không bắt buộc nhập khi tính xếp loại.') }}
    </div>
</div>
