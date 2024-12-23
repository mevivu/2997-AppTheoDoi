<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('title')</label>
                    <x-input name="title"
                             :value="old('title')"
                             :required="true"
                             :placeholder="__('title')"/>
                </div>
            </div>

            <!-- age -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('age')</label>
                    <x-input name="age"
                             type="number"
                             :value="old('age')"
                             :required="true"
                             :placeholder="__('age')"/>
                </div>
            </div>


            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('description')</label>
                    <textarea name="description"
                              class="form-control"
                              rows="4"
                              placeholder="{{ __('description') }}"
                    >{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- type -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('type')</label>
                    <x-select name="type" :required="true" id="type-select" >
                        <x-select-option value="" :title="__('--Chọn thể loại--')" />
                        @foreach ($type as $key => $value)
                            <x-select-option :value="$key" :title="$value"/>
                        @endforeach
                    </x-select>
                </div>
            </div>
            <div class="col-12">
                <div id="count-checked" class="mb-3">
                    Các câu hỏi được chọn: <span id="checked-count">0</span>
                </div>
            </div>

            <!-- show questions -->
            <div class="col-12">

                <div id="loading" style="display: none;">
                    <i class="fa fa-spinner fa-spin"></i> Loading...
                </div>
                <div id="questions-container" class="mb-3">
                 </div>
            </div>

        </div>
    </div>
</div>
