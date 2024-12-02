@extends('admin.layouts.master')
@push('libs-css')
<link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form id="notificationForm" :action="route('admin.children.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    <input type="hidden" name="device_token" value="">
                    @include('admin.children.forms.create-left')
                    @include('admin.children.forms.create-right')

                </div>
                @include('admin.forms.actions-fixed')
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- button in datatable -->
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
    <script src="{{ asset('/public/libs/firebase/firebase.js') }}"></script>
@endpush

@push('custom-js')
@include('admin.children.scripts.scripts')
@endpush
