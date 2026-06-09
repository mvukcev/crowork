<style>
    :root {
        --cw-brand-tangerine: #fe5000;
        --cw-brand-navy: #0c2340;
        --cw-brand-ice: #dde5ed;
        --cw-brand-violet: #8b84d7;
        --cw-brand-lime: #e2e868;
    }

    .fi-sidebar {
        background: linear-gradient(180deg, #f8fafd 0%, #edf3f9 100%);
        border-inline-end-color: rgba(12, 35, 64, 0.14);
        color: rgba(12, 35, 64, 0.86);
    }

    .dark .fi-sidebar {
        background: linear-gradient(180deg, rgba(12, 35, 64, 0.98), rgba(6, 24, 45, 0.96));
        border-inline-end-color: rgba(221, 229, 237, 0.16);
        color: rgba(221, 229, 237, 0.9);
    }

    .fi-sidebar .fi-sidebar-group-label {
        color: rgba(12, 35, 64, 0.62);
    }

    .dark .fi-sidebar .fi-sidebar-group-label {
        color: rgba(221, 229, 237, 0.62);
    }

    .fi-sidebar .fi-sidebar-item-button,
    .fi-sidebar .fi-sidebar-group-button {
        color: rgba(12, 35, 64, 0.8);
    }

    .dark .fi-sidebar .fi-sidebar-item-button,
    .dark .fi-sidebar .fi-sidebar-group-button {
        color: rgba(221, 229, 237, 0.88);
    }

    .fi-topbar {
        background: color-mix(in srgb, var(--cw-brand-navy) 94%, #000000 6%);
        border-bottom-color: rgba(221, 229, 237, 0.16);
    }

    .fi-main,
    .fi-page {
        background: #f4f7fa;
    }

    .dark .fi-main,
    .dark .fi-page {
        background: #06182d;
    }

    .fi-sidebar .fi-sidebar-item-button.fi-active,
    .fi-sidebar .fi-sidebar-group-button.fi-active {
        background: rgba(254, 80, 0, 0.14);
        color: #fe5000;
        border: 1px solid rgba(254, 80, 0, 0.24);
    }

    .fi-sidebar .fi-sidebar-item-button:hover,
    .fi-sidebar .fi-sidebar-group-button:hover {
        background: rgba(254, 80, 0, 0.1);
        color: #c13f00;
    }

    .dark .fi-sidebar .fi-sidebar-item-button.fi-active,
    .dark .fi-sidebar .fi-sidebar-group-button.fi-active,
    .dark .fi-sidebar .fi-sidebar-item-button:hover,
    .dark .fi-sidebar .fi-sidebar-group-button:hover {
        background: rgba(254, 80, 0, 0.18);
        color: #ffffff;
        border: none;
    }

    .fi-btn-color-primary,
    .fi-color-primary {
        --tw-ring-color: rgba(254, 80, 0, 0.42);
    }

    .fi-ta-table thead tr,
    .fi-ta-header-cell,
    .fi-fo-field-wrp-label {
        color: #0c2340;
    }

    .dark .fi-ta-table thead tr,
    .dark .fi-ta-header-cell,
    .dark .fi-fo-field-wrp-label {
        color: #dde5ed;
    }

    .fi-ta-table tbody tr:hover {
        background: rgba(12, 35, 64, 0.04);
    }

    .dark .fi-ta-table tbody tr:hover {
        background: rgba(139, 132, 215, 0.1);
    }
</style>
