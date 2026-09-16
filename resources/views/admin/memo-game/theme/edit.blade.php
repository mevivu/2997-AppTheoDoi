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
                icon="palette"
                :title="__('Cập nhật Chủ đề Memo Game')"
                :subtitle="$response->name"
                :back-route="route(RouteAdminSystem::MEMO_THEME_INDEX)"
            />

            <x-form :action="route('admin.memo-game.theme.update')" type="put" :validate="true" :has-file="true" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{ $response->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.memo-game.theme.forms.edit-left')
                    @include('admin.memo-game.theme.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
