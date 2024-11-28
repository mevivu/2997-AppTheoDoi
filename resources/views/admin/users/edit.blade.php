@extends('admin.layouts.master')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Sửa Thành viên') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.user.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$user->id" />
                <div class="row justify-content-center">

                    @include('admin.users.forms.edit-left', ['user' => $user])
                    @include('admin.users.forms.edit-right', ['user' => $user])
                </div>
            </x-form>
        </div>
    </div>
@endsection
@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('public/libs/numeral/numeral.min.js') }}"></script>

@endpush
@push('custom-js')
    @include('admin.layouts.modal.modal-pick-address')
    @include('admin.scripts.google-map-input')


@endpush
