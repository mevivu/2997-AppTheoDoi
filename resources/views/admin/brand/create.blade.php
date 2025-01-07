@extends('admin.layouts.master')

@push('libs-css')
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
        <div class="container-xl">
            <x-form :action="route('admin.brand.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.brand.forms.create-left')  <!-- Include form left section -->
                    @include('admin.brand.forms.create-right') <!-- Include form right section -->
                </div>
                @include('admin.forms.actions-fixed')  <!-- Include action buttons -->
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.brand.scripts.script') <!-- Include custom scripts -->
@endpush
