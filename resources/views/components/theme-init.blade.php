@php
    $serverThemePreference = session('theme');
    if (! in_array($serverThemePreference, ['light', 'dark', 'system'], true)) {
        $serverThemePreference = request()->cookie('cw_theme');
    }
    if (! in_array($serverThemePreference, ['light', 'dark', 'system'], true)) {
        $serverThemePreference = 'system';
    }
@endphp

<script>
    (function () {
        var serverPreference = @json($serverThemePreference);

        var key = 'cw-theme';
        var legacyKey = 'cw_theme_preference';
        var filamentKey = 'theme';
        var allowed = { light: true, dark: true, system: true };
        var cookieMatch = document.cookie.match(/(?:^|; )cw_theme=([^;]+)/);
        var cookieValue = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
        var stored = null;
        var legacyStored = null;

        try {
            stored = localStorage.getItem(key);
            legacyStored = localStorage.getItem(legacyKey);
        } catch (_) {
            stored = null;
            legacyStored = null;
        }

        var preference = allowed[serverPreference]
            ? serverPreference
            : (allowed[stored]
                ? stored
                : (allowed[legacyStored]
                    ? legacyStored
                    : (allowed[cookieValue] ? cookieValue : 'system')));
        var isDarkSystem = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var resolved = preference === 'system' ? (isDarkSystem ? 'dark' : 'light') : preference;
        var root = document.documentElement;

        root.classList.remove('cw-theme-light', 'cw-theme-dark');
        root.classList.add(resolved === 'dark' ? 'cw-theme-dark' : 'cw-theme-light');
        if (resolved === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        root.dataset.theme = resolved;
        root.dataset.themePreference = preference;
        root.style.colorScheme = resolved;

        try {
            localStorage.setItem(key, preference);
            localStorage.setItem(legacyKey, preference);
            localStorage.setItem(filamentKey, preference);
        } catch (_) {
            // Ignore storage failures in restricted/private contexts.
        }

        window.addEventListener('theme-changed', function (event) {
            var next = event && event.detail;
            if (!allowed[next]) {
                return;
            }

            try {
                localStorage.setItem(key, next);
                localStorage.setItem(legacyKey, next);
            } catch (_) {
                // Ignore storage failures in restricted/private contexts.
            }
        });
    })();
</script>
