@php
    use App\AES\AESHelper;
    use Carbon\Carbon;

    $birthday = Carbon::parse($children->birthday)->format('Y-m-d');
    $due_date = Carbon::parse($children->due_date)->format('Y-m-d');
@endphp
<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Trẻ em') }}</h2>
        </div>
        <div class="row card-body">

            <!-- Fullname -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Họ và tên') }}: <span class="text-danger">*</span></label>
                    <x-input name="fullname" :value="old('fullname')" :required="true" placeholder="{{ __('Họ và tên') }}"
                        value="{{ $children->fullname }}" />
                </div>
            </div>

            <!-- birthday -->
            <div class="col-md-6 col-sm-12">
                @if ($children->is_born == \App\Enums\Child\BornStatus::Born)
                    <div class="mb-3" id="date_birthday">
                        <label class="control-label">{{ __('Ngày sinh') }}:</label>
                        <x-input type="date" name="birthday" placeholder="{{ __('Ngày sinh') }}"
                            value="{{ $birthday }}" />
                    </div>
                @else
                    <div class="mb-3 d-none" id="date_birthday">
                        <label class="control-label">{{ __('Ngày sinh') }}:</label>
                        <x-input type="date" name="birthday" placeholder="{{ __('Ngày sinh') }}"
                            value="{{ old('birthday') }}" />
                    </div>
                @endif

                @if ($children->is_born == \App\Enums\Child\BornStatus::Unborn)
                    <div class="mb-3" id="due_date">
                        <label class="control-label">{{ __('Ngày dự sinh') }}:</label>
                        <x-input type="date" name="due_date" placeholder="{{ __('Ngày dự sinh') }}"
                            value="{{ $due_date }}" />
                    </div>
                @else
                    <div class="mb-3 d-none" id="due_date">
                        <label class="control-label">{{ __('Ngày dự sinh') }}:</label>
                        <x-input type="date" name="due_date" placeholder="{{ __('Ngày dự sinh') }}"
                            value="{{ old('due_date') }}" />
                    </div>
                @endif
            </div>

            <!-- gender-->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Giới tính') }}: <span class="text-danger">*</span></label>
                    <x-select name="gender" :required="true">
                        @foreach ($gender as $key => $value)
                            <x-select-option :option="$children->gender->value" :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    @lang('Cha/mẹ'):</label>
                <x-select class="select2-bs5-ajax" name="user_id" id="user_id" :data-url="route('admin.search.select.user')">
                    <x-select-option :option="$children->user_id" :value="$children->user_id" :title="$children->user->fullname . '-' . AESHelper::decrypt($children->user->phone)" :selected="old('user_id') ? old('user_id') == $children->user_id : true" />
                </x-select>
            </div>

            <!-- age -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('age') }}:</label>
                    <x-input name="age"
                             disabled
                             placeholder="{{ __('age') }}"
                             value="{{ $children->age }}" />
                </div>
            </div>

            <!-- month -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('month') }}:</label>
                    <x-input name="month"
                             disabled
                             placeholder="{{ __('month') }}"
                             value="{{ $children->month }}" />
                </div>
            </div>
        </div>

    </div>
</div>
