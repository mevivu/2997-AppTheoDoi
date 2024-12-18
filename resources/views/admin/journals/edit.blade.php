@extends('admin.layouts.master')
@push('libs-css')
<link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form id="notificationForm" :action="route('admin.children.update')" type="put" :validate="true">
                <div class="row justify-content-center">
                    <input type="hidden" name="id" value="{{ $children->id }}">
                    @include('admin.children.forms.edit-left',['children' => $children ])
                    @include('admin.children.forms.edit-right',['children' => $children ])

                </div>
                @include('admin.forms.actions-fixed')
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <!-- button in datatable -->
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush

@push('custom-js')
@include('admin.children.scripts.scripts')
@endpush
