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
                icon="ti ti-book-upload"
                :title="__('Thêm Bài học giáo dục mới')"
                :subtitle="__('Soạn thảo bài học, thiết lập độ khó, tần suất và đính kèm video hướng dẫn')"
                :back-route="route(RouteAdminSystem::LESSON_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::LESSON_STORE)" type="post" :validate="true" :has-files="true" id="lesson_form">
                <div class="row g-4 justify-content-center">
                    @include('admin.lessons.forms.create-left')
                    @include('admin.lessons.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
@endpush
