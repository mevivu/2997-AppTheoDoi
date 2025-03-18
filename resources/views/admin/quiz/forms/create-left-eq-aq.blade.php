<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ $title }}</h2>
        </div>
        <div class="row card-body ">

            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('title')</label>
                    <x-input name="title" :value="old('title')" :required="true" :placeholder="__('title')" />
                </div>
            </div>

            @if (request()->routeIs('admin.quiz.createEq'))
                <!-- age -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="control-label">@lang('age')</label>
                        <x-input name="age" type="number" :value="old('age')" :required="true" :placeholder="__('age')" />
                    </div>
                </div>
            @endif


            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('description')</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="{{ __('description') }}">{{ old('description') }}</textarea>
                </div>
            </div>
            <input type="hidden" id="type-select" value="{{ $selectedType }}">


            <div class="container mt-4">
                <div class="card">
                    <div class="card-body">
                        <!-- Search Section -->
                        <label for="search-keyword" class="form-label">Chọn độ tuổi</label>

                        <x-select id="filter_age" class="mb-2" >
                            <!-- Default "Chọn độ tuổi" option when no instance is available -->
                            <x-select-option value="" title="Chọn độ tuổi" :selected="!isset($instance)"/>

                            <!-- Loop through the age_group enum and mark the correct value as selected if instance exists -->
                            @foreach ($age_group as $key => $value)
                                <x-select-option
                                    :value="$key"
                                    :title="$value"
                                    :selected="isset($instance) && $instance->age_group->value == $key" />
                            @endforeach
                        </x-select>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="search-keyword" class="form-label">Từ khoá</label>
                                    <div class="input-group">
                                        <input type="text" id="search-keyword" class="form-control"
                                               placeholder="Nhập từ khoá để tìm kiếm" aria-label="Search keyword"
                                               aria-describedby="button-addon">
                                        <button class="btn btn-primary" type="button" id="search-button">Tìm kiếm
                                        </button>
                                        <button class="btn btn-secondary" type="button" id="clear-button">Reload
                                        </button>
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
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="mb-0">Danh sách câu hỏi</h4>
                                    </div>
                                    <div class="card-body">
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

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="mb-0">Câu hỏi đã chọn</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="loading-indicator" style="display: none;" class="text-center">
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
