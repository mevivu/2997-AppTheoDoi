<div class="d-flex align-items-center ">
    <a target="_blank" href="{{ route('admin.bmi.edit', $id) }}"
       class="btn btn-icon btn-primary">
        <i class="ti ti-pencil"></i>
    </a>
    <x-button.modal-delete class="btn-icon m-lg-2"
                           data-route="{{ route('admin.bmi.delete', $id) }}">
        <i class="ti ti-trash"></i>
    </x-button.modal-delete>

</div>
