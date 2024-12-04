@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form  :action="route('admin.weight-height-who.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.WeightHeightWho.forms.create-left')
                    @include('admin.WeightHeightWho.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
@endpush

@push('custom-js')
@endpush
