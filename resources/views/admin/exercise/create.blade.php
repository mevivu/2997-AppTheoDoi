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
                :title="__('Thêm Bài tập mới')"
                :subtitle="__('Nhập thông tin bài tập rèn luyện thể chất và phát triển năng lực cho trẻ')"
                :back-route="request()->back == 'power' ? route('admin.exercise.power') : route('admin.exercise.physical')"
            />

            <x-form :action="route(RouteAdminSystem::EXERCISE_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.exercise.forms.create-left')
                    @include('admin.exercise.forms.create-right')
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
