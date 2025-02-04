<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Tiêu đề hướng dẫn -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tiêu đề hướng dẫn')</label>
                    <x-input type="text" name="title" :value="$instance->title" :required="true" :placeholder="__('Tiêu đề hướng dẫn')" />
                </div>
            </div>

            <!-- Mô tả -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Mô tả') }}:</label>
                    <textarea name="description" class="ckeditor visually-hidden">{{ $instance->description }}</textarea>
                </div>
            </div>

            <!-- Các bước hướng dẫn -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>@lang('Các bước hướng dẫn')</h4>
                    </div>
                    <div class="card-body">
                        <div id="steps-container">
                            <div id="steps-list">
                                @forelse ($instance->steps ?? [] as $step)
                                    <div class="step-item border rounded p-3 mb-3" id="step-{{ $loop->index }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <label class="control-label d-block text-start">@lang('Thứ tự')</label>
                                                <input type="hidden" name="steps[{{ $loop->index }}][order]" value="{{ $step->order }}" class="step-order">
                                                <span class="form-control-plaintext">{{ $step->order }}</span>
                                            </div>
                                            <button type="button" class="btn btn-danger remove-step">@lang('Xóa bước')</button>
                                        </div>

                                        <div class="mb-3">
                                            <label class="control-label d-block text-start">@lang('Tiêu đề bước')</label>
                                            <x-input name="steps[{{ $loop->index }}][title]" :required="true" :placeholder="__('Tiêu đề bước')" :value="$step->title" class="w-100"/>
                                        </div>

                                        <div class="mb-3">
                                            <label class="control-label d-block text-start">@lang('Mô tả bước')</label>
                                            <textarea name="steps[{{ $loop->index }}][description]" class="form-control ckeditor visually-hidden" placeholder="@lang('Mô tả bước')">{{ $step->description }}</textarea>
                                        </div>
                                    </div>
                                @empty
                                    <p>@lang('Chưa có bước nào được thêm.')</p>
                                @endforelse
                            </div>
                            <div class="text-end mt-3">
                                <button type="button" id="add-step-btn" class="btn btn-primary">@lang('Thêm bước mới')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
