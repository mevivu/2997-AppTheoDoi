<!-- Navbar -->
<header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">
    <div class="container custom-header-top">
        <div class="container-xl">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                    aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
    
        </div>
        <div class="col-md-4 col-lg-4 d-flex justify-content-end">
            @include('admin.layouts.partials.notification')
    
            <div class="navbar-nav flex-row order-md-last">
                @include('admin.layouts.partials.account')
            </div>
    
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu"></div>
    </div>
</header>


