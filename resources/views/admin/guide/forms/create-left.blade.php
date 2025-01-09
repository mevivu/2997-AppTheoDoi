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



        </div>
    </div>
</div>
