<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-upload me-1"></span>
            {{ __('Hoạt động') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                                 class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                <x-link :href="route('admin.develop.index')" class="btn btn-outline w-50">
                    @lang('Quay lại')
                </x-link>
            </div>
        </div>
    </div>
    <input type="hidden" name="type" value="{{ \App\Enums\Guide\GuideType::Develop->value }}">

    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-status-change me-1"></span>
            {{ __('Trạng thái') }}
        </div>
        <div class="card-body p-2">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" :selected="$instance->status->value == $key" />
                @endforeach
            </x-select>
        </div>
    </div>
</div>
