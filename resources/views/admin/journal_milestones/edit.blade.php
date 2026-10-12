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
                icon="ti ti-flag"
                :title="__('Cập nhật cột mốc gợi ý')"
                :subtitle="__('Chỉnh sửa thông tin cột mốc phát triển')"
                :back-route="route(RouteAdminSystem::JOURNAL_MILESTONE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::JOURNAL_MILESTONE_UPDATE)" type="put" :validate="true">
                <input type="hidden" name="id" value="{{ $response->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.journal_milestones.forms.edit-left')
                    @include('admin.journal_milestones.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
