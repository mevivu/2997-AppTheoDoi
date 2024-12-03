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

            <!--reminder_time-->
            <div class="col-6">
                <div class="mb-3">
                    <i class="ti ti-clock"></i>
                    <label class="control-label">@lang('performed_on')</label>
                    <x-input input
                             type="datetime-local"
                             name="performed_on"
                             :value="$instance->performed_on"
                             :required="true"
                             :placeholder="__('performed_on')" />
                </div>
            </div>

        </div>
    </div>
</div>
