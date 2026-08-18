<x-admin.datatable.action-group>
    @if(isset($type) && $type == \App\Enums\Question\QuestionType::IQ)
        <x-admin.datatable.action-edit :href="route('admin.question.editIq', $id)" />
    @else
        <x-admin.datatable.action-edit :href="route('admin.question.editEqAq', $id)" />
    @endif
    <x-admin.datatable.action-delete :route="route('admin.question.delete', $id)" />
</x-admin.datatable.action-group>
