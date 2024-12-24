<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Lớp') }}</h2>
        </div>
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên')</label>
                    <x-input type="text"
                             name="name"
                             :value="$response->name"
                             :required="true"
                             :placeholder="__('name')"/>
                </div>
            </div>


        </div>
    </div>
</div>
