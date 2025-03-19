@php use App\Enums\Question\QuestionType; @endphp
<div class="col-12 col-md-3">

    <div class="card mb-3 custom-shadow">
        <div class="card-header">
            {{ __('Đăng') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                                 class="flex-column gap-1 text-wrap p-2 flex-grow-1"/>

                <x-link :href="route($route)" class="w-50 btn btn-outline" :title="'Quay lại'"/>

            </div>
        </div>
    </div>

    @if($instance->type == QuestionType::AQ || $instance->type == QuestionType::EQ)
        <div class="card mb-3 custom-shadow">
            <div class="card-header">
                @lang('type')
            </div>
            <div class="card-body p-2">
                <x-select name="age_group" :required="true">
                    <x-select-option value="" title="Chọn loại" :selected="$instance->age_group === null"/>
                    @foreach ($age_group as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="!is_null($instance->age_group) && $instance->age_group->value == $key"/>
                    @endforeach
                </x-select>
            </div>
        </div>
    @endif

    @if($instance->type == QuestionType::EQ)
        <div class="card mb-3 custom-shadow">
            <div class="card-header">
                @lang('Random')
            </div>
            <div class="card-body p-2">
                <x-select name="random">
                    <x-select-option value="" title="Chọn ngẫu nhiên" :selected="is_null(old('random'))"/>
                    @foreach ($random as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="old('random') == $key"/>
                    @endforeach
                </x-select>
            </div>
        </div>
    @endif



    <div class="card custom-shadow mb-3">
        <div class="card-header">
            @lang('status')
        </div>
        <div class="card-body p-2">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" :selected="$instance->status->value == $key"/>
                @endforeach
            </x-select>
        </div>
    </div>
</div>
