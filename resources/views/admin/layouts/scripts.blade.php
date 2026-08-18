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
<script src="https://cdn.jsdelivr.net/npm/redoc@2.2.0/bundles/redoc.standalone.min.js"></script>


@stack('libs-js')
<script type="module" src="{{ asset('public/admin/assets/js/i18n.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/setup.js') }}?v=1.0.3"></script>
<script src="{{ asset('/public/libs/firebase/firebase.js') }}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&language=vi&callback=initMaps" async defer></script>
<script>
    function initMaps() {
        try {
            if (typeof initMap === 'function') {
                initMap();
            }
            // if (typeof initEndMap === 'function') {
            //     initEndMap();
            // }

        } catch (error) {
            handleAjaxError();
            window.location.reload();
        }
    }

</script>

<script>
    // Back to top functionality
    document.addEventListener('DOMContentLoaded', function() {
        const backToTop = document.querySelector('.back-to-top');

        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        // Smooth scroll to top when clicked
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
<script>
    window.adminPwaConfig = {
        swUrl: @json(route('admin.service-worker')),
        scope: @json(parse_url(url('/admin'), PHP_URL_PATH).'/')
    };
</script>
<script src="{{ asset('public/admin/assets/js/admin-pwa.js') }}"></script>
@stack('custom-js')
