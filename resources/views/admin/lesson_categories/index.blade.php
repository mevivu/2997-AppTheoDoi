@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Danh sách danh mục bài học')"
                                     :subtitle="__('Quản lý danh mục bài học theo độ tuổi và 5 trụ cột (PQ, IQ, EQ, AQ, Thai giáo)')"
                                     icon="ti ti-folder"
                                     :addRoute="route('admin.lesson_category.create')"
                                     :addText="__('Thêm danh mục mới')" />
                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.lesson_category.multiple')" type="post" :validate="true">
                        <div class="table-responsive position-relative">
                            <x-admin.partials.toggle-column-datatable />
                            @isset($actionMultiple)
                                <x-admin.partials.select-action-multiple :actionMultiple="$actionMultiple" />
                            @endisset
                            {{ $dataTable->table(['class' => 'table table-bordered table-striped align-middle'], true) }}
                        </div>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
@endpush
