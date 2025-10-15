@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-form :action="route('admin.quality.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$response->id" />
                <div class="row justify-content-center">
                    @include('admin.quality.forms.edit-left')
                    @include('admin.quality.forms.edit-right')
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
