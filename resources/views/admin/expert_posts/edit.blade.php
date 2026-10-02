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
                icon="stethoscope"
                :title="__('Chỉnh sửa Bài viết Chuyên gia')"
                :subtitle="$instance->title"
                :back-route="route(RouteAdminSystem::EXPERT_POST_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::EXPERT_POST_UPDATE)" type="put" :validate="true">
                <input type="hidden" name="id" value="{{ $instance->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.expert_posts.forms.edit-left')
                    @include('admin.expert_posts.forms.edit-right')
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
