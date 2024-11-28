@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Danh sách Quyền') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Danh sách Quyền') }}</h2>
                    <x-link :href="route('admin.permission.create')" class="btn btn-primary"><i
                            class="ti ti-plus"></i>{{ __('Thêm Quyền') }}</x-link>
                </div>
                <div class="card-body">
                    <p><strong>Lưu ý</strong>: Đây là phần chỉ dành riêng cho Nhà phát triển. Các Dev sẽ sử dụng Slug (
                        Permission_name ) để lập trình, đóng gói các chức năng để có thể phân quyền. Vui lòng <b>không xóa
                            hoặc điều chỉnh</b> các Quyền nếu bạn không phải Dev hoặc không biết về nó để tránh bị Lỗi toàn
                        bộ hệ thống. </p>
                    <div class="card-body">
                        <x-form id="formMultiple" :action="route('admin.permission.multiple')" type="post" :validate="true">
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
