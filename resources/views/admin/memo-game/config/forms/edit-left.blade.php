<div class="col-12 col-md-8 col-xl-9">
    <div class="card custom-shadow mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('Thông tin Nhóm tuổi & Quy chuẩn Lưới thẻ') }}</h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Tên cấu hình / Nhóm độ tuổi') }}: <span class="text-danger">*</span></label>
                        <x-input type="text" name="name" :value="$response->name" :required="true" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Độ tuổi tối thiểu (Min Age)') }}: <span class="text-danger">*</span></label>
                        <x-input type="number" name="min_age" :value="$response->min_age" min="1" max="25" :required="true" />
                        <small class="text-muted fs-12">{{ __('Độ tuổi nhỏ nhất được áp dụng cấu hình này (tuổi)') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Độ tuổi tối đa (Max Age)') }}: <span class="text-danger">*</span></label>
                        <x-input type="number" name="max_age" :value="$response->max_age" min="1" max="25" :required="true" />
                        <small class="text-muted fs-12">{{ __('Độ tuổi lớn nhất được áp dụng cấu hình này (tuổi)') }}</small>
                    </div>
                </div>

                <!-- Grid dimensions -->
                <div class="col-12">
                    <div class="card bg-light border p-3">
                        <h5 class="fw-bold mb-3 text-primary"><i class="ti ti-grid-dots me-1"></i> {{ __('Kích thước Lưới thẻ (Hàng x Cột)') }}</h5>
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">{{ __('Số hàng (Rows)') }}: <span class="text-danger">*</span></label>
                                <input type="number" name="rows" id="inputRows" class="form-control" value="{{ $response->rows }}" min="2" max="10" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">{{ __('Số cột (Columns)') }}: <span class="text-danger">*</span></label>
                                <input type="number" name="columns" id="inputCols" class="form-control" value="{{ $response->columns }}" min="2" max="10" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-2 bg-white rounded border text-center">
                                    <div class="fs-12 text-muted">{{ __('Tổng số thẻ / Cặp') }}:</div>
                                    <div class="fw-bold fs-16" id="gridPreview">
                                        <span class="text-primary">{{ $response->total_cards }}</span> thẻ (<span class="text-success">{{ $response->pairs_count }}</span> cặp)
                                    </div>
                                    <div id="gridWarning" class="text-danger fs-11 d-none mt-1">
                                        <i class="ti ti-alert-triangle"></i> Số thẻ phải là số chẵn!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Game Rules -->
                <div class="col-12 col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Thời lượng làm bài (giây)') }}: <span class="text-danger">*</span></label>
                        <x-input type="number" name="total_duration" :value="$response->total_duration" min="30" max="600" :required="true" />
                        <small class="text-muted fs-12">{{ __('Ví dụ: 180s = 03 phút/bài test') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Số lượt game / bài test') }}: <span class="text-danger">*</span></label>
                        <x-input type="number" name="total_rounds" :value="$response->total_rounds" min="1" max="10" :required="true" />
                        <small class="text-muted fs-12">{{ __('Ví dụ: 03 lượt game liên tiếp') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Thời gian xem trước (giây)') }}:</label>
                        <x-input type="number" name="peek_time" :value="$response->peek_time" min="0" max="30" />
                        <small class="text-muted fs-12">{{ __('Mở thẻ cho bé ghi nhớ trước khi úp') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputRows = document.getElementById('inputRows');
    const inputCols = document.getElementById('inputCols');
    const preview = document.getElementById('gridPreview');
    const warning = document.getElementById('gridWarning');

    function updateGrid() {
        const r = parseInt(inputRows?.value || 0);
        const c = parseInt(inputCols?.value || 0);
        const total = r * c;
        const pairs = Math.floor(total / 2);

        if (preview) {
            preview.innerHTML = `<span class="text-primary">${total}</span> thẻ (<span class="text-success">${pairs}</span> cặp)`;
        }

        if (total % 2 !== 0) {
            warning?.classList.remove('d-none');
        } else {
            warning?.classList.add('d-none');
        }
    }

    inputRows?.addEventListener('input', updateGrid);
    inputCols?.addEventListener('input', updateGrid);
});
</script>
@endpush
