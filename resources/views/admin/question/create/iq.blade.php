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
                :title="__('Thêm mới Câu hỏi IQ')"
                :subtitle="__('Cấu hình nội dung câu hỏi trắc nghiệm IQ, hình ảnh minh họa và danh sách đáp án')"
                :back-route="route('admin.question.iq')"
            />

            <x-form :action="route('admin.question.storeIq')" type="post" :validate="true" id="form_iq" enctype="multipart/form-data">
                <div class="row g-4 justify-content-center">
                    @include('admin.question.create.forms.create-iq-left')
                    @include('admin.question.create.forms.create-right')
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
    @include('admin.question.scripts.scripts-create-iq')
@endpush
