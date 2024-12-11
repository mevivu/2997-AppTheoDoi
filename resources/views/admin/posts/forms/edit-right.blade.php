<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-upload me-1"></span>
            {{ __('Đăng') }}
        </div>

        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                    class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                <x-link :href="route('admin.post.index')" class="btn btn-outline w-50">
                    @lang('Quay lại')
                </x-link>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-category me-1"></span>
            {{ __('Danh mục') }}
        </div>
        <div class="card-body p-2 wrap-list-checkbox">
            @foreach ($categories as $category)
                <x-input-checkbox :checked="$post->categories->pluck('id')->toArray()" :depth="$category->depth" name="categories_id[]" :label="$category->name"
                    :value="$category->id" />
            @endforeach
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-tag me-1"></span>
            {{ __('Nổi bật') }}
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_featured" value="{{ App\Enums\FeaturedStatus::Featureless->value }}">
            <x-input-switch name="is_featured" value="{{ App\Enums\FeaturedStatus::Featured->value }}" :label="__('Nổi bật?')"
                :checked="$post->is_featured->value == App\Enums\FeaturedStatus::Featured->value" />
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-status-change me-1"></span>
            {{ __('Trạng thái') }}
        </div>
        <div class="card-body p-2">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :option="$post->status->value" :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-photo me-1"></span>
            {{ __('Ảnh đại diện') }}
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="image" showImage="image" :value="$post->image" />
        </div>
    </div>
</div>
