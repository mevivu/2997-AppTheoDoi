@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
    @include('admin.memo-game.card.css.card-filter')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="cloud-upload"
                :title="__('Tải lên hàng loạt Thẻ bài Memo Game')"
                :subtitle="__('Thêm nhanh nhiều thẻ bài cho chủ đề bằng cách tải lên nhiều ảnh cùng lúc')"
                :back-route="route(RouteAdminSystem::MEMO_CARD_INDEX)"
            />

            <x-form :action="route('admin.memo-game.card.bulkStore')" type="post" :validate="true" :has-file="true" enctype="multipart/form-data">
                <div class="row g-4 justify-content-center">
                    @include('admin.memo-game.card.forms.bulk-create')
                </div>
            </x-form>
        </div>
    </div>
@endsection
