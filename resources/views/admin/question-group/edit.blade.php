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
                :title="__('Chỉnh sửa Nhóm câu hỏi')"
                :subtitle="$response->name ?? __('Cập nhật thông tin chi tiết nhóm câu hỏi')"
                :back-route="route(RouteAdminSystem::QUESTION_GROUP_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::QUESTION_GROUP_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$response->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.question-group.forms.edit-left')
                    @include('admin.question-group.forms.edit-right')
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
