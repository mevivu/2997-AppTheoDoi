<div class="col-12 col-md-8 col-xl-9">
    <!-- Card 1: Thông tin cơ bản & Nhóm tuổi áp dụng -->
    <div class="card custom-shadow mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0 d-flex align-items-center">
                <i class="ti ti-users text-primary me-2 fs-18"></i>
                {{ __('Thông tin Nhóm tuổi & Tên cấu hình') }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label fw-bold">{{ __('Tên cấu hình / Nhóm độ tuổi') }}: <span class="text-danger">*</span></label>
                        <x-input type="text" name="name" :value="old('name')" :required="true" placeholder="{{ __('Ví dụ: Khởi động (3 - 5 tuổi) - Lưới 2x3') }}" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label fw-bold">{{ __('Độ tuổi tối thiểu (Min Age)') }}: <span class="text-danger">*</span></label>
                        <input type="number" name="min_age" id="inputMinAge" class="form-control" value="{{ old('min_age', 3) }}" min="1" max="25" required>
                        <small class="text-muted fs-12">{{ __('Độ tuổi nhỏ nhất áp dụng cấu hình này (tuổi)') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label fw-bold">{{ __('Độ tuổi tối đa (Max Age)') }}: <span class="text-danger">*</span></label>
                        <input type="number" name="max_age" id="inputMaxAge" class="form-control" value="{{ old('max_age', 5) }}" min="1" max="25" required>
                        <small class="text-muted fs-12">{{ __('Độ tuổi lớn nhất áp dụng cấu hình này (tuổi)') }}</small>
                    </div>
                </div>

                <div class="col-12">
                    <div class="alert alert-info py-2 px-3 mb-0 d-flex align-items-center rounded-3 fs-13">
                        <i class="ti ti-info-circle fs-18 me-2 text-info"></i>
                        <span>{{ __('Phạm vi áp dụng:') }} <strong id="ageRangeBadge" class="text-dark">{{ __('Trẻ từ') }} {{ old('min_age', 3) }} {{ __('đến') }} {{ old('max_age', 5) }} {{ __('tuổi') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Kích thước Lưới thẻ (Hàng x Cột & Presets) -->
    <div class="card custom-shadow mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0 d-flex align-items-center">
                <i class="ti ti-grid-dots text-success me-2 fs-18"></i>
                {{ __('Kích thước Lưới thẻ (Hàng x Cột)') }}
            </h4>
        </div>
        <div class="card-body">
            <!-- Preset gợi ý theo lứa tuổi -->
            <div class="mb-3 pb-3 border-bottom">
                <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">
                    <i class="ti ti-sparkles text-warning me-1"></i> {{ __('Gợi ý kích thước lưới chuẩn theo lứa tuổi (Bấm để chọn nhanh):') }}
                </label>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-grid-preset px-2.5 py-1" data-rows="2" data-cols="3">
                        <i class="ti ti-check d-none me-1 check-icon"></i> <strong>2 × 3</strong> (6 thẻ • 3 cặp) - Khởi động (3-5t)
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-grid-preset px-2.5 py-1" data-rows="3" data-cols="4">
                        <i class="ti ti-check d-none me-1 check-icon"></i> <strong>3 × 4</strong> (12 thẻ • 6 cặp) - Cơ bản (6-8t)
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-grid-preset px-2.5 py-1" data-rows="4" data-cols="4">
                        <i class="ti ti-check d-none me-1 check-icon"></i> <strong>4 × 4</strong> (16 thẻ • 8 cặp) - Nâng cao (9-11t)
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-grid-preset px-2.5 py-1" data-rows="4" data-cols="5">
                        <i class="ti ti-check d-none me-1 check-icon"></i> <strong>4 × 5</strong> (20 thẻ • 10 cặp) - Thử thách (12+t)
                    </button>
                </div>
            </div>

            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold">{{ __('Số hàng (Rows)') }}: <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-arrows-vertical fs-14"></i></span>
                        <input type="number" name="rows" id="inputRows" class="form-control fw-bold" value="{{ old('rows', 2) }}" min="2" max="10" required>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold">{{ __('Số cột (Columns)') }}: <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-arrows-horizontal fs-14"></i></span>
                        <input type="number" name="columns" id="inputCols" class="form-control fw-bold" value="{{ old('columns', 3) }}" min="2" max="10" required>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-12 text-muted fw-semibold">{{ __('Tổng thẻ & Số cặp hình:') }}</span>
                            <span class="fw-bold fs-15" id="gridPreview">
                                <span class="text-primary">6</span> thẻ (<span class="text-success">3</span> cặp)
                            </span>
                        </div>
                        <!-- Mini visual grid preview -->
                        <div class="d-flex justify-content-center">
                            <div id="visualGridContainer" class="p-2 bg-white rounded border d-inline-block shadow-xs" style="min-height: 48px; min-width: 100px;">
                                <!-- Rendered dynamically by JS -->
                            </div>
                        </div>
                        <div id="gridWarning" class="alert alert-danger py-1 px-2 fs-12 mt-2 mb-0 d-none text-center">
                            <i class="ti ti-alert-triangle me-1"></i> {{ __('Số thẻ phải là số chẵn để tạo thành các cặp trùng nhau!') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Quy chuẩn Thời gian & Ván chơi (Rõ ràng 100%) -->
    <div class="card custom-shadow mb-4">
        <div class="card-header bg-light-subtle">
            <h4 class="card-title mb-0 d-flex align-items-center text-dark">
                <i class="ti ti-clock-play text-warning me-2 fs-18"></i>
                {{ __('Quy chuẩn Thời gian & Số ván chơi (Rounds)') }}
            </h4>
        </div>
        <div class="card-body">
            <!-- 1. Thời lượng 1 ván -->
            <div class="mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <label class="form-label fw-bold mb-0 fs-14">
                        <i class="ti ti-hourglass-low text-primary me-1"></i>
                        {{ __('Thời gian cho MỖI ván chơi (1 game):') }} <span class="text-danger">*</span>
                    </label>
                    <span id="durationConvertedBadge" class="badge bg-azure-lt px-2 py-1 fs-12 fw-semibold">
                        3 phút 00s / ván
                    </span>
                </div>

                <!-- Presets chọn nhanh thời gian -->
                <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                    <span class="fs-12 text-muted align-self-center me-1">{{ __('Chọn nhanh:') }}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-duration-preset px-2.5 py-0.5 fs-12" data-seconds="60">1 phút (60s)</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-duration-preset px-2.5 py-0.5 fs-12" data-seconds="120">2 phút (120s)</button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-duration-preset px-2.5 py-0.5 fs-12" data-seconds="180">⭐ 3 phút (180s - Chuẩn)</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-duration-preset px-2.5 py-0.5 fs-12" data-seconds="240">4 phút (240s)</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-duration-preset px-2.5 py-0.5 fs-12" data-seconds="300">5 phút (300s)</button>
                </div>

                <div class="row align-items-center g-2">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fs-12">{{ __('Số giây:') }}</span>
                            <input type="number" name="total_duration" id="inputDuration" class="form-control fw-bold" value="{{ old('total_duration', 180) }}" min="30" max="600" required>
                            <span class="input-group-text bg-light">giây</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-8">
                        <small class="text-muted fs-12 d-block">
                            <i class="ti ti-info-circle me-1"></i> {{ __('Đây là thời gian đếm ngược của 1 ván lật thẻ. Hết giờ hoặc bé ghép xong sẽ chuyển sang ván kế tiếp.') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- 2. Số ván chơi -->
            <div class="mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <label class="form-label fw-bold mb-0 fs-14">
                        <i class="ti ti-repeat text-purple me-1"></i>
                        {{ __('Số ván chơi của bài test (Rounds):') }} <span class="text-danger">*</span>
                    </label>
                    <span id="roundsConvertedBadge" class="badge bg-purple-lt px-2 py-1 fs-12 fw-semibold">
                        {{ old('total_rounds', 3) }} ván liên tiếp
                    </span>
                </div>

                <!-- Presets chọn nhanh số ván -->
                <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                    <span class="fs-12 text-muted align-self-center me-1">{{ __('Chọn nhanh:') }}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-rounds-preset px-2.5 py-0.5 fs-12" data-rounds="1">1 ván</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-rounds-preset px-2.5 py-0.5 fs-12" data-rounds="2">2 ván</button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-rounds-preset px-2.5 py-0.5 fs-12" data-rounds="3">⭐ 3 ván (Chuẩn)</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-rounds-preset px-2.5 py-0.5 fs-12" data-rounds="4">4 ván</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-rounds-preset px-2.5 py-0.5 fs-12" data-rounds="5">5 ván</button>
                </div>

                <div class="row align-items-center g-2">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fs-12">{{ __('Số ván:') }}</span>
                            <input type="number" name="total_rounds" id="inputRounds" class="form-control fw-bold" value="{{ old('total_rounds', 3) }}" min="1" max="10" required>
                            <span class="input-group-text bg-light">giây</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-8">
                        <small class="text-muted fs-12 d-block">
                            <i class="ti ti-info-circle me-1"></i> {{ __('Mỗi ván là 1 chủ đề độc lập. Hoàn thành mỗi ván bé được tích lũy +1 điểm game.') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- 3. Thời gian xem trước (Peek time) -->
            <div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <label class="form-label fw-bold mb-0 fs-14">
                        <i class="ti ti-eye text-teal me-1"></i>
                        {{ __('Thời gian mở thẻ xem trước (Peek Time):') }}
                    </label>
                    <span id="peekConvertedBadge" class="badge bg-teal-lt px-2 py-1 fs-12 fw-semibold">
                        {{ old('peek_time', 3) }} giây quan sát
                    </span>
                </div>

                <!-- Presets chọn nhanh peek time -->
                <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                    <span class="fs-12 text-muted align-self-center me-1">{{ __('Chọn nhanh:') }}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-peek-preset px-2.5 py-0.5 fs-12" data-seconds="0">0s (Tắt)</button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill btn-peek-preset px-2.5 py-0.5 fs-12" data-seconds="3">⭐ 3s (Chuẩn)</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-peek-preset px-2.5 py-0.5 fs-12" data-seconds="5">5s</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill btn-peek-preset px-2.5 py-0.5 fs-12" data-seconds="10">10s</button>
                </div>

                <div class="row align-items-center g-2">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fs-12">{{ __('Số giây:') }}</span>
                            <input type="number" name="peek_time" id="inputPeekTime" class="form-control fw-bold" value="{{ old('peek_time', 3) }}" min="0" max="30">
                            <span class="input-group-text bg-light">giây</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-8">
                        <small class="text-muted fs-12 d-block">
                            <i class="ti ti-info-circle me-1"></i> {{ __('Mở ngửa toàn bộ thẻ để bé ghi nhớ trước khi thẻ úp lại và bắt đầu đếm ngược ván chơi.') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inputs
    const inputMinAge = document.getElementById('inputMinAge');
    const inputMaxAge = document.getElementById('inputMaxAge');
    const inputRows = document.getElementById('inputRows');
    const inputCols = document.getElementById('inputCols');
    const inputDuration = document.getElementById('inputDuration');
    const inputRounds = document.getElementById('inputRounds');
    const inputPeekTime = document.getElementById('inputPeekTime');

    // Badges & Previews
    const ageRangeBadge = document.getElementById('ageRangeBadge');
    const gridPreview = document.getElementById('gridPreview');
    const gridWarning = document.getElementById('gridWarning');
    const visualGridContainer = document.getElementById('visualGridContainer');
    const durationConvertedBadge = document.getElementById('durationConvertedBadge');
    const roundsConvertedBadge = document.getElementById('roundsConvertedBadge');
    const peekConvertedBadge = document.getElementById('peekConvertedBadge');

    // Simulation Card (Right column)
    const simAge = document.getElementById('simAge');
    const simGrid = document.getElementById('simGrid');
    const simDuration = document.getElementById('simDuration');
    const simRounds = document.getElementById('simRounds');
    const simTotalGame = document.getElementById('simTotalGame');
    const simGameMinutes = document.getElementById('simGameMinutes');
    const simTotalAssessment = document.getElementById('simTotalAssessment');

    function updateSimulation() {
        const minAge = parseInt(inputMinAge?.value || 1);
        const maxAge = parseInt(inputMaxAge?.value || minAge);
        const rows = parseInt(inputRows?.value || 2);
        const cols = parseInt(inputCols?.value || 2);
        const totalDuration = parseInt(inputDuration?.value || 180);
        const totalRounds = parseInt(inputRounds?.value || 3);
        const peekTime = parseInt(inputPeekTime?.value || 0);

        // 1. Age Range
        const ageText = `Trẻ từ ${minAge} đến ${maxAge} tuổi`;
        if (ageRangeBadge) ageRangeBadge.textContent = ageText;
        if (simAge) simAge.textContent = ageText;

        // 2. Grid & Cards
        const totalCards = rows * cols;
        const pairsCount = Math.floor(totalCards / 2);
        if (gridPreview) {
            gridPreview.innerHTML = `<span class="text-primary">${totalCards}</span> thẻ (<span class="text-success">${pairsCount}</span> cặp)`;
        }
        if (simGrid) {
            simGrid.textContent = `${rows} hàng × ${cols} cột (${totalCards} thẻ • ${pairsCount} cặp)`;
        }

        if (totalCards % 2 !== 0) {
            gridWarning?.classList.remove('d-none');
        } else {
            gridWarning?.classList.add('d-none');
        }

        // Draw Mini Visual Grid
        if (visualGridContainer) {
            visualGridContainer.innerHTML = '';
            visualGridContainer.style.display = 'grid';
            visualGridContainer.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
            visualGridContainer.style.gap = '4px';
            visualGridContainer.style.maxWidth = '180px';
            visualGridContainer.style.margin = '0 auto';

            for (let i = 0; i < totalCards; i++) {
                const card = document.createElement('div');
                card.style.width = '24px';
                card.style.height = '24px';
                card.style.borderRadius = '5px';
                card.style.backgroundColor = '#E0F2FE';
                card.style.border = '1.5px solid #38BDF8';
                card.style.display = 'flex';
                card.style.alignItems = 'center';
                card.style.justifyContent = 'center';
                card.style.fontSize = '10px';
                card.style.color = '#0284C7';
                card.innerHTML = '🎴';
                visualGridContainer.appendChild(card);
            }
        }

        // 3. Duration of 1 round
        const durationMin = Math.floor(totalDuration / 60);
        const durationSec = totalDuration % 60;
        let durationFormatted = '';
        if (durationMin > 0) durationFormatted += `${durationMin} phút`;
        if (durationSec > 0) durationFormatted += ` ${durationSec}s`;
        if (durationFormatted === '') durationFormatted = '0s';

        if (durationConvertedBadge) {
            durationConvertedBadge.textContent = `${durationFormatted} / ván`;
        }
        if (simDuration) {
            simDuration.textContent = `${durationFormatted} (${totalDuration}s) / ván`;
        }

        // 4. Rounds
        if (roundsConvertedBadge) {
            roundsConvertedBadge.textContent = `${totalRounds} ván liên tiếp`;
        }
        if (simRounds) {
            simRounds.textContent = `${totalRounds} ván chơi`;
        }

        // 5. Total Game Duration & Total Assessment Duration
        const totalGameSeconds = totalDuration * totalRounds;
        const totalGameMinutes = Math.round(totalGameSeconds / 60);
        const totalAssessmentMinutes = 15 + totalGameMinutes;

        if (simTotalGame) {
            simTotalGame.textContent = `${totalRounds} ván × ${durationFormatted} = ${totalGameMinutes} PHÚT`;
        }
        if (simGameMinutes) {
            simGameMinutes.textContent = totalGameMinutes;
        }
        if (simTotalAssessment) {
            simTotalAssessment.textContent = `${totalAssessmentMinutes} PHÚT`;
        }

        // 6. Peek Time Badge
        if (peekConvertedBadge) {
            peekConvertedBadge.textContent = peekTime > 0 ? `${peekTime} giây quan sát` : 'Không mở trước';
        }

        // Highlight active presets
        highlightActivePresets(rows, cols, totalDuration, totalRounds, peekTime);
    }

    function highlightActivePresets(rows, cols, duration, rounds, peek) {
        // Grid presets
        document.querySelectorAll('.btn-grid-preset').forEach(btn => {
            const r = parseInt(btn.dataset.rows);
            const c = parseInt(btn.dataset.cols);
            const check = btn.querySelector('.check-icon');
            if (r === rows && c === cols) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
                check?.classList.remove('d-none');
            } else {
                btn.classList.add('btn-outline-primary');
                btn.classList.remove('btn-primary');
                check?.classList.add('d-none');
            }
        });

        // Duration presets
        document.querySelectorAll('.btn-duration-preset').forEach(btn => {
            const sec = parseInt(btn.dataset.seconds);
            if (sec === duration) {
                btn.classList.remove('btn-outline-secondary', 'btn-outline-primary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-secondary');
            }
        });

        // Rounds presets
        document.querySelectorAll('.btn-rounds-preset').forEach(btn => {
            const rnd = parseInt(btn.dataset.rounds);
            if (rnd === rounds) {
                btn.classList.remove('btn-outline-secondary', 'btn-outline-primary');
                btn.classList.add('btn-purple', 'btn-primary');
            } else {
                btn.classList.remove('btn-purple', 'btn-primary');
                btn.classList.add('btn-outline-secondary');
            }
        });

        // Peek presets
        document.querySelectorAll('.btn-peek-preset').forEach(btn => {
            const sec = parseInt(btn.dataset.seconds);
            if (sec === peek) {
                btn.classList.remove('btn-outline-secondary', 'btn-outline-primary');
                btn.classList.add('btn-teal', 'btn-primary');
            } else {
                btn.classList.remove('btn-teal', 'btn-primary');
                btn.classList.add('btn-outline-secondary');
            }
        });
    }

    // Attach Preset Event Listeners
    document.querySelectorAll('.btn-grid-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputRows) inputRows.value = this.dataset.rows;
            if (inputCols) inputCols.value = this.dataset.cols;
            updateSimulation();
        });
    });

    document.querySelectorAll('.btn-duration-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputDuration) inputDuration.value = this.dataset.seconds;
            updateSimulation();
        });
    });

    document.querySelectorAll('.btn-rounds-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputRounds) inputRounds.value = this.dataset.rounds;
            updateSimulation();
        });
    });

    document.querySelectorAll('.btn-peek-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputPeekTime) inputPeekTime.value = this.dataset.seconds;
            updateSimulation();
        });
    });

    // Inputs change listeners
    [inputMinAge, inputMaxAge, inputRows, inputCols, inputDuration, inputRounds, inputPeekTime].forEach(input => {
        input?.addEventListener('input', updateSimulation);
        input?.addEventListener('change', updateSimulation);
    });

    // Initial run
    updateSimulation();
});
</script>
@endpush
