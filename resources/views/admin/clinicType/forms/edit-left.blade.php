<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên')</label>
                    <x-input type="text"
                             name="name"
                             :value="$instance->name"
                             :required="true"
                             :placeholder="__('name')"/>
                </div>
            </div>

            <!-- length -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('description')</label>
                    <x-input name="description"
                             type="text"
                             :value="$instance->description"
                             :required="true"
                             :placeholder="__('length')"/>
                </div>
            </div>

        </div>
    </div>
</div>
