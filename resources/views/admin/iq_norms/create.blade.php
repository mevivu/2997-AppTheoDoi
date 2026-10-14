@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-form :action="route('admin.iq_norm.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.iq_norms.forms.create-left')
                    @include('admin.iq_norms.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
