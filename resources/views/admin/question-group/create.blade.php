@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.question-group.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.question-group.forms.create-left')
                    @include('admin.question-group.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection


@push('libs-js')
    <!-- ckfinder js -->
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush

@push('custom-js')
@endpush
