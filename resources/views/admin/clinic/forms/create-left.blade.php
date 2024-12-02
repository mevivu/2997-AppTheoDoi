<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên')</label>
                    <x-input name="name"
                             :value="old('name')"
                             :required="true"
                             :placeholder="__('name')"/>
                </div>
            </div>


            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên')</label>
                    <x-input name="description"
                             :value="old('description')"
                             :required="true"
                             :placeholder="__('description')"/>
                </div>
            </div>

            {{-- address --}}
            <div class="col-12">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('province'):</label>
                        <x-select name="province" :required="true">
                            <x-select-option value="" :title="__('choose')"/>
                            @foreach ($provinces as $province)
                                <x-select-option :value="$province->code" :title="__($province->name)"/>
                            @endforeach
                        </x-select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('district'):</label>
                        <x-select name="district" required>
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </x-select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('ward'):</label>
                        <x-select name="ward" required>
                            <option value="">-- Chọn Phường/Xã --</option>
                        </x-select>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
