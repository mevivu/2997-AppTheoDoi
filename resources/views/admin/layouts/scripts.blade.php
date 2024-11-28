<script src="{{ asset('public/libs/tabler/dist/js/tabler.min.js') }}" defer></script>
<script src="{{ asset('public/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('public/libs/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
<script src="{{ asset('public/libs/Parsley.js-2.9.2/parsley.min.js') }}"></script>
<!-- datatables -->
<script src="{{ asset('/public/libs/datatables/jquery.dataTables.min.js') }}"></script>

<script src="{{ asset('/public/libs/datatables/plugins/bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<script src="{{ asset('/public/libs/datatables/plugins/buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('/public/libs/datatables/plugins/buttons/js/buttons.bootstrap5.min.js') }}"></script>

<script src="{{ asset('/public/libs/datatables/plugins/responsive/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ asset('/public/libs/datatables/plugins/responsive/js/responsive.bootstrap5.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@stack('libs-js')
<script type="module" src="{{ asset('public/admin/assets/js/i18n.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/setup.js') }}"></script>
<script src="{{ asset('/public/libs/firebase/firebase.js') }}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&language=vi&callback=initMaps" async defer></script>
<script>
    function initMaps() {
        try {
            if (typeof initMap === 'function') {
                initMap();
            }
            if (typeof initEndMap === 'function') {
                initEndMap();
            }

        } catch (error) {
            handleAjaxError();
            window.location.reload();
        }
    }

</script>
@stack('custom-js')
