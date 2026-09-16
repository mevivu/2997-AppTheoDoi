@php
    $formAttrs = ['action' => $action, 'method' => $marcoMethod()];
    if ($hasFile || $attributes->get('has-file') || $attributes->get('has_file')) {
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