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
                icon="ti ti-video"
                :title="__('Chỉnh sửa Video giáo dục')"
                :subtitle="$instance->title ?? __('Cập nhật thông tin video')"
                :back-route="route(RouteAdminSystem::VIDEO_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::VIDEO_UPDATE)" type="put" :validate="true" :has-files="true">
                <x-input type="hidden" name="id" :value="$instance->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.videos.forms.edit-left')
                    @include('admin.videos.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush
