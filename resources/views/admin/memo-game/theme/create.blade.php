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
                :title="__('Thêm Chủ đề Memo Game mới')"
                :subtitle="__('Khai báo chủ đề bài test lật thẻ (Xe, Hoa, Số, Cờ...)')"
                :back-route="route(RouteAdminSystem::MEMO_THEME_INDEX)"
            />

            <x-form :action="route('admin.memo-game.theme.store')" type="post" :validate="true" :has-file="true" enctype="multipart/form-data">
                <div class="row g-4 justify-content-center">
                    @include('admin.memo-game.theme.forms.create-left')
                    @include('admin.memo-game.theme.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
