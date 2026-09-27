<div class="col-12 col-md-9">
    <div class="card custom-shadow mb-3">
        <div class="card-header bg-light">
            <h3 class="card-title fw-bold text-dark mb-0">
                <i class="ti ti-folder text-primary me-2"></i>{{ __('Thông tin Danh mục bài học') }}
            </h3>
        </div>
        <div class="card-body">
            {{-- Nhóm độ tuổi --}}
            <div class="mb-4">
                <label class="form-label fw-bold text-dark fs-14">
                    {{ __('1. Nhóm độ tuổi') }}: <span class="text-danger">*</span>
                </label>
                <x-select name="age_group_id" id="age_group_id" :required="true">
                    <x-select-option value="" title="-- Chọn nhóm độ tuổi --" />
                    @foreach ($ageGroups as $group)
                        <x-select-option :value="$group->id" :title="$group->name . ' (' . ($group->min_months ?? 0) . ' - ' . ($group->max_months ?? '...') . ' tháng)'" />
                    @endforeach
                </x-select>
                <div class="form-text text-muted">
                    {{ __('Phân loại độ tuổi của trẻ em mà danh mục này áp dụng.') }}
                </div>
            </div>

            {{-- 5 Lĩnh vực cốt lõi (Pillars) - 5 Buttons dạng Card --}}
            <div class="mb-4">
                <label class="form-label fw-bold text-dark fs-14 mb-2">
                    {{ __('2. Lĩnh vực giáo dục cốt lõi (Trụ cột)') }}: <span class="text-danger">*</span>
                </label>
                <div class="row g-2" id="pillar_card_group">
                    @foreach ($pillars as $p)
                        <div class="col-6 col-sm-4 col-md-auto flex-fill">
                            <label class="card card-sm card-link text-center p-3 h-100 cursor-pointer border pillar-card"
                                   id="pillar_label_{{ $p->value }}"
                                   style="border-radius: 10px; transition: all 0.2s;"
                                   for="pillar_radio_{{ $p->value }}">
                                <input type="radio" name="pillar" id="pillar_radio_{{ $p->value }}"
                                       value="{{ $p->value }}"
                                       class="d-none pillar-radio-input"
                                       {{ old('pillar', 'pq') == $p->value ? 'checked' : '' }} required>
                                <div class="mb-2">
                                    <span class="avatar avatar-md rounded-circle" style="background-color: {{ $p->color() }}15; color: {{ $p->color() }};">
                                        <i class="{{ $p->icon() }} fs-2"></i>
                                    </span>
                                </div>
                                <div class="fw-bold text-dark fs-13 mb-1">{{ $p->label() }}</div>
                                <div class="text-muted fs-11 text-uppercase">{{ $p->value }}</div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Định danh chuyên môn (Key) --}}
            <div class="mb-4">
                <label class="form-label fw-bold text-dark fs-14">
                    {{ __('3. Định danh chuyên môn (Key)') }}: <span class="text-danger">*</span>
                </label>
                <select name="key" id="category_key_select" class="form-select" required>
                    <option value="">-- Đang tải danh sách key... --</option>
                </select>
                <div class="form-text text-muted">
                    {{ __('Hệ thống sẽ tự động điền Tên danh mục bên dưới khi bạn chọn Key.') }}
                </div>
            </div>

            {{-- Tên danh mục --}}
            <div class="mb-3">
                <label class="form-label fw-bold text-dark fs-14">
                    {{ __('4. Tên hiển thị danh mục') }}: <span class="text-danger">*</span>
                </label>
                <x-input type="text" name="name" id="category_name_input" :value="old('name')" :required="true" placeholder="Ví dụ: Chiều cao, Toán học – Logic, Đồng cảm..." />
            </div>

            {{-- Icon danh mục --}}
            <div class="mb-3">
                <label class="form-label fw-bold text-dark fs-14">{{ __('Icon / Hình ảnh đại diện') }}:</label>
                <x-input-image name="icon" />
            </div>

            {{-- Mô tả ngắn --}}
            <div class="mb-3">
                <label class="form-label fw-bold text-dark fs-14">{{ __('Mô tả ngắn') }}:</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Mô tả tóm tắt ý nghĩa và mục tiêu của danh mục này đối với sự phát triển của trẻ...">{{ old('description') }}</textarea>
            </div>

            {{-- Thứ tự sắp xếp --}}
            <div class="mb-2">
                <label class="form-label fw-bold text-dark fs-14">{{ __('Thứ tự sắp xếp') }}:</label>
                <x-input type="number" name="sort_order" :value="old('sort_order', 0)" min="0" />
            </div>
        </div>
    </div>
</div>

<style>
.pillar-card:hover {
    border-color: #3b82f6 !important;
    background-color: #f8fafc;
    transform: translateY(-2px);
}
.pillar-card.active-pillar {
    border-color: #3b82f6 !important;
    border-width: 2px !important;
    background-color: #eff6ff !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}
</style>

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('.pillar-radio-input');
    const cards = document.querySelectorAll('.pillar-card');
    const keySelect = document.getElementById('category_key_select');
    const nameInput = document.getElementById('category_name_input');

    function highlightSelectedPillar() {
        cards.forEach(card => card.classList.remove('active-pillar'));
        const checkedRadio = document.querySelector('.pillar-radio-input:checked');
        if (checkedRadio) {
            const card = document.getElementById('pillar_label_' + checkedRadio.value);
            if (card) card.classList.add('active-pillar');
        }
    }

    function loadKeysForPillar(pillarValue, selectedKey = null) {
        if (!pillarValue) return;

        keySelect.innerHTML = '<option value="">⏳ Đang tải key...</option>';

        fetch('{{ route("admin.lesson_category.get_keys") }}?pillar=' + encodeURIComponent(pillarValue))
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    keySelect.innerHTML = '<option value="">-- Chọn định danh chuyên môn (Key) --</option>';
                    Object.values(res.data).forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.key;
                        opt.textContent = item.label + ' (' + item.key + ')';
                        opt.dataset.label = item.label;
                        if (selectedKey && item.key === selectedKey) {
                            opt.selected = true;
                        }
                        keySelect.appendChild(opt);
                    });
                }
            })
            .catch(() => {
                keySelect.innerHTML = '<option value="">❌ Lỗi khi tải danh sách key</option>';
            });
    }

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            highlightSelectedPillar();
            loadKeysForPillar(this.value);
        });
    });

    keySelect.addEventListener('change', function () {
        const selectedOpt = this.options[this.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.label && (!nameInput.value || nameInput.value.trim() === '')) {
            nameInput.value = selectedOpt.dataset.label;
        }
    });

    // Khởi tạo ban đầu
    highlightSelectedPillar();
    const initialRadio = document.querySelector('.pillar-radio-input:checked');
    if (initialRadio) {
        loadKeysForPillar(initialRadio.value, '{{ old("key") }}');
    }
});
</script>
@endpush
