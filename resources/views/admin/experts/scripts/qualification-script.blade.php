<script>
    document.addEventListener('DOMContentLoaded', function () {
        let qualificationIndex = 0;
        const container = document.getElementById('qualifications-container');
        const emptyAlert = document.getElementById('no-qualifications-alert');
        const btnAdd = document.getElementById('btn-add-qualification');

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function checkEmptyState() {
            if (!container || !emptyAlert) return;
            const items = container.querySelectorAll('.qualification-item');
            if (items.length === 0) {
                emptyAlert.style.display = 'block';
            } else {
                emptyAlert.style.display = 'none';
            }

            // Cập nhật lại số thứ tự hiển thị trên nhãn (Học vị #1, Học vị #2...)
            items.forEach((item, idx) => {
                const label = item.querySelector('.qualification-index-label');
                if (label) {
                    label.textContent = '#' + (idx + 1);
                }
            });
        }

        function addQualification(data = {}) {
            if (!container) return;

            const idx = qualificationIndex++;
            const degreeName = escapeHtml(data.degree_name || '');
            const specialization = escapeHtml(data.specialization || '');
            const institution = escapeHtml(data.institution || '');
            const graduationYear = escapeHtml(data.graduation_year || '');
            const sortOrder = data.sort_order !== undefined && data.sort_order !== null ? data.sort_order : idx;

            const itemHtml = `
                <div class="qualification-item p-3 p-md-4 position-relative shadow-xs" data-idx="${idx}">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white fw-bold px-2 py-1 fs-12">
                                <i class="ti ti-award me-1"></i>Học vị <span class="qualification-index-label">#1</span>
                            </span>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-qualification d-flex align-items-center gap-1 px-2 py-1 rounded-2 shadow-none" title="{{ __('Xóa văn bằng này') }}">
                            <i class="ti ti-trash fs-3"></i>
                            <span class="fs-12 fw-semibold">{{ __('Xóa') }}</span>
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fs-13 fw-bold text-dark mb-1">
                                <i class="ti ti-certificate text-primary me-1"></i>{{ __('Tên bằng cấp / Học vị') }}: <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="qualifications[${idx}][degree_name]" 
                                   class="form-control" 
                                   required 
                                   placeholder="{{ __('VD: Bác sĩ CKII, Thạc sĩ Y học, Tiến sĩ...') }}" 
                                   value="${degreeName}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fs-13 fw-bold text-dark mb-1">
                                <i class="ti ti-stethoscope text-cyan me-1"></i>{{ __('Chuyên ngành đào tạo') }}:
                            </label>
                            <input type="text" 
                                   name="qualifications[${idx}][specialization]" 
                                   class="form-control" 
                                   placeholder="{{ __('VD: Nhi sơ sinh, Dinh dưỡng, Tâm lý trẻ em...') }}" 
                                   value="${specialization}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fs-13 fw-bold text-dark mb-1">
                                <i class="ti ti-building text-secondary me-1"></i>{{ __('Cơ sở / Trường đào tạo') }}:
                            </label>
                            <input type="text" 
                                   name="qualifications[${idx}][institution]" 
                                   class="form-control" 
                                   placeholder="{{ __('VD: Đại học Y Hà Nội, Viện Dinh Dưỡng...') }}" 
                                   value="${institution}">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fs-13 fw-bold text-dark mb-1">
                                <i class="ti ti-calendar text-warning me-1"></i>{{ __('Năm tốt nghiệp') }}:
                            </label>
                            <input type="text" 
                                   name="qualifications[${idx}][graduation_year]" 
                                   class="form-control text-center" 
                                   placeholder="{{ __('VD: 2018') }}" 
                                   value="${graduationYear}">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fs-13 fw-bold text-dark mb-1">
                                <i class="ti ti-sort-ascending text-muted me-1"></i>{{ __('Thứ tự') }}:
                            </label>
                            <input type="number" 
                                   name="qualifications[${idx}][sort_order]" 
                                   class="form-control text-center" 
                                   value="${sortOrder}" 
                                   min="0">
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', itemHtml);
            checkEmptyState();
        }

        if (btnAdd) {
            btnAdd.addEventListener('click', function (e) {
                e.preventDefault();
                addQualification();
            });
        }

        if (container) {
            container.addEventListener('click', function (e) {
                const btnRemove = e.target.closest('.btn-remove-qualification');
                if (btnRemove) {
                    e.preventDefault();
                    const item = btnRemove.closest('.qualification-item');
                    if (item) {
                        item.remove();
                        checkEmptyState();
                    }
                }
            });
        }

        // Render dữ liệu ban đầu
        const rawInitialData = @json($initialQualifications ?? []);
        let initialList = [];
        if (Array.isArray(rawInitialData)) {
            initialList = rawInitialData;
        } else if (rawInitialData && typeof rawInitialData === 'object') {
            initialList = Object.values(rawInitialData);
        }

        if (initialList.length > 0) {
            initialList.forEach(item => addQualification(item));
        } else {
            checkEmptyState();
        }
    });
</script>
