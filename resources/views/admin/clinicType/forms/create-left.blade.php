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

            <!-- length -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('length')(cm)</label>
                    <x-input name="length"
                             type="number"
                             min="1"
                             :value="old('length')"
                             :required="true"
                             :placeholder="__('length')"/>
                </div>
            </div>

            <!-- width -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('width')(cm)</label>
                    <x-input name="width"
                             type="number"
                             min="1"
                             :value="old('width')"
                             :required="true"
                             :placeholder="__('width')"/>
                </div>
            </div>

            <!-- amount -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Số lượng giấy')</label>
                    <x-input name="amount"
                             type="number"
                             min="1"
                             :value="old('amount')"
                             :required="true"
                             :placeholder="__('Số lượng giấy')"/>
                </div>
            </div>


        </div>
    </div>
</div>
