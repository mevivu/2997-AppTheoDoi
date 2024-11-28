@extends('admin.layouts.master')
@push('libs-css')

@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.notification.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$notification->id" />
                <div class="row justify-content-center">
                    @include('admin.notifications.forms.edit-left')
                    @include('admin.notifications.forms.edit-right')
                </div>
                @include('admin.forms.actions-fixed')
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- button in datatable -->
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>

@endpush

@push('custom-js')
@include('admin.notifications.scripts.scripts')
@endpush
