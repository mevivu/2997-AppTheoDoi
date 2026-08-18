<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Thai kì') }}</h2>
        </div>
        <div class="row card-body">

            <!-- children -->
            <div class="col-md-12 col-sm-12">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    @lang('Trẻ em'): <span class="text-danger">*</span></label>
                <x-select class="select2-bs5-ajax"
                          name="child_id"
                          id="child_id"
                          :required="true"
                          :data-url="route('admin.search.select.children')">
                    <x-select-option
                        :option="$response->child_id"
                        :value="$response->child_id"
                        :title="$response->child->fullname "
                        :selected="old('child_id') ? (old('child_id') == $child->child_id) : true"
                    />
                </x-select>

            </div>
            {{--  week--}}
            <div class="col-md-6 col-12 mt-2">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-calendar-search"></span>
                        @lang('Tuần'):</label>
                    <x-input type="number" name="week" :value="$response->week"/>
                </div>
            </div>
            {{--  weight--}}
            <div class="col-md-6 col-12 mt-2">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-weight"></span>
                        @lang('Cân năng(kg)'):</label>
                    <x-input type="number" name="weight" step="any" :value="$response->weight"/>
                </div>
            </div>
            {{--  length--}}
            <div class="col-md-6 col-12 mt-2">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-ruler-2"></span>
                        @lang('Chiều dài(cm)'):</label>
                    <x-input type="number" name="length" :value="$response->length"/>
                </div>
            </div>
            {{--  head_circumference--}}
            <div class="col-md-6 col-12 mt-2">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-layers-difference"></span>
                        @lang('Chu vi vòng đầu(cm)'):</label>
                    <x-input type="number" name="head_circumference" :value="$response->head_circumference"/>
                </div>
            </div>

        </div>

    </div>
</div>
