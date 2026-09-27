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
                icon="ti ti-edit"
                :title="__('Chỉnh sửa Bài học giáo dục')"
                :subtitle="__('Cập nhật nội dung, độ khó và video của bài: ') . $instance->name"
                :back-route="route(RouteAdminSystem::LESSON_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::LESSON_UPDATE)" type="put" :validate="true" :has-files="true" id="lesson_form">
                <input type="hidden" name="id" value="{{ $instance->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.lessons.forms.edit-left')
                    @include('admin.lessons.forms.edit-right')
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
