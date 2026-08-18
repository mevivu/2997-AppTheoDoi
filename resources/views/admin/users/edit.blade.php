@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="user-edit"
                :title="__('Chỉnh sửa Khách hàng')"
                :subtitle="$user->fullname ?? __('Cập nhật thông tin chi tiết và trạng thái tài khoản khách hàng')"
                :back-route="route(RouteAdminSystem::USER_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::USER_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$user->id" />
                <div class="row g-4 justify-content-center">
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
