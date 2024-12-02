@php 
    use App\AES\AESHelper; 
    use Carbon\Carbon; 

    $birthday = Carbon::parse($children->birthday)->format('Y-m-d');
@endphp
<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Admin') }}</h2>
        </div>
        <div class="row card-body">

            <!-- Fullname -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Họ và tên') }}:</label>
                    <x-input name="fullname" :value="old('fullname')" :required="true" 
                    placeholder="{{ __('Họ và tên') }}" 
                    value="{{ $children->fullname }}"/>
                </div>
            </div>

            <!-- birthday -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Ngày sinh') }}:</label>
                    <x-input type="date" name="birthday" :value="old('birthday')" 
                    :required="true" placeholder="{{ __('Ngày sinh') }}" 
                    value="{{ $birthday }}"/>
                </div>
            </div>

            <!-- gender-->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Giới tính') }}:</label>
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
                <x-select class="select2-bs5-ajax" 
                          name="user_id" 
                          id="user_id" 
                          :data-url="route('admin.search.select.user')">
                    <x-select-option 
                        :option="$children->user_id" 
                        :value="$children->user_id" 
                        :title="$children->user->fullname . '-' . AESHelper::decrypt($children->user->phone)"
                        :selected="old('user_id') ? (old('user_id') == $children->user_id) : true"
                    />
                </x-select>
            </div>
        </div>

    </div>
</div>
