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
                :title="__('Cập nhật Cấu hình Độ tuổi & Lưới thẻ')"
                :subtitle="$response->name"
                :back-route="route(RouteAdminSystem::MEMO_AGE_CONFIG_INDEX)"
            />

            <x-form :action="route('admin.memo-game.config.update')" type="put" :validate="true">
                <input type="hidden" name="id" value="{{ $response->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.memo-game.config.forms.edit-left')
                    @include('admin.memo-game.config.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
