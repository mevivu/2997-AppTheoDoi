@extends('admin.layouts.master')

@push('libs-css')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.subject.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.subject.forms.create-left')
                    @include('admin.subject.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
