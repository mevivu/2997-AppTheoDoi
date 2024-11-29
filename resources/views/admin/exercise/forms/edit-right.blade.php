<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            {{ __('Đăng') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                    class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                @if ($response->exercise_type == \App\Enums\Exercise\ExerciseType::PHYSICAL)
                    <x-link :href="route('admin.exercise.physical')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @elseif($response->exercise_type == \App\Enums\Exercise\ExerciseType::POWER)
                    <x-link :href="route('admin.exercise.power')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @else
                    <x-link :href="route('admin.exercise.physical')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @endif
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            {{ __('Trạng thái') }}
        </div>
        <div class="card-body p-2">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" :option="$response->status->value" />
                @endforeach
            </x-select>
        </div>
    </div>
</div>
