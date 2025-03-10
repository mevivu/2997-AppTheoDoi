<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin trẻ') }}</h2>
        </div>
        <div class="row card-body">

            <!-- Fullname -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Họ và tên') }}:</label>
                    <x-input name="fullname" :value="old('fullname')" :required="true" placeholder="{{ __('Họ và tên') }}" />
                </div>
            </div>

            <!-- birthday -->
            <div class="col-md-6 col-sm-12" id="date_birthday">
                <div class="mb-3 ">
                    <label class="control-label">{{ __('Ngày sinh') }}:</label>
                    <x-input type="date" name="birthday" :value="old('birthday')" placeholder="{{ __('Ngày sinh') }}" />
                </div>
            </div>

            <div class="col-md-6 col-sm-12 d-none" id="due_date">
                <div class="mb-3">
                    <label class="control-label">{{ __('Ngày dự sinh') }}:</label>
                    <x-input type="date" name="due_date" :value="old('due_date')" placeholder="{{ __('Ngày sinh') }}" />
                </div>
            </div>

            <!-- gender-->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Giới tính') }}:</label>
                    <x-select name="gender" :required="true">
                        @foreach ($gender as $key => $value)
                            <x-select-option :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    @lang('Cha/mẹ'):</label>
                <x-select class="select2-bs5-ajax" name="user_id" id="user_id" :data-url="route('admin.search.select.user')">
                </x-select>
            </div>
        </div>

    </div>
</div>
