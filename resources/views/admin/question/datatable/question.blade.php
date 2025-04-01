@if (request()->routeIs('admin.question.iq'))
    <x-link target="_blank" :href="route('admin.question.editIq', $id)" :title="$question"/>
@elseif(request()->routeIs('admin.question.aq'))
    <x-link target="_blank" :href="route('admin.question.editEqAq', $id)" :title="$question"/>
@elseif(request()->routeIs('admin.question.eq'))
    <x-link target="_blank" :href="route('admin.question.editEqAq', $id)" :title="$question"/>
@endif
<i class="ti ti-copy copy-btn" style="font-size: 18px; cursor: pointer;" data-value="{{ $question }}"></i>
<i class="ti ti-check check-icon" style="display:none; font-size: 18px"></i>
