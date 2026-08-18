@php
    use App\Traits\RouteAdminSystem;
    $isEq = request()->routeIs('admin.question.createEq');
    $title = $isEq ? __('Thêm mới Câu hỏi EQ') : __('Thêm mới Câu hỏi AQ');
    $subtitle = $isEq ? __('Cấu hình câu hỏi đánh giá chỉ số cảm xúc EQ và thang điểm') : __('Cấu hình câu hỏi đánh giá chỉ số vượt khó AQ và thang điểm');
    $icon = $isEq ? 'heart' : 'leaf';
    $backRoute = $isEq ? route('admin.question.eq') : route('admin.question.aq');
@endphp
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
                :icon="$icon"
                :title="$title"
                :subtitle="$subtitle"
                :back-route="$backRoute"
            />

            <x-form :action="route('admin.question.storeAqEq')" type="post" :validate="true" id="form_eq_aq" enctype="multipart/form-data">
                <div class="row g-4 justify-content-center">
                    @include('admin.question.create.forms.create-eq-aq-left')
                    @include('admin.question.create.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- ckfinder js -->
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
@endpush

@push('custom-js')
    @include('admin.question.scripts.scripts-eq-aq')
@endpush
