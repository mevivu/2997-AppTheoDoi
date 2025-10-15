@extends('admin.layouts.master')

@push('libs-css')
    <link href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush

@push('custom-css')
    <style>
        .pac-container {
            z-index: 99999999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-form :action="route('admin.product.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.product.forms.create-left')
                    @include('admin.product.forms.create-right')
                </div>
                @include('admin.forms.actions-fixed')
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#brand_id').select2();
        });
    </script>
    @include('ckfinder::setup')
@endpush

@push('custom-js')
@endpush
