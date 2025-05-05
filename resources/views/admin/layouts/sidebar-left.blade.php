<!-- Sidebar -->
<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        @php
            $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
            $settings = $settingRepository->getAll();
        @endphp
        <h1 class="navbar-brand navbar-brand-autodark">
            <x-link :href="route('admin.dashboard')">
                <img src="{{ asset($settings->where('setting_key', 'site_logo')->first()->plain_value) }}"
                     width="110" height="32"
                     alt="Tabler"
                     class="navbar-brand-image">
            </x-link>
        </h1>

        <div class="p-3">
            <input type="text" class="form-control" id="searchMenuInput" placeholder="Tìm kiếm menu...">
        </div>
        <div class="navbar-nav flex-row d-lg-none">
            @include('admin.layouts.partials.account')
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                @foreach ($menu as $item)
                    @if(auth('admin')->user()->checkPermissions($item['permissions']) || in_array("mevivuDev",$item['permissions']))
                        <li @class(['nav-item', 'dropdown' => count($item['sub']) > 0])>
                            <x-admin-item-link-sidebar-left class="nav-link"
                                                            :href="$routeName($item['routeName'], $item['param'] ?? [])"
                                                            :dropdown="count($item['sub']) > 0 ? true : false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    {!! __($item['icon']) !!}
                                </span>
                                <span class="nav-link-title">
                                    {{ __($item['title']) }}
                                </span>
                            </x-admin-item-link-sidebar-left>
                            @if (count($item['sub']))
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            @foreach ($item['sub'] as $item)
                                                @if(auth('admin')->user()->checkPermissions($item['permissions']) || in_array("mevivuDev",$item['permissions']) )
                                                    <x-admin-item-link-sidebar-left class="dropdown-item"
                                                                                    :href="$routeName($item['routeName'], $item['param'] ?? [])">
                                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                            {!! __($item['icon']) !!}
                                                        </span>
                                                        <span class="nav-link-title">
                                                            {{ __($item['title']) }}
                                                        </span>
                                                    </x-admin-item-link-sidebar-left>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</aside>

<style>
    /* End Navbar */

    #searchMenuInput {
        background-color: #fff;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 10px;
        color: black;
    }
</style>

<script src="{{ asset('public/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#searchMenuInput').on('keyup', function () {
            const value = $(this).val().toLowerCase();
            $("#sidebar-menu ul.navbar-nav > li").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

