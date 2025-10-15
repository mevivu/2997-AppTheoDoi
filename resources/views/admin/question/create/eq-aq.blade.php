@extends('admin.layouts.master')
@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-form :action="route('admin.question.storeAqEq')" type="post" :validate="true" id="form_eq_aq" enctype="multipart/form-data">
                <div class="row justify-content-center">
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

    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
@endpush

@push('custom-js')
    @include('admin.question.scripts.scripts-eq-aq')
@endpush
