<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            {{ __('Đăng') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                                 class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                @if ($response->exercise_type == \App\Enums\Journal\JournalType::Prescription)
                    <x-link :href="route('admin.journal.prescription')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @elseif($response->exercise_type == \App\Enums\Journal\JournalType::Moment)
                    <x-link :href="route('admin.journal.moment')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @else
                    <x-link :href="route('admin.journal.prescription')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @endif
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


</div>
