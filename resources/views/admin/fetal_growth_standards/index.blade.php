@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Tiêu chuẩn thai nhi theo tuần')"
                                     :subtitle="__('Bảng quy chuẩn chiều dài (cm) và cân nặng (g) thai nhi theo tuần (Vinmec / WHO)')"
                                     icon="ti ti-heart-rate-monitor"
                                     :addRoute="route('admin.fetal-growth-standard.create')"
                                     :addText="__('Thêm mới')">
                    <x-slot name="actions">
                        <a href="{{ route('admin.fetal-growth-standard.downloadTemplate') }}"
                           class="btn btn-outline-success rounded-pill px-3 shadow-sm d-inline-flex align-items-center me-2">
                            <i class="ti ti-file-spreadsheet me-1 fs-5"></i> {{ __('Tải template mẫu') }}
                        </a>
                        <button type="button"
                                class="btn btn-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center"
                                data-bs-toggle="modal"
                                data-bs-target="#importExcelModal">
                            <i class="ti ti-cloud-upload me-1 fs-5"></i> {{ __('Import Excel') }}
                        </button>
                    </x-slot>
                </x-admin.page-header>
                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.fetal-growth-standard.multiple')" type="post" :validate="true">
                        <div class="table-responsive position-relative">
                            <x-admin.partials.toggle-column-datatable />
                            @isset($actionMultiple)
                                <x-admin.partials.select-action-multiple :actionMultiple="$actionMultiple" />
                            @endisset
                            {{ $dataTable->table(['class' => 'table table-bordered'], true) }}
                        </div>
                    </x-form>
                </div>

                <!-- Modal Import Excel & Template Mẫu -->
                <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header bg-light border-bottom py-3 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="ti ti-file-spreadsheet fs-2"></i>
                                    </div>
                                    <div>
                                        <h5 class="modal-title fw-bold text-dark mb-0" id="importExcelModalLabel">{{ __('Import tiêu chuẩn phát triển thai nhi') }}</h5>
                                        <small class="text-muted">{{ __('Nhập dữ liệu hàng loạt từ file Excel chuẩn hóa theo tuần thai') }}</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <x-form id="importExcelForm" :action="route('admin.fetal-growth-standard.import')" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body p-4">
                                    <!-- Khung tải template mẫu chuẩn -->
                                    <div class="card border border-success border-opacity-25 bg-success bg-opacity-10 rounded-3 mb-4 shadow-none">
                                        <div class="card-body p-3">
                                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-3 p-2 bg-success text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px; min-width: 46px;">
                                                        <i class="ti ti-file-type-xls fs-2"></i>
                                                    </div>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                            <h6 class="fw-bold mb-0 text-dark">mau_nhap_tieu_chuan_thai_nhi.xlsx</h6>
                                                            <span class="badge bg-success text-white px-2 py-1" style="font-size: 11px;">Mẫu chuẩn Vinmec/WHO</span>
                                                            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">35 tuần thai</span>
                                                        </div>
                                                        <p class="text-muted mb-0 small">
                                                            File đã được định dạng chuẩn sẵn 35 mốc tuần (tuần 8 - 42). Bạn có thể tải về để tham khảo hoặc chỉnh sửa số liệu trước khi tải lên.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="text-md-end">
                                                    <a href="{{ route('admin.fetal-growth-standard.downloadTemplate') }}" class="btn btn-success rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-nowrap fw-semibold">
                                                        <i class="ti ti-download me-1 fs-5"></i> {{ __('Tải template mẫu') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bảng quy cách cột dữ liệu -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center mb-2">
                                            <i class="ti ti-table me-2 text-primary"></i> {{ __('Quy cách các cột trong file Excel') }}
                                        </label>
                                        <div class="table-responsive border rounded-3 bg-white">
                                            <table class="table table-sm table-bordered text-center align-middle mb-0" style="font-size: 12.5px;">
                                                <thead class="table-light text-secondary">
                                                    <tr>
                                                        <th class="py-2" style="width: 20%;">Cột A (Bắt buộc)</th>
                                                        <th class="py-2" style="width: 25%;">Cột B (Bắt buộc)</th>
                                                        <th class="py-2" style="width: 25%;">Cột C (Bắt buộc)</th>
                                                        <th class="py-2" style="width: 30%;">Cột D (Tùy chọn)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="fw-semibold">
                                                        <td class="text-primary py-2">Tuần (số nguyên)</td>
                                                        <td class="text-dark py-2">Chiều dài (cm)</td>
                                                        <td class="text-dark py-2">Cân nặng (g)</td>
                                                        <td class="text-muted py-2">Chu vi đầu (cm)</td>
                                                    </tr>
                                                    <tr class="text-muted bg-light bg-opacity-50" style="font-size: 11.5px;">
                                                        <td>VD: 8, 9, 20...</td>
                                                        <td>VD: 1.6 hoặc 25.6</td>
                                                        <td>VD: 1.0 hoặc 330.0</td>
                                                        <td>Để trống nếu chưa có</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Khu vực tải lên file -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-2">
                                            <span><i class="ti ti-cloud-upload me-2 text-primary"></i> {{ __('Chọn file Excel để tải lên') }}</span>
                                            <small class="text-muted">Định dạng hỗ trợ: .xlsx, .xls, .csv</small>
                                        </label>

                                        <div class="dropzone-box border-2 rounded-3 p-4 text-center position-relative" id="dropZoneContainer"
                                             style="border: 2px dashed #cbd5e1; background-color: #f8fafc; cursor: pointer; transition: all 0.2s ease;">
                                            <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required style="cursor: pointer; z-index: 5;">

                                            <div id="dropZonePrompt">
                                                <div class="mb-2">
                                                    <i class="ti ti-cloud-upload text-primary" style="font-size: 42px;"></i>
                                                </div>
                                                <div class="fw-bold text-dark mb-1">
                                                    {{ __('Kéo và thả file Excel vào đây, hoặc ') }}
                                                    <span class="text-primary text-decoration-underline">{{ __('chọn file từ máy tính') }}</span>
                                                </div>
                                                <small class="text-muted">{{ __('Chấp nhận file định dạng .xlsx, .xls, .csv (Tối đa 10MB)') }}</small>
                                            </div>

                                            <div id="dropZoneFileInfo" class="d-none">
                                                <div class="d-inline-flex align-items-center bg-white border rounded-pill px-3 py-2 shadow-sm">
                                                    <i class="ti ti-file-spreadsheet text-success fs-2 me-2"></i>
                                                    <div class="text-start me-3">
                                                        <div class="fw-bold text-dark small" id="selectedFileName">file.xlsx</div>
                                                        <small class="text-muted" id="selectedFileSize">0 KB</small>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1 d-inline-flex align-items-center justify-content-center" id="btnRemoveFile" style="width: 26px; height: 26px; z-index: 10;" title="Chọn lại file khác">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info py-2 px-3 mb-0 d-flex align-items-center rounded-3" style="font-size: 12.5px;">
                                        <i class="ti ti-info-circle fs-5 me-2 flex-shrink-0 text-info"></i>
                                        <div>
                                            {{ __('Hệ thống sẽ tự động cập nhật lại các chỉ số nếu tuần thai đã tồn tại trong danh sách.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light py-2 px-4 border-top">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center shadow-sm" id="btnSubmitImport">
                                        <i class="ti ti-upload me-1"></i> {{ __('Bắt đầu Import') }}
                                    </button>
                                </div>
                            </x-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('excel_file');
            const dropZone = document.getElementById('dropZoneContainer');
            const promptBox = document.getElementById('dropZonePrompt');
            const infoBox = document.getElementById('dropZoneFileInfo');
            const fileNameEl = document.getElementById('selectedFileName');
            const fileSizeEl = document.getElementById('selectedFileSize');
            const btnRemove = document.getElementById('btnRemoveFile');
            const importForm = document.getElementById('importExcelForm');
            const btnSubmit = document.getElementById('btnSubmitImport');

            if (!fileInput || !dropZone) return;

            function formatBytes(bytes, decimals = 2) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }

            function updateFileDisplay(file) {
                if (file) {
                    fileNameEl.textContent = file.name;
                    fileSizeEl.textContent = formatBytes(file.size);
                    promptBox.classList.add('d-none');
                    infoBox.classList.remove('d-none');
                    dropZone.style.borderColor = '#198754';
                    dropZone.style.backgroundColor = '#f0fdf4';
                } else {
                    fileInput.value = '';
                    promptBox.classList.remove('d-none');
                    infoBox.classList.add('d-none');
                    dropZone.style.borderColor = '#cbd5e1';
                    dropZone.style.backgroundColor = '#f8fafc';
                }
            }

            fileInput.addEventListener('change', function (e) {
                if (e.target.files && e.target.files.length > 0) {
                    updateFileDisplay(e.target.files[0]);
                }
            });

            if (btnRemove) {
                btnRemove.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    updateFileDisplay(null);
                });
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.style.borderColor = '#0d6efd';
                    dropZone.style.backgroundColor = '#eef2ff';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!fileInput.files || fileInput.files.length === 0) {
                        dropZone.style.borderColor = '#cbd5e1';
                        dropZone.style.backgroundColor = '#f8fafc';
                    } else {
                        dropZone.style.borderColor = '#198754';
                        dropZone.style.backgroundColor = '#f0fdf4';
                    }
                }, false);
            });

            dropZone.addEventListener('drop', function (e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    fileInput.files = files;
                    updateFileDisplay(files[0]);
                }
            });

            if (importForm && btnSubmit) {
                importForm.addEventListener('submit', function () {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang Import...';
                });
            }
        });
    </script>
@endpush
