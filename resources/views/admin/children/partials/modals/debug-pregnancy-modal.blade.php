@if(isset($pregnancyOverview) && isset($pregnancyOverview['debug']))
    @php
        $debug = $pregnancyOverview['debug'];
    @endphp
    <!-- Modal Debug Tính Toán Thai Kỳ -->
    <div class="modal fade" id="debugPregnancyModal" tabindex="-1" aria-labelledby="debugPregnancyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-teal text-white py-2 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-bug fs-20"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="debugPregnancyModalLabel">
                                {{ __('Debug Chi Tiết Tính Toán Thai Kỳ') }}
                            </h5>
                            <small class="opacity-75 fs-11">
                                {{ __('Bé') }}: <strong>{{ $children->fullname }}</strong> (ID: {{ $children->id }})
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs nav-fill mb-3" id="debugTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-2 fs-13 fw-bold" id="step-tab" data-bs-toggle="tab" data-bs-target="#tab-steps" type="button" role="tab">
                                <i class="ti ti-calculator text-teal me-1"></i> {{ __('1. Các Bước Tính Toán') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 fs-13 fw-bold" id="standard-tab" data-bs-toggle="tab" data-bs-target="#tab-standard" type="button" role="tab">
                                <i class="ti ti-database text-primary me-1"></i> {{ __('2. Chuẩn Tăng Trưởng DB') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 fs-13 fw-bold" id="json-tab" data-bs-toggle="tab" data-bs-target="#tab-json" type="button" role="tab">
                                <i class="ti ti-code text-warning me-1"></i> {{ __('3. Dữ Liệu Thô (JSON)') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="debugTabsContent">
                        <!-- TAB 1: CÁC BƯỚC TÍNH TOÁN -->
                        <div class="tab-pane fade show active" id="tab-steps" role="tabpanel">
                            <!-- Card: Biến đầu vào -->
                            <div class="card border mb-3 shadow-none bg-light-lt">
                                <div class="card-header py-2 bg-transparent border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark fs-12 text-uppercase">
                                        <i class="ti ti-login text-info me-1"></i> {{ __('Dữ liệu đầu vào thực tế (Input)') }}
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row g-2 fs-12">
                                        <div class="col-md-4">
                                            <span class="text-muted">{{ __('Ngày hệ thống (today):') }}</span>
                                            <strong class="d-block text-dark">{{ $debug['system_today'] }} ({{ $debug['system_today_iso'] }})</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="text-muted">{{ __('Ngày dự sinh (due_date):') }}</span>
                                            <strong class="d-block text-warning">{{ $debug['parsed_due_date'] }} ({{ $debug['parsed_due_date_iso'] }})</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="text-muted">{{ __('Trạng thái sinh (is_born):') }}</span>
                                            <span class="badge bg-secondary-lt">{{ $debug['is_born'] == 0 ? __('Chưa sinh (0)') : __('Đã sinh (1)') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline các bước -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle fs-12 mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 70px;" class="text-center">{{ __('Bước') }}</th>
                                            <th>{{ __('Mục tiêu & Công thức') }}</th>
                                            <th style="width: 200px;">{{ __('Thay số thực tế') }}</th>
                                            <th style="width: 150px;" class="text-center">{{ __('Kết quả') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Bước 1 -->
                                        <tr>
                                            <td class="text-center fw-bold text-teal">01</td>
                                            <td>
                                                <strong>{{ __('Số ngày còn lại đến ngày dự sinh') }}</strong>
                                                <div class="text-muted fs-11">
                                                    <code>$today->diffInDays($dueDate, false)</code>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $debug['system_today'] }} $\to$ {{ $debug['parsed_due_date'] }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-teal-lt fs-12 fw-bold">{{ $debug['days_remaining'] }} ngày</span>
                                            </td>
                                        </tr>

                                        <!-- Bước 2 -->
                                        <tr>
                                            <td class="text-center fw-bold text-teal">02</td>
                                            <td>
                                                <strong>{{ __('Tuổi thai tính theo ngày (Gestational Age)') }}</strong>
                                                <div class="text-muted fs-11">
                                                    {{ __('Quy ước y khoa thai kỳ chuẩn là 40 tuần (280 ngày tính từ ngày đầu kỳ kinh cuối LMP).') }}
                                                    <br>
                                                    <code>$gestationalAgeDays = 280 - $daysRemaining</code>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="text-dark">280 - {{ $debug['days_remaining'] }}</code>
                                            </td>
                                            <td class="text-center">
                                                <strong class="text-teal fs-13">{{ $debug['gestational_age_days'] }} ngày</strong>
                                            </td>
                                        </tr>

                                        <!-- Bước 3 -->
                                        <tr>
                                            <td class="text-center fw-bold text-teal">03</td>
                                            <td>
                                                <strong>{{ __('Quy đổi ra tuần tuổi & ngày lẻ') }}</strong>
                                                <div class="text-muted fs-11">
                                                    • {{ __('Tuần:') }} <code>intdiv($gestationalAgeDays, 7)</code>
                                                    <br>
                                                    • {{ __('Ngày lẻ:') }} <code>$gestationalAgeDays % 7</code>
                                                    <br>
                                                    • {{ __('Tiến độ:') }} <code>(gestationalAge / 280) * 100</code>
                                                </div>
                                            </td>
                                            <td>
                                                <div><code>{{ $debug['formula_week'] }}</code></div>
                                                <div><code>{{ $debug['formula_extra_days'] }}</code></div>
                                                <div class="mt-1 progress progress-xs">
                                                    <div class="progress-bar bg-teal" style="width: {{ $debug['progress_percent'] }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $debug['progress_percent'] }}% {{ __('thai kỳ') }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-teal text-white fs-12">{{ $pregnancyOverview['weekDisplay'] }}</span>
                                            </td>
                                        </tr>

                                        <!-- Bước 4 -->
                                        <tr>
                                            <td class="text-center fw-bold text-teal">04</td>
                                            <td>
                                                <strong>{{ __('Tra cứu chuẩn tăng trưởng thai nhi') }}</strong>
                                                <div class="text-muted fs-11">
                                                    {{ __('Tìm bản ghi tuần trong bảng') }} <code>fetal_growth_standards</code>:
                                                    <br>
                                                    <span class="badge bg-blue-lt">Tuần tra cứu: {{ $debug['lookup_week'] }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted text-break">
                                                    <code>{{ $debug['sql_query'] }}</code>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                @if($pregnancyOverview['standard'])
                                                    <div class="text-dark fw-bold">Dài: {{ $pregnancyOverview['standard']['length'] ?? '--' }} cm</div>
                                                    <div class="text-dark fw-bold">Nặng: {{ $pregnancyOverview['standard']['weight'] ?? '--' }} g</div>
                                                @else
                                                    <span class="badge bg-danger-lt">{{ __('Không tìm thấy') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: CHUẨN TĂNG TRƯỞNG DATABASE -->
                        <div class="tab-pane fade" id="tab-standard" role="tabpanel">
                            @if(isset($debug['standard_record']) && $debug['standard_record'])
                                @php $std = $debug['standard_record']; @endphp
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm fs-12 mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 180px;">{{ __('Trường Database') }}</th>
                                                <th>{{ __('Giá trị tra cứu được') }}</th>
                                                <th>{{ __('Ghi chú / Ý nghĩa') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">ID bản ghi</td>
                                                <td><span class="badge bg-dark text-white">#{{ $std['id'] }}</span></td>
                                                <td class="text-muted">{{ __('Khóa chính bảng fetal_growth_standards') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">week (Tuần)</td>
                                                <td><strong class="text-teal fs-13">{{ $std['week'] }}</strong></td>
                                                <td class="text-muted">{{ __('Khớp với tuần thai lookup_week') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">length (Chiều dài)</td>
                                                <td><strong class="text-primary fs-13">{{ $std['length'] !== null ? $std['length'] . ' cm' : '--' }}</strong></td>
                                                <td class="text-muted">{{ __('Chiều dài thai nhi chuẩn (cm)') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">weight (Cân nặng)</td>
                                                <td><strong class="text-success fs-13">{{ $std['weight'] !== null ? $std['weight'] . ' g' : '--' }}</strong></td>
                                                <td class="text-muted">{{ __('Cân nặng thai nhi chuẩn (gram)') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">head_circumference</td>
                                                <td>{{ $std['head_circumference'] !== null ? $std['head_circumference'] . ' cm' : 'null' }}</td>
                                                <td class="text-muted">{{ __('Chu vi vòng đầu (cm)') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">status</td>
                                                <td>
                                                    <span class="badge bg-success-lt">{{ $std['status'] == 1 ? 'Active (1)' : 'Inactive' }}</span>
                                                </td>
                                                <td class="text-muted">{{ __('Chỉ query bản ghi có status = 1 (Active)') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">description</td>
                                                <td colspan="2">
                                                    <div class="p-2 rounded bg-light border text-muted fs-11" style="max-height: 120px; overflow-y: auto;">
                                                        {{ $std['description'] ?: __('(Không có mô tả)') }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0 fs-12">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    {{ __('Chưa có dữ liệu tiêu chuẩn cho tuần này trong bảng fetal_growth_standards.') }}
                                </div>
                            @endif
                        </div>

                        <!-- TAB 3: RAW JSON -->
                        <div class="tab-pane fade" id="tab-json" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fs-12 text-muted">{{ __('Dữ liệu đối tượng Debug dạng JSON:') }}</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fs-11" id="btnCopyPregnancyJson" onclick="copyPregnancyDebugJson()">
                                    <i class="ti ti-copy me-1"></i> <span id="btnCopyPregnancyJsonText">{{ __('Sao chép JSON') }}</span>
                                </button>
                            </div>
                            <pre class="bg-dark text-light p-3 rounded-2 fs-11 mb-0 border" style="max-height: 360px; overflow-y: auto;"><code id="pregnancyDebugJsonCode">{{ json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        {{ __('Đóng') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyPregnancyDebugJson() {
            var codeEl = document.getElementById('pregnancyDebugJsonCode');
            var btnTextEl = document.getElementById('btnCopyPregnancyJsonText');
            if (!codeEl) return;

            var text = codeEl.innerText || codeEl.textContent;
            navigator.clipboard.writeText(text).then(function() {
                if (btnTextEl) {
                    btnTextEl.innerText = 'Đã sao chép!';
                    setTimeout(function() {
                        btnTextEl.innerText = 'Sao chép JSON';
                    }, 2000);
                }
            }).catch(function(err) {
                console.error('Không thể copy: ', err);
            });
        }
    </script>
@endif
