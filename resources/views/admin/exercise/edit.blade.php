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
                icon="stretching"
                :title="__('Chỉnh sửa Bài tập')"
                :subtitle="$response->name ?? __('Cập nhật thông tin chi tiết bài tập')"
                :back-route="$response->exercise_type == \App\Enums\Exercise\ExerciseType::POWER ? route('admin.exercise.power') : route('admin.exercise.physical')"
            />

            <x-form :action="route(RouteAdminSystem::EXERCISE_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$response->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.exercise.forms.edit-left')
                    @include('admin.exercise.forms.edit-right')
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
