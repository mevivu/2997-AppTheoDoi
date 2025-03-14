@foreach ($parents_name as $parent)
    <x-link target="_blank" :href="route('admin.post_category.edit', $parent->id)" :title="$parent->name"/>
@endforeach
