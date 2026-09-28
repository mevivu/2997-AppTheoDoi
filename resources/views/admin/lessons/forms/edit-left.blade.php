<div class="col-12 col-lg-8">
    {{-- Khối 1: Danh mục bài học (/admin/lesson-categories & Lĩnh vực) --}}
    <div class="card custom-shadow mb-4 border-0">
        <div class="card-header bg-light py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div>
                <h4 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-category text-primary fs-2"></i>
                    <span>{{ __('1. Danh mục bài học & Lĩnh vực') }}</span>
                </h4>
                <div class="text-muted fs-12 mt-1">
                    {{ __('Bài học thuộc một Danh mục bài học, và mỗi danh mục thuộc một Lĩnh vực giáo dục (5 Trụ cột).') }}
                </div>
            </div>
            <a href="{{ route('admin.lesson_category.index') }}" target="_blank" class="btn btn-outline-primary btn-sm fw-semibold">
                <i class="ti ti-folder me-1"></i>{{ __('Quản lý danh mục') }}
            </a>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                @php
                    $currentPillar = $instance->category?->pillar?->value ?? ($instance->category?->pillar ?? 'all');
                    $currentAgeGroupId = $instance->category?->age_group_id ?? '';
                @endphp

                {{-- Lọc nhanh theo Lĩnh vực (5 Tabs Pills) --}}
                <div class="col-12">
                    <label class="form-label fw-bold text-dark fs-13 mb-2 d-flex justify-content-between align-items-center">
                        <span>{{ __('Lọc nhanh theo Lĩnh vực giáo dục:') }}</span>
                        <span class="text-muted fs-11 fw-normal">{{ __('Bấm để lọc danh mục theo lĩnh vực tương ứng') }}</span>
                    </label>
                    <div class="d-flex flex-wrap gap-2" id="pillar_filter_pills">
                        <button type="button" class="btn btn-sm {{ $currentPillar === 'all' ? 'active btn-dark' : 'btn-outline-primary' }} pillar-filter-btn" data-pillar="all">
                            <i class="ti ti-layout-grid me-1"></i>{{ __('Tất cả lĩnh vực') }}
                        </button>
                        @foreach ($pillars as $p)
                            @php
                                $isActive = ($currentPillar == $p->value);
                            @endphp
                            <button type="button" class="btn btn-sm {{ $isActive ? 'active btn-dark' : 'btn-outline-primary' }} pillar-filter-btn" 
                                    data-pillar="{{ $p->value }}"
                                    data-color="{{ $p->color() }}">
                                <i class="{{ $p->icon() }} me-1"></i>{{ $p->label() }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Chọn Danh mục bài học chính --}}
                <div class="col-12 col-md-8">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">
                        {{ __('Danh mục bài học') }}: <span class="text-danger">*</span>
                    </label>
                    <select name="lesson_category_id" id="lesson_category_id" class="form-select @error('lesson_category_id') is-invalid @enderror" required>
                        <option value="">-- {{ __('Chọn danh mục bài học') }} --</option>
                        @foreach ($pillars as $p)
                            @php
                                $pCats = $categories->where('pillar', $p);
                            @endphp
                            @if($pCats->isNotEmpty())
                                <optgroup label="⭐ {{ $p->label() }} ({{ strtoupper($p->value) }})" data-pillar="{{ $p->value }}">
                                    @foreach($pCats as $cat)
                                        @php
                                            $ageText = $cat->ageGroup ? ($cat->ageGroup->name . ' - ' . ($cat->ageGroup->min_months ?? 0) . '-' . ($cat->ageGroup->max_months ?? '...') . ' tháng') : 'Mọi độ tuổi';
                                            $isSel = old('lesson_category_id', $instance->lesson_category_id) == $cat->id;
                                        @endphp
                                        <option value="{{ $cat->id }}"
                                                data-pillar="{{ $cat->pillar?->value }}"
                                                data-pillar-label="{{ $cat->pillar?->label() }}"
                                                data-pillar-icon="{{ $cat->pillar?->icon() }}"
                                                data-pillar-color="{{ $cat->pillar?->color() }}"
                                                data-age-group="{{ $cat->ageGroup?->name ?? 'Mọi độ tuổi' }}"
                                                data-age-group-id="{{ $cat->age_group_id }}"
                                                data-cat-name="{{ $cat->name }}"
                                                data-cat-key="{{ $cat->key?->value ?? $cat->key }}"
                                                {{ $isSel ? 'selected' : '' }}>
                                            {{ $cat->name }} [{{ $ageText }}]
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    @error('lesson_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Lọc phụ theo Nhóm tuổi (Tùy chọn) --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">
                        {{ __('Lọc theo Nhóm độ tuổi') }}:
                    </label>
                    <select id="filter_age_group_id" class="form-select">
                        <option value="">-- Tất cả độ tuổi --</option>
                        @foreach ($ageGroups as $group)
                            <option value="{{ $group->id }}" {{ $currentAgeGroupId == $group->id ? 'selected' : '' }}>
                                {{ $group->name }} ({{ $group->min_months ?? 0 }} - {{ $group->max_months ?? '...' }} tháng)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Thẻ thông tin chi tiết danh mục được chọn (Live Category Info Card) --}}
                <div class="col-12" id="selected_cat_info_wrapper" style="display: none;">
                    <div class="p-3 rounded-3 border bg-white d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 shadow-xs">
                        <div class="d-flex align-items-center gap-3">
                            <span class="avatar avatar-md rounded-circle text-white shadow-xs" id="cat_info_pillar_avatar" style="background-color: #206bc4; width: 44px; height: 44px;">
                                <i class="ti ti-category fs-2" id="cat_info_pillar_icon"></i>
                            </span>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge fw-bold px-2 py-1 fs-12" id="cat_info_pillar_badge">Lĩnh vực</span>
                                    <span class="fw-bold text-dark fs-14" id="cat_info_name">Tên danh mục</span>
                                </div>
                                <div class="text-muted fs-12 d-flex align-items-center gap-3 flex-wrap">
                                    <span><i class="ti ti-baby-carriage me-1 text-primary"></i>{{ __('Độ tuổi áp dụng:') }} <strong id="cat_info_age" class="text-dark">—</strong></span>
                                    <span><i class="ti ti-key me-1 text-muted"></i>{{ __('Mã key:') }} <code id="cat_info_key">—</code></span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.lesson_category.index') }}" target="_blank" class="btn btn-light btn-sm flex-shrink-0" id="cat_info_link">
                            <i class="ti ti-external-link me-1"></i>{{ __('Xem danh mục này') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Khối 2: Thông tin chi tiết bài học --}}
    <div class="card custom-shadow mb-4 border-0">
        <div class="card-header bg-light py-3">
            <h4 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="ti ti-file-description text-primary fs-2"></i>
                <span>{{ __('2. Thông tin nội dung bài học') }}</span>
            </h4>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                {{-- Tên bài học --}}
                <div class="col-12">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">
                        {{ __('Tên bài học') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input type="text" name="name" :value="old('name', $instance->name)" :required="true" placeholder="Ví dụ: Rèn luyện khả năng thăng bằng với bóng phản xạ..." />
                </div>

                {{-- Tóm tắt bài học --}}
                <div class="col-12">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">{{ __('Tóm tắt bài học') }}:</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $instance->description) }}</textarea>
                </div>

                {{-- Độ khó bài học (3 Thẻ chọn trực quan phân biệt trạng thái) --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">
                        {{ __('Độ khó bài học') }}: <span class="text-danger">*</span>
                    </label>
                    @php
                        $currentDiff = old('difficulty', $instance->difficulty?->value ?? 'easy');
                    @endphp
                    <div class="diff-selector d-flex gap-2">
                        {{-- Dễ --}}
                        <label class="diff-card diff-card-easy flex-fill cursor-pointer rounded-3 p-2 text-center {{ $currentDiff == 'easy' ? 'is-active' : '' }}" for="diff_easy">
                            <input type="radio" class="d-none diff-input" name="difficulty" id="diff_easy" value="easy" 
                                   {{ $currentDiff == 'easy' ? 'checked' : '' }} autocomplete="off">
                            <div class="d-flex align-items-center justify-content-center gap-1 py-1">
                                <i class="ti ti-mood-smile fs-2"></i>
                                <span class="fw-bold fs-13">{{ __('Dễ') }}</span>
                            </div>
                        </label>

                        {{-- Có trợ giúp --}}
                        <label class="diff-card diff-card-medium flex-fill cursor-pointer rounded-3 p-2 text-center {{ $currentDiff == 'medium' ? 'is-active' : '' }}" for="diff_medium">
                            <input type="radio" class="d-none diff-input" name="difficulty" id="diff_medium" value="medium" 
                                   {{ $currentDiff == 'medium' ? 'checked' : '' }} autocomplete="off">
                            <div class="d-flex align-items-center justify-content-center gap-1 py-1">
                                <i class="ti ti-hand-stop fs-2"></i>
                                <span class="fw-bold fs-13">{{ __('Có trợ giúp') }}</span>
                            </div>
                        </label>

                        {{-- Tự lập / Thử thách --}}
                        <label class="diff-card diff-card-hard flex-fill cursor-pointer rounded-3 p-2 text-center {{ $currentDiff == 'hard' ? 'is-active' : '' }}" for="diff_hard">
                            <input type="radio" class="d-none diff-input" name="difficulty" id="diff_hard" value="hard" 
                                   {{ $currentDiff == 'hard' ? 'checked' : '' }} autocomplete="off">
                            <div class="d-flex align-items-center justify-content-center gap-1 py-1">
                                <i class="ti ti-flame fs-2"></i>
                                <span class="fw-bold fs-13">{{ __('Tự lập / Thử thách') }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                <style>
                    .diff-card {
                        border: 2px solid #e2e8f0;
                        background-color: #ffffff;
                        color: #64748b;
                        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                        user-select: none;
                    }
                    .diff-card:hover {
                        border-color: #cbd5e1;
                        background-color: #f8fafc;
                        transform: translateY(-1px);
                    }
                    .diff-card-easy i { color: #2fb344; }
                    .diff-card-medium i { color: #0054a6; }
                    .diff-card-hard i { color: #d63939; }

                    /* Dễ - Active */
                    .diff-card-easy.is-active,
                    .diff-card-easy:has(input:checked) {
                        border-color: #2fb344 !important;
                        background-color: #f0fdf4 !important;
                        color: #166534 !important;
                        box-shadow: 0 4px 12px rgba(47, 179, 68, 0.2) !important;
                        transform: translateY(-1px);
                    }
                    /* Có trợ giúp - Active */
                    .diff-card-medium.is-active,
                    .diff-card-medium:has(input:checked) {
                        border-color: #0054a6 !important;
                        background-color: #eff6ff !important;
                        color: #1e40af !important;
                        box-shadow: 0 4px 12px rgba(0, 84, 166, 0.2) !important;
                        transform: translateY(-1px);
                    }
                    /* Tự lập - Active */
                    .diff-card-hard.is-active,
                    .diff-card-hard:has(input:checked) {
                        border-color: #d63939 !important;
                        background-color: #fef2f2 !important;
                        color: #991b1b !important;
                        box-shadow: 0 4px 12px rgba(214, 57, 57, 0.2) !important;
                        transform: translateY(-1px);
                    }
                </style>

                {{-- Tần suất gợi ý --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">
                        {{ __('Tần suất gợi ý') }}:
                    </label>
                    <div class="input-group">
                        <input type="text" name="frequency" id="frequency_input" class="form-control" value="{{ old('frequency', $instance->frequency ?? '3 lần/tuần') }}">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Gợi ý</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item freq-preset" href="javascript:void(0)" data-val="Hàng ngày">Hàng ngày</a></li>
                            <li><a class="dropdown-item freq-preset" href="javascript:void(0)" data-val="3 lần/tuần">3 lần/tuần</a></li>
                            <li><a class="dropdown-item freq-preset" href="javascript:void(0)" data-val="Cuối tuần">Cuối tuần</a></li>
                            <li><a class="dropdown-item freq-preset" href="javascript:void(0)" data-val="Tùy hứng">Tùy hứng</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Lợi ích cho trẻ --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">{{ __('Lợi ích phát triển mang lại') }}:</label>
                    <textarea name="benefit" class="form-control" rows="3">{{ old('benefit', $instance->benefit) }}</textarea>
                </div>

                {{-- Dụng cụ cần chuẩn bị --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">{{ __('Dụng cụ / Học cụ cần chuẩn bị') }}:</label>
                    <textarea name="tools" class="form-control" rows="3">{{ old('tools', $instance->tools) }}</textarea>
                </div>

                {{-- Hướng dẫn chi tiết / Nội dung --}}
                <div class="col-12">
                    <label class="form-label fw-bold text-dark fs-13 mb-1">{{ __('Nội dung hướng dẫn chi tiết cho phụ huynh') }}:</label>
                    <textarea name="content" class="ckeditor visually-hidden">{{ old('content', $instance->content) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Khối 3: Đa Video Repeater (Hỗ trợ 2 hoặc nhiều video) --}}
    @include('admin.lessons.partials.video-repeater')
</div>

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const catSelect = document.getElementById('lesson_category_id');
    const frequencyInput = document.getElementById('frequency_input');
    const ageFilterSelect = document.getElementById('filter_age_group_id');
    const pillarButtons = document.querySelectorAll('.pillar-filter-btn');

    // Preset tần suất
    document.querySelectorAll('.freq-preset').forEach(item => {
        item.addEventListener('click', function () {
            frequencyInput.value = this.dataset.val;
        });
    });

    // Cập nhật Live Card thông tin Danh mục
    const catInfoWrapper = document.getElementById('selected_cat_info_wrapper');
    const catInfoPillarBadge = document.getElementById('cat_info_pillar_badge');
    const catInfoPillarAvatar = document.getElementById('cat_info_pillar_avatar');
    const catInfoPillarIcon = document.getElementById('cat_info_pillar_icon');
    const catInfoName = document.getElementById('cat_info_name');
    const catInfoAge = document.getElementById('cat_info_age');
    const catInfoKey = document.getElementById('cat_info_key');

    function updateCategoryInfo() {
        if (!catSelect || !catInfoWrapper) return;
        const selectedOpt = catSelect.options[catSelect.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            catInfoWrapper.style.display = 'none';
            return;
        }

        const pillarLabel = selectedOpt.dataset.pillarLabel || 'Lĩnh vực';
        const pillarColor = selectedOpt.dataset.pillarColor || '#206bc4';
        const pillarIcon = selectedOpt.dataset.pillarIcon || 'ti ti-category';
        const ageGroup = selectedOpt.dataset.ageGroup || 'Mọi độ tuổi';
        const catName = selectedOpt.dataset.catName || selectedOpt.textContent.trim();
        const catKey = selectedOpt.dataset.catKey || '';

        catInfoWrapper.style.display = 'block';
        catInfoPillarBadge.textContent = pillarLabel;
        catInfoPillarBadge.style.backgroundColor = pillarColor + '20';
        catInfoPillarBadge.style.color = pillarColor;
        catInfoPillarAvatar.style.backgroundColor = pillarColor;
        catInfoPillarIcon.className = pillarIcon + ' fs-2 text-white';
        catInfoName.textContent = catName;
        catInfoAge.textContent = ageGroup;
        catInfoKey.textContent = catKey ? catKey.toUpperCase() : '—';
    }

    if (catSelect) {
        catSelect.addEventListener('change', updateCategoryInfo);
    }

    // Lọc nhanh theo Lĩnh vực (Pills) & Độ tuổi
    function filterCategories() {
        const activeBtn = document.querySelector('.pillar-filter-btn.active');
        const selectedPillar = activeBtn ? activeBtn.dataset.pillar : 'all';
        const selectedAge = ageFilterSelect ? ageFilterSelect.value : '';

        const optgroups = catSelect.querySelectorAll('optgroup');
        optgroups.forEach(group => {
            const groupPillar = group.dataset.pillar;
            let groupHasVisible = false;

            const options = group.querySelectorAll('option');
            options.forEach(opt => {
                const optPillar = opt.dataset.pillar;
                const optAgeId = opt.dataset.ageGroupId;

                const matchPillar = (selectedPillar === 'all' || optPillar === selectedPillar);
                const matchAge = (!selectedAge || optAgeId === selectedAge);

                if (matchPillar && matchAge) {
                    opt.hidden = false;
                    opt.disabled = false;
                    groupHasVisible = true;
                } else {
                    opt.hidden = true;
                    opt.disabled = true;
                }
            });

            group.hidden = !groupHasVisible;
        });

        // Nếu option đang chọn bị ẩn thì reset
        const currentOpt = catSelect.options[catSelect.selectedIndex];
        if (currentOpt && currentOpt.hidden) {
            catSelect.value = '';
            updateCategoryInfo();
        }
    }

    pillarButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            pillarButtons.forEach(b => {
                b.classList.remove('active', 'btn-dark');
                b.classList.add('btn-outline-primary');
            });
            this.classList.add('active', 'btn-dark');
            this.classList.remove('btn-outline-primary');
            filterCategories();
        });
    });

    if (ageFilterSelect) {
        ageFilterSelect.addEventListener('change', filterCategories);
    }

    // Kích hoạt hiển thị ban đầu
    updateCategoryInfo();

    // Đồng bộ trạng thái active của Thẻ độ khó
    const diffInputs = document.querySelectorAll('.diff-input');
    const diffCards = document.querySelectorAll('.diff-card');
    function syncDiffCards() {
        diffCards.forEach(card => {
            const input = card.querySelector('.diff-input');
            if (input && input.checked) {
                card.classList.add('is-active');
            } else {
                card.classList.remove('is-active');
            }
        });
    }
    diffInputs.forEach(input => {
        input.addEventListener('change', syncDiffCards);
    });
    syncDiffCards();
});
</script>
@endpush
