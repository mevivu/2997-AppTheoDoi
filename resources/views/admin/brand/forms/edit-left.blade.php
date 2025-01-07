<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Tên thương hiệu -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên thương hiệu')</label>
                    <x-input type="text" name="name" :value="$instance->name" :required="true" :placeholder="__('Tên thương hiệu')" />
                </div>
            </div>

            <!-- Mô tả -->
            <div class="col-12">
                @php
                    // Decode the JSON description and ensure it's an array
                    $description = json_decode($instance->description);
                @endphp
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="control-label">@lang('Mô tả')</label>
                        <p class="text-primary" style="cursor: pointer;" id="add-description">
                            <i class="ti ti-plus"></i> Thêm mô tả
                        </p>
                    </div>
                    <div class="d-flex flex-column gap-2" id="description-container">
                        @if (is_array($description))
                            @foreach ($description as $item)
                                <div class="d-flex align-items-stretch gap-1">
                                    <textarea name="description[]" class="form-control" rows="2" placeholder="{{ __('Mô tả thương hiệu') }}">{{ $item }}</textarea>
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

            <!-- Quốc gia -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Quốc gia')</label>
                    <x-input type="text" name="country" :value="$instance->country" :required="false" :placeholder="__('Quốc gia thương hiệu')" />
                </div>
            </div>



        </div>
    </div>
</div>
