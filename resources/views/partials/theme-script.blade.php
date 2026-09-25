{{--
    Sets data-theme before first paint (no flash).
    Preference: localStorage[key] → $default; "system" follows the OS live.
    The website and the dashboard use separate keys so each remembers its own choice.
--}}
@php($themeKey = $key ?? 'theme')
<script>
    (function () {
        var key = @json($themeKey), fallback = @json($default ?? 'system'), media = window.matchMedia('(prefers-color-scheme: dark)');
        document.documentElement.dataset.themeKey = key;
        function apply() {
            var pref;
            try { pref = localStorage.getItem(key); } catch (e) {}
            if (pref !== 'light' && pref !== 'dark' && pref !== 'system') pref = fallback;
            var dark = pref === 'dark' || (pref === 'system' && media.matches);
            document.documentElement.dataset.theme = dark ? 'dark' : 'light';
            document.documentElement.dataset.themePref = pref;
            document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        }
        apply();
        media.addEventListener('change', apply);
        window.__applyTheme = apply;
    })();
</script>
