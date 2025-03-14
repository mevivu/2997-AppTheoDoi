@if (request()->routeIs('admin.question.iq'))
    <x-link target="_blank" :href="route('admin.question.editIq', $id)" :title="$question"/>
@elseif(request()->routeIs('admin.question.aq'))
    <x-link target="_blank" :href="route('admin.question.editEqAq', $id)" :title="$question"/>
@elseif(request()->routeIs('admin.question.eq'))
    <x-link target="_blank" :href="route('admin.question.editEqAq', $id)" :title="$question"/>
@endif
