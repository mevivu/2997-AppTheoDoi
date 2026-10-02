@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="ti ti-category"
                :title="__('Danh mục Chuyên đề Góc Chuyên gia')"
                :subtitle="__('Quản lý các nhóm kiến thức như Dinh dưỡng & Thể chất, Vận động, Tâm lý mầm non...')"
                :add-route="route(RouteAdminSystem::EXPERT_CATEGORY_CREATE)"
                :add-title="__('Thêm chuyên mục mới')"
            />

            <div class="card custom-shadow">
                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.expert_category.multiple')" type="post" :validate="true">
                        <div class="table-responsive position-relative">
                            <x-admin.partials.toggle-column-datatable />
                            @isset($actionMultiple)
                                <x-admin.partials.select-action-multiple :actionMultiple="$actionMultiple" />
                            @endisset
                            {{ $dataTable->table(['class' => 'table table-bordered'], true) }}
                        </div>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
@endpush
