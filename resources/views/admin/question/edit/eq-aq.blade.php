@php
    use App\Traits\RouteAdminSystem;
    use App\Enums\Question\QuestionType;
    $isEq = $response->question_type == QuestionType::EQ;
    $title = $isEq ? __('Chỉnh sửa Câu hỏi EQ') : __('Chỉnh sửa Câu hỏi AQ');
    $subtitle = $isEq ? __('Cập nhật nội dung câu hỏi đánh giá chỉ số cảm xúc EQ') : __('Cập nhật nội dung câu hỏi đánh giá chỉ số vượt khó AQ');
    $icon = $isEq ? 'heart' : 'leaf';
    $backRoute = $isEq ? route('admin.question.eq') : route('admin.question.aq');
@endphp
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
                :icon="$icon"
                :title="$title"
                :subtitle="$subtitle"
                :back-route="$backRoute"
            />

            <x-form :action="route('admin.question.updateAqEq')" type="put" enctype="multipart/form-data" :validate="true" id="form_eq_aq">
                <x-input type="hidden" name="question[id]" :value="$response->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.question.edit.forms.edit-eq-aq-left')
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
    @include('admin.question.scripts.scripts-eq-aq')
@endpush
