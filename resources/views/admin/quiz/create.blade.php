@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
    <style>
        .pac-container {
            z-index: 99999999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="brain"
                :title="__('Thêm Câu hỏi Trắc nghiệm IQ mới')"
                :subtitle="__('Nhập thông tin câu hỏi và các phương án trả lời')"
                :back-route="route($route)"
            />

            <x-form :action="route('admin.quiz.storeIQ')" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.quiz.forms.create-left')
                    @include('admin.quiz.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.quiz.scripts.create-iq')
@endpush
