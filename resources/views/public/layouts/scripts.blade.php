<script src="{{ asset('public/assets/js/jquery-3.6.4.min.js') }}"></script>
<script src="{{ asset('public/libs/tabler/dist/js/tabler.min.js') }}"></script>
<script src="{{ asset('public/assets/js/jquery-ui.1.12.1.js') }}"></script>
<!-- datatables -->
<script src="{{ asset('/public/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/public/libs/datatables/plugins/bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('/public/libs/datatables/plugins/buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('/public/libs/datatables/plugins/buttons/js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('public/libs/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
<script src="{{ asset('public/libs/Parsley.js-2.9.2/parsley.min.js') }}"></script>
<script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>


@stack('libs-js')

<script type="module" src="{{ asset('public/admin/assets/js/i18n.js') }}"></script>
<script src="{{ asset('public/assets/js/scripts.js') }}"></script>



@stack('custom-js')
