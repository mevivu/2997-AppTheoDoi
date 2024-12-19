<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            {{ __('Đăng') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                                 class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                <x-link :href="route('admin.journal.index')" class="w-50 btn btn-outline" :title="'Quay lại'" />
            </div>
        </div>
    </div>


    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-status-change me-1"></span>
            {{ __('Kiểu') }}
        </div>
        <div class="card-body p-2">
            <x-select name="type"  :required="true">
                @foreach ($type as $key => $value)
                    <x-select-option :value="$key" :title="$value"  :option="$response->type->value"/>
                @endforeach
            </x-select>
        </div>
    </div>

    <!-- avatar -->
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header">
                <span class="ti ti-photo me-1"></span>
                @lang('avatar')
            </div>
            <div class="card-body p-2">
                <x-input-image-ckfinder name="avatar" showImage="avatar" class="img-fluid" :value="$response->image" />
            </div>
        </div>
    </div>

</div>
