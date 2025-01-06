<div class="col-12 col-md-3">

    <div class="card mb-3">
        <div class="card-header">
            {{ __('Hoạt động') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                                 class="flex-column gap-1 text-wrap p-2 flex-grow-1"/>
                @if (request()->back == 'admin')
                    <x-link :href="route('admin.vaccination.admin')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @elseif(request()->back == 'user')
                    <x-link :href="route('admin.vaccination.user')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @else
                    <x-link :href="route('admin.vaccination.admin')" class="btn btn-outline w-50">
                        {{ __('Quay lại') }}
                    </x-link>
                @endif

            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-photo me-1"></span>
            @lang('avatar')
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="image" :value="old('image')" showImage="featureImage"/>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-typography"></span>
            @lang('loại')
        </div>
        <div class="card-body p-2">
            <x-select name="type" :required="true">
                @foreach ($type as $key => $value)
                    <x-select-option :value="$key" :title="$value"/>
                @endforeach
            </x-select>
        </div>
    </div>
</div>
