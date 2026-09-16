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
                icon="adjustments"
                :title="__('Thêm Cấu hình Độ tuổi & Lưới thẻ mới')"
                :subtitle="__('Thiết lập quy chuẩn bài test lật thẻ cho nhóm độ tuổi')"
                :back-route="route(RouteAdminSystem::MEMO_AGE_CONFIG_INDEX)"
            />

            <x-form :action="route('admin.memo-game.config.store')" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.memo-game.config.forms.create-left')
                    @include('admin.memo-game.config.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
