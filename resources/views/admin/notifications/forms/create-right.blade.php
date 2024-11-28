<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header gap-1">
            <i class="ti ti-settings"></i>
            @lang('action')
        </div>

        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                    class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                <x-button type="submit" name="submitter" value="saveAndExit" class="p-2 text-wrap w-50">
                    @lang('save&exit')
                </x-button>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header gap-1">
            <i class="ti ti-settings-cancel"></i>
            @lang('status')
        </div>
        <div class="card-body p-2">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    @if ($key == 1)
                        <x-select-option :value="$key" :title="$value" selected />
                    @else
                        <x-select-option :value="$key" :title="$value" />
                    @endif
                @endforeach
            </x-select>
        </div>
    </div>

</div>
