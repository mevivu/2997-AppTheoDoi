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
                :title="__('Thêm cột mốc gợi ý mới')"
                :subtitle="__('Nhập thông tin cột mốc phát triển theo giai đoạn của trẻ')"
                :back-route="route(RouteAdminSystem::JOURNAL_MILESTONE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::JOURNAL_MILESTONE_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.journal_milestones.forms.create-left')
                    @include('admin.journal_milestones.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
