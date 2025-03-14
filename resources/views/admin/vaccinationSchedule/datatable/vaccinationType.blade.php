@if ($vaccinationType)
    <x-link target="_blank" :href="route('admin.vaccinationType.edit', $vaccinationType->id)"
            :title="$vaccinationType->name"/>
@else
    <span>N/A</span>
@endif
