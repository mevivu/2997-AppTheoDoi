<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên')</label>
                    <x-input type="text" name="name" :value="$instance->name" :required="true" :placeholder="__('name')" />
                </div>
            </div>

            <!-- price -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('price')</label>
                    <x-input-price name="price" :value="$instance->price" :required="true" :placeholder="__('price')" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('type')</label>
                    <x-select name="type" :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$instance->type->value == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- days -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Số ngày')</label>
                    <x-input name="days"
                             type="number"
                             :value="$instance->days"
                             :required="true"
                             :placeholder="__('Số ngày')" />
                </div>
            </div>


            <!-- description -->
            <div class="col-12">
                @php
                    $description = json_decode($instance->description);
                @endphp
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="control-label">@lang('description')</label>
                        <p class="text-primary" style="cursor: pointer;" id="add-description">
                            <i class="ti ti-plus"></i> Thêm mô tả
                        </p>
                    </div>
                    <div class="d-flex flex-column gap-2" id="description-container">
                        @if ($description)
                            @foreach ($description as $item)
                                <div class="d-flex align-items-strech gap-1">
                                    <textarea name="description[]" class="form-control" rows="2" placeholder="{{ __('description') }}">{{ $item }}</textarea>
                                    @if (!$loop->first)
                                        <button type="button" class="btn btn-danger remove-description">
                                            <i class="ti ti-x fs-2"></i>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
