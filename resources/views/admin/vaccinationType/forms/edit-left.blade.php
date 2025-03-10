<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin loại tiêm chủng') }}</h2>
        </div>
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

            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('description')</label>
                    <textarea name="description"
                              class="form-control"
                              rows="4"
                              placeholder="{{ __('description') }}"
                    >{{ $instance->description }}</textarea>
                </div>
            </div>


        </div>
    </div>
</div>
