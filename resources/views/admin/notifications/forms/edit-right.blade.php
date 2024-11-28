@php use App\Enums\Notification\MessageType; @endphp
<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header gap-1">
            <i class="ti ti-settings"></i>
            @lang('action')
        </div>
        <div class="card-body p-2 d-flex justify-content-between">
            <div class="d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save" />
                <x-button type="submit" name="submitter" value="saveAndExit">
                    @lang('save&exit')
                </x-button>
            </div>
            <x-button.modal-delete data-route="{{ route('admin.notification.delete', $notification->id) }}"
                :title="__('delete')" />
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header gap-1">
            <i class="ti ti-settings-cancel"></i>
            @lang('status')
        </div>
        <div class="card-body p-2">
            <x-select class="select2-bs5-ajax" name="status" :value="old('status')" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :option="$notification->status->value" :value="$key" :title="__($value)" />
                @endforeach
            </x-select>
        </div>
    </div>
</div>
