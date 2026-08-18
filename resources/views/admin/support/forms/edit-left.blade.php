<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            @if ($response->type->value == \App\Enums\Support\SupportType::HelpCenter->value)
                <h2 class="mb-0">{{ __('Trung tâm trợ giúp') }}</h2>
            @else
                <h2 class="mb-0">{{ __('Hướng dẫn sử dụng') }}</h2>
            @endif
        </div>
        <div class="row card-body">

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('title') }}: <span class="text-danger">*</span></label>
                    <x-input type="text" name="title" :value="$response->title" :required="true" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('content') }}</label>
                    <textarea name="content" class="ckeditor visually-hidden">{{ $response->content }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
