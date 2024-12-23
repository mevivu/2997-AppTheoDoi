<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- type -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('type')</label>
                    <x-select name="type" disabled :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option :value="$key"
                                             :title="$value"
                                             :selected="$instance->type->value == $key"/>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('title')</label>
                    <x-input type="text"
                             name="title"
                             :value="$instance->title"
                             :required="true"
                             :placeholder="__('title')"/>
                </div>
            </div>

            <!-- age -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('age')</label>
                    <x-input type="text"
                             name="age"
                             :value="$instance->age"
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
                    >{{ $instance->description }}</textarea>
                </div>
            </div>

        </div>
    </div>
</div>
