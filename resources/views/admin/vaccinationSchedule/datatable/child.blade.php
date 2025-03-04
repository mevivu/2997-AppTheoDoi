@if ($child)
    <x-link :href="route('admin.children.edit', $child->id)" :title="$child->fullname"/>
@else
    <span>N/A</span>
@endif
