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
                icon="notebook"
                :title="__('Chỉnh sửa Nhật ký / Đơn thuốc')"
                :subtitle="__('Cập nhật thông tin chi tiết nhật ký')"
                :back-route="$response->exercise_type == \App\Enums\Journal\JournalType::Moment ? route('admin.journal.moment') : route('admin.journal.prescription')"
            />

            <x-form id="notificationForm" :action="route(RouteAdminSystem::JOURNAL_UPDATE)" type="put" :validate="true">
                <input type="hidden" name="id" value="{{ $response->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.journals.forms.edit-left')
                    @include('admin.journals.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.journals.scripts.scripts')
@endpush
