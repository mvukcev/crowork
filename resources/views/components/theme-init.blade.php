<script>
    (function () {
        var key = 'cw_theme_preference';
        var allowed = { light: true, dark: true, system: true };
        var cookieMatch = document.cookie.match(/(?:^|; )cw_theme=([^;]+)/);
        var cookieValue = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
        var stored = null;

        try {
            stored = localStorage.getItem(key);
        } catch (_) {
            stored = null;
        }

        var preference = allowed[stored] ? stored : (allowed[cookieValue] ? cookieValue : 'system');
        var isDarkSystem = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var resolved = preference === 'system' ? (isDarkSystem ? 'dark' : 'light') : preference;
        var root = document.documentElement;

        root.classList.remove('cw-theme-light', 'cw-theme-dark');
        root.classList.add(resolved === 'dark' ? 'cw-theme-dark' : 'cw-theme-light');
        root.dataset.theme = resolved;
        root.dataset.themePreference = preference;
        root.style.colorScheme = resolved;

        try {
            localStorage.setItem(key, preference);
        } catch (_) {
            // Ignore storage failures in restricted/private contexts.
        }
    })();
</script>
