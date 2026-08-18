@php use App\Enums\Question\QuestionType; @endphp
<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">

            <!-- type -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('type') <span class="text-danger">*</span></label>
                    <x-select name="type" disabled :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$instance->type->value == $key"/>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('title') <span class="text-danger">*</span></label>
                    <x-input type="text" name="title" :value="$instance->title" :required="true"
                             :placeholder="__('title')"/>
                </div>
            </div>
            @if ($instance->type == QuestionType::IQ->value)
                <!-- age -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="control-label">@lang('age') <span class="text-danger">*</span></label>
                        <x-input type="text" name="age" :value="$instance->age" :required="true"
                                 :placeholder="__('age')"/>
                    </div>
                </div>
            @endif

            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('description')</label>
                    <textarea name="description" class="form-control" rows="4"
                              placeholder="{{ __('description') }}">{{ $instance->description }}</textarea>
                </div>
            </div>

            <input type="hidden" id="type-select" value="{{ $selectedType }}">

            <div class="container mt-4">
                <div class="card">
                    <div class="card-body">
                        <!-- Search Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="search-keyword" class="form-label">Từ khoá</label>
                                    <div class="input-group">
                                        <input type="text" id="search-keyword"
                                               class="form-control"
                                               placeholder="Nhập từ khoá để tìm kiếm"
                                               aria-label="Search keyword"
                                               aria-describedby="button-addon">
                                        <input type="number"
                                               id="search-age"
                                               class="form-control"
                                               placeholder="Tuổi"
                                               aria-label="Search age"
                                               min="1"
                                               max="100"
                                               style="flex: 0 0 100px; max-width: 100px;">
                                        <button class="btn btn-primary" type="button" id="search-button">Tìm kiếm</button>
                                        <button class="btn btn-secondary" type="button" id="clear-button">Reload</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Count Checked Questions -->
                        <div class="row">
                            <div class="col-12">
                                <div id="count-checked" class="mb-3">
                                    Các câu hỏi được chọn: <span id="checked-count">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Display -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-list-check me-2 fs-4"></i>
                                            <h5 class="mb-0">Danh sách câu hỏi</h5>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light text-primary" id="toggle-question-panel">
                                            Ẩn
                                        </button>
                                    </div>
                                    <div class="card-body" id="question-panel-body">
                                        <div id="loading" class="text-center" style="display: none;">
                                            <div class="loading-circles">
                                                <div class="circle"></div>
                                                <div class="circle"></div>
                                                <div class="circle"></div>
                                            </div>
                                        </div>
                                        <div id="questions-container" class="mb-3"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12 mt-2">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-checks me-2 fs-4"></i>
                                            <h5 class="mb-0">Câu hỏi đã chọn</h5>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light text-success" id="toggle-selected-panel">
                                            Ẩn
                                        </button>
                                    </div>

                                    <div class="card-body" id="selected-panel-body">
                                        <div id="loading-indicator" class="text-center" style="display: none;">
                                            <div class="loading-circles">
                                                <div class="circle"></div>
                                                <div class="circle"></div>
                                                <div class="circle"></div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="selected_questions_input" name="selected_questions">
                                        <ul id="selected-questions" class="list-group list-group-flush"></ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
