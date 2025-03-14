@extends('admin.layouts.master')
@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.question.updateAqEq')" type="put" enctype="multipart/form-data" :validate="true" id="form_eq_aq">
                <x-input type="hidden" name="question[id]" :value="$response->id" />
                <div class="row justify-content-center">
                    @include('admin.question.edit.forms.edit-eq-aq-left')
                    @include('admin.question.edit.forms.edit-right')
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
