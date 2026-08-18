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
                icon="brain"
                :title="__('Chỉnh sửa Câu hỏi Trắc nghiệm')"
                :subtitle="__('Cập nhật nội dung câu hỏi và các đáp án')"
                :back-route="route($route)"
            />

            <x-form :action="route('admin.quiz.updateIQ')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$instance->id"/>
                <x-input type="hidden" name="type" :value="$instance->type->value"/>
                <div class="row g-4 justify-content-center">
                    @include('admin.quiz.forms.edit-left')
                    @include('admin.quiz.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('public/libs/sortable/Sortable.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.quiz.scripts.edit-iq')
@endpush
