@extends('admin.layouts.master')
@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.quiz.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$instance->id"/>
                <x-input type="hidden" name="type" :value="$instance->type->value"/>
                <div class="row justify-content-center">
                    @include('admin.quiz.forms.edit-left')
                    @include('admin.quiz.forms.edit-right')
                </div>
                @include('admin.forms.actions-fixed')
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
@endpush

@push('custom-js')
    <script>
        $(document).ready(function() {
            function updateCheckedCount() {
                const count = $('input[name="question_ids[]"]:checked').length;
                $('#checked-count').text(count);
            }

            updateCheckedCount();
        });
    </script>

@endpush
