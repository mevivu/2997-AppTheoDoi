@php
    $isIq = request()->routeIs('admin.question.iq') 
        || (isset($question_type) && ($question_type == \App\Enums\Question\QuestionType::IQ || (is_object($question_type) && $question_type->value == 'iq') || $question_type == 'iq'))
        || (isset($type) && ($type == \App\Enums\Question\QuestionType::IQ || (is_object($type) && $type->value == 'iq') || $type == 'iq'));
@endphp
<x-admin.datatable.action-group>
    @if($isIq)
        <x-admin.datatable.action-edit :href="route('admin.question.editIq', $id)" />
    @else
        <x-admin.datatable.action-edit :href="route('admin.question.editEqAq', $id)" />
    @endif
    <x-admin.datatable.action-delete :route="route('admin.question.delete', $id)" />
</x-admin.datatable.action-group>
