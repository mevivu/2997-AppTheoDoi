<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            {{ __('Đăng') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                    class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                <x-link :href="route('admin.post_category.index')" class="btn btn-outline w-50">
                    @lang('Quay lại')
                </x-link>
            </div>
        </div>
    </div>
    <!-- status -->
    <div class="card mb-3">
        <div class="card-header">
            @lang('status')
        </div>
        <div class="card-body p-2">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>
    <!-- avatar -->
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header">
                @lang('avatar')
            </div>
            <div class="card-body p-2">
                <x-input-image-ckfinder name="avatar" showImage="avatar" :value="$category->avatar" class="img-fluid" />
            </div>
        </div>
    </div>
</div>