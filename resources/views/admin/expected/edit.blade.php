@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.expected.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$response->id" />
                <div class="row justify-content-center">
                    @include('admin.expected.forms.edit-left')
                    @include('admin.expected.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
