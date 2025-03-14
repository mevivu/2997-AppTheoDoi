@extends('admin.layouts.master')
@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.question.storeIq')" type="post" :validate="true" id="form_iq" enctype="multipart/form-data">
                <div class="row justify-content-center">
                    @include('admin.question.create.forms.create-iq-left')
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
    @include('admin.question.scripts.scripts-create-iq')
@endpush
