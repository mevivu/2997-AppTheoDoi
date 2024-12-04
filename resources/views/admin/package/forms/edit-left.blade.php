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

            <!-- price -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('price')</label>
                    <x-input-price name="price"
                                   :value="$instance->price"
                                   :required="true"
                                   :placeholder="__('price')"/>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('type')</label>
                    <x-select name="type" :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option :value="$key"
                                             :title="$value"
                                             :selected="$instance->type->value == $key" />
                        @endforeach
                    </x-select>
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
