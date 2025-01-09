<div class="col-12 col-md-3">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin hướng dẫn') }}</h2>
        </div>
        <div class="row card-body">

            <!-- Hành động -->
            <div class="col-12">
                <div class="mb-3">
                    <div class="w-100 d-flex align-items-center h-100 gap-2">
                        <x-button.submit :title="__('save')" name="submitter" value="save"
                                         class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                        <x-link :href="route('admin.guide.index')" class="w-50 btn btn-outline"
                                :title="'Quay lại'" />
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    @lang('type')
                </div>
                <div class="card-body p-2">
                    <x-select name="type" :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option
                                :value="$key"
                                :title="$value"
                                :selected="$instance->type->value == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    @lang('status')
                </div>
                <div class="card-body p-2">
                    <x-select name="status" :required="true">
                        @foreach ($status as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$instance->status->value == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>
        </div>
    </div>
</div>
