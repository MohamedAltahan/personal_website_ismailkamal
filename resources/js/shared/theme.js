/**
 * Three-state theme switch (light / dark / system) shared by the site and the dashboard.
 * The inline head script (partials/theme-script) does the actual applying.
 */
export default function themeToggle() {
    return {
        pref: document.documentElement.dataset.themePref || 'system',
        options: ['light', 'dark', 'system'],

        set(value) {
            this.pref = value;
            try {
                // "theme" for the website, "admin-theme" for the dashboard (set by partials/theme-script).
                localStorage.setItem(document.documentElement.dataset.themeKey || 'theme', value);
            } catch (e) {
                // Private mode: the choice lasts for this page only.
            }
            document.documentElement.classList.add('theme-switching');
            window.__applyTheme?.();
            setTimeout(() => document.documentElement.classList.remove('theme-switching'), 250);
        },

        cycle() {
            this.set(this.options[(this.options.indexOf(this.pref) + 1) % this.options.length]);
        },
    };
}
