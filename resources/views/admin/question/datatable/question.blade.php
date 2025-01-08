@if (request()->routeIs('admin.question.iq'))
    <x-link :href="route('admin.question.editIq', $id)" :title="$question" />
@elseif(request()->routeIs('admin.question.aq'))
    <x-link :href="route('admin.question.editEqAq', $id)" :title="$question" />
@elseif(request()->routeIs('admin.question.eq'))
    <x-link :href="route('admin.question.editEqAq', $id)" :title="$question" />
@endif
