@php
    $formAttrs = ['action' => $action, 'method' => $marcoMethod()];
    if ($hasFile || $attributes->get('has-file') || $attributes->get('has_file') || $attributes->get('has-files') || $attributes->get('has_files') || $attributes->get('enctype') === 'multipart/form-data') {
        $formAttrs['enctype'] = 'multipart/form-data';
    }
@endphp
<form {{ $attributes->merge($formAttrs) }} {{ $isValidate() }}>
    
    @unless($type == 'GET')

        @csrf

        @unless($type == 'POST')

            @method($type)
            
        @endunless

    @endunless

    {{ $slot }}

</form>