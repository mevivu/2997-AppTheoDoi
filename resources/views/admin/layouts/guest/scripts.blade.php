<script src="{{ asset('public/libs/tabler/dist/js/tabler.min.js') }}" defer></script>
<script src="{{ asset('public/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('public/libs/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
<script src="{{ asset('public/libs/Parsley.js-2.9.2/parsley.min.js') }}"></script>

@stack('libs-js')
<script>
    window.adminPwaConfig = {
        swUrl: @json(route('admin.service-worker')),
        scope: @json(parse_url(url('/admin'), PHP_URL_PATH).'/')
    };
</script>
<script src="{{ asset('public/admin/assets/js/admin-pwa.js') }}"></script>
@stack('custom-js')