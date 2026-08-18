@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="brain"
                :title="__('Chỉnh sửa Câu hỏi IQ')"
                :subtitle="__('Cập nhật nội dung câu hỏi, hình ảnh và danh sách đáp án IQ')"
                :back-route="route('admin.question.iq')"
            />

            <x-form :action="route('admin.question.updateIq')" enctype="multipart/form-data" type="put" :validate="true" id="form_iq">
                <x-input type="hidden" name="question[id]" :value="$response->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.question.edit.forms.edit-iq-left')
                    @include('admin.question.edit.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- ckfinder js -->
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
@endpush

@push('custom-js')
    @include('admin.question.scripts.scripts-edit-iq')
@endpush
