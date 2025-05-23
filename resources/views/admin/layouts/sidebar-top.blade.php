<!-- Navbar -->
<header class="modern-header">
    <div class="header-container">
        <div class="header-left">
            <button class="header-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                <i class="ti ti-menu-2"></i>
            </button>
        </div>

        <div class="header-right">
            @include('admin.layouts.partials.notification')

            <div class="header-divider"></div>

            <div class="header-account">
                @include('admin.layouts.partials.account')
            </div>
        </div>
    </div>
</header>

<style>
    .modern-header {
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 0.5rem 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .header-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 60px;
    }

    .header-left {
        display: flex;
        align-items: center;
    }

    .header-toggler {
        background: transparent;
        border: none;
        color: #1e3c72;
        padding: 0.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .header-toggler:hover {
        background: rgba(30, 60, 114, 0.05);
        color: #2a5298;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-divider {
        width: 1px;
        height: 32px;
        background: rgba(0,0,0,0.1);
        margin: 0 0.5rem;
    }

    .header-account {
        display: flex;
        align-items: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-container {
            padding: 0 1rem;
            height: 56px;
        }

        .header-divider {
            display: none;
        }
    }
</style>


