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
            <x-form :action="route('admin.vaccination.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.vaccinationSchedule.forms.create-left')
                    @include('admin.vaccinationSchedule.forms.create-right')
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
@endpush

@push('custom-js')

@endpush