<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin danh mục video') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tên danh mục') }}: <span class="text-danger">*</span></label>
                    <x-input type="text" name="name" :value="old('name')" :required="true" placeholder="Ví dụ: PQ Thể chất, EQ Cảm xúc, Ngôn ngữ..." />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nhóm độ tuổi') }}: <span class="text-danger">*</span></label>
                    <x-select name="age_group_id" :required="true">
                        <x-select-option value="" title="-- Chọn nhóm tuổi --" />
                        @foreach ($ageGroups as $group)
                            <x-select-option :value="$group->id" :title="$group->name" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Icon danh mục') }}:</label>
                    <x-input-image name="icon" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Thứ tự sắp xếp') }}:</label>
                    <x-input type="number" name="sort_order" :value="old('sort_order', 0)" min="0" />
                </div>
            </div>
        </div>
    </div>
</div>
