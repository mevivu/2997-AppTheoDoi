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
                icon="help"
                :title="__('Thêm Nhóm câu hỏi mới')"
                :subtitle="__('Khai báo nhóm câu hỏi đánh giá chỉ số EQ, IQ, AQ')"
                :back-route="route(RouteAdminSystem::QUESTION_GROUP_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::QUESTION_GROUP_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.question-group.forms.create-left')
                    @include('admin.question-group.forms.create-right')
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
@endpush
