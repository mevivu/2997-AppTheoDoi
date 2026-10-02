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
                icon="user-plus"
                :title="__('Thêm Chuyên gia / Bác sĩ mới')"
                :subtitle="__('Nhập thông tin cá nhân, chức danh, bệnh viện và tiểu sử công tác')"
                :back-route="route(RouteAdminSystem::EXPERT_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::EXPERT_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.experts.forms.create-left')
                    @include('admin.experts.forms.create-right')
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
