@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="vaccine"
                :title="__('Thêm Lịch tiêm chủng mới')"
                :subtitle="__('Nhập thông tin chi tiết về mũi tiêm và độ tuổi khuyến nghị')"
                :back-route="request()->back == 'user' ? route('admin.vaccination.user') : route('admin.vaccination.admin')"
            />

            <x-form :action="route('admin.vaccination.store')" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.vaccinationSchedule.forms.create-left')
                    @include('admin.vaccinationSchedule.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <!-- button in datatable -->
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.vaccinationSchedule.scripts.scripts')
@endpush
