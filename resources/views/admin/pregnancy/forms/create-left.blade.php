<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Thai kì') }}</h2>
        </div>
        <div class="row card-body">


            <!-- children -->
            <div class="col-md-12 col-sm-12">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    @lang('Trẻ em'):</label>
                <x-select class="select2-bs5-ajax" name="child_id" id="child_id"
                          :data-url="route('admin.search.select.childrenBorn')">
                </x-select>
            </div>

            {{--  week--}}
            <div class="col-md-6 col-12 mt-2">
                <div class="mb-3">
                    <label class="control-label">

                        <span class="ti ti-calendar-search"></span>
                        @lang('Tuần'):</label>
                    <x-input type="number" name="week" :value="old('week')"/>
                </div>
            </div>
            {{--  weight--}}
            <div class="col-md-6 col-12 mt-2">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-weight"></span>
                        @lang('Cân năng(kg)'):</label>
                    <x-input type="number" name="weight" :value="old('weight')" step="any"/>
                </div>
            </div>
            {{--  length--}}
            <div class="col-md-6 col-12 mt-2">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-ruler-2"></span>
                        @lang('Chiều dài(cm)'):</label>
                    <x-input type="number" name="length" :value="old('length')"/>
                </div>
            </div>
            {{--  head_circumference--}}
            <div class="col-md-6 col-12 mt-2">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-layers-difference"></span>
                        @lang('Chu vi vòng đầu(cm)'):</label>
                    <x-input type="number" name="head_circumference" :value="old('head_circumference')"/>
                </div>
            </div>


        </div>

    </div>
</div>
