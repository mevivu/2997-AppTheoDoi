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
            <x-form :action="route('admin.clinic.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.clinic.forms.create-left')
                    @include('admin.clinic.forms.create-right')
                </div>
                @include('admin.forms.actions-fixed')
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
<script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.clinic.scripts.address')
@endpush
