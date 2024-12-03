<div class="col-12 col-md-3">

    <div class="card mb-3">
        <div class="card-header">
            {{ __('Hoạt động') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                    class="flex-column gap-1 text-wrap p-2 flex-grow-1" />
                <x-link :href="route('admin.clinicType.index')" class="w-50 btn btn-outline" :title="'Quay lại'" />
            </div>
        </div>
    </div>

</div>
