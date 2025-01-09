<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên')</label>
                    <x-input name="name"
                             :value="old('title')"
                             :required="true"
                             :placeholder="__('title')"/>
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
