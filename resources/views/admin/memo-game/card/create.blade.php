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
                icon="cards"
                :title="__('Thêm Thẻ bài Memo Game mới')"
                :subtitle="__('Tạo thẻ mới cho chủ đề tương ứng (kèm hình ảnh & phát âm nếu có)')"
                :back-route="route(RouteAdminSystem::MEMO_CARD_INDEX)"
            />

            <x-form :action="route('admin.memo-game.card.store')" type="post" :validate="true" :has-file="true" enctype="multipart/form-data">
                <div class="row g-4 justify-content-center">
                    @include('admin.memo-game.card.forms.create-left')
                    @include('admin.memo-game.card.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
