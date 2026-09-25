import Alpine from 'alpinejs';
import sort from '@alpinejs/sort';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

import themeToggle from './shared/theme';
import http, { errorMessage } from './dashboard/http';
import registerToast from './dashboard/toast';
import registerMediaPicker from './dashboard/media-picker';
import { uploadQueue, uploadFile } from './dashboard/uploader';
import rte from './dashboard/rte';

Alpine.plugin([sort, collapse, focus]);

registerToast(Alpine);
registerMediaPicker(Alpine);

/**
 * Global confirm dialog:  if (await confirmAction({ title, message, danger: true })) { ... }
 */
Alpine.store('confirm', {
    isOpen: false,
    title: '',
    message: '',
    confirmLabel: '',
    danger: true,
    resolver: null,
    ask({ title = '', message = '', confirmLabel = '', danger = true } = {}) {
        Object.assign(this, { title, message, confirmLabel, danger, isOpen: true });
        return new Promise((resolve) => (this.resolver = resolve));
    },
    answer(value) {
        this.isOpen = false;
        this.resolver?.(value);
        this.resolver = null;
    },
});
window.confirmAction = (options) => Alpine.store('confirm').ask(options);

Alpine.data('themeToggle', themeToggle);
Alpine.data('uploadQueue', uploadQueue);
Alpine.data('rte', rte);

/** Single media field bound to a hidden input (settings, cover image…). */
Alpine.data('mediaField', (initial = null, accept = ['image']) => ({
    media: initial,
    async choose() {
        const [picked] = await Alpine.store('picker').open({ accept, multiple: false });
        if (picked) this.media = picked;
    },
    clear() {
        this.media = null;
    },
}));

/** Forms submitted with a delete/patch via fetch, e.g. toggles inside tables. */
Alpine.data('ajaxToggle', (url, value) => ({
    value,
    busy: false,
    async toggle() {
        this.busy = true;
        try {
            const { data } = await http.patch(url);
            this.value = data.value ?? !this.value;
            if (data.message) window.toast(data.message);
        } catch (error) {
            window.toast(errorMessage(error), 'error');
        } finally {
            this.busy = false;
        }
    },
}));

/** Delete button: confirm, send DELETE, then remove the row / redirect. */
window.deleteResource = async (url, { title, message, redirect = null, el = null } = {}) => {
    const ok = await window.confirmAction({ title, message });
    if (!ok) return false;
    try {
        const { data } = await http.delete(url);
        window.toast(data.message || 'OK');
        if (redirect) {
            window.location = redirect;
        } else if (el) {
            el.style.transition = 'opacity .25s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 250);
        }
        return true;
    } catch (error) {
        window.toast(errorMessage(error), 'error');
        return false;
    }
};

/** <canvas x-data="chart({ type, labels, values })"> — Chart.js loaded on demand. */
Alpine.data('chart', ({ type = 'line', labels = [], values = [] }) => ({
    async init() {
        const { default: Chart } = await import('chart.js/auto');
        const dark = document.documentElement.dataset.theme === 'dark';
        const gold = '#fbd300';
        const navy = dark ? '#a0bbf0' : '#2b357d';
        const grid = dark ? 'rgba(255,255,255,.06)' : '#eceef4';
        const palette = ['#fbd300', '#2b357d', '#5274df', '#1f9d57', '#b5842a', '#d6342c', '#3b4cb5', '#a0bbf0', '#8c94a3'];
        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        Chart.defaults.color = dark ? '#a3a8c3' : '#6e7485';

        new Chart(this.$el, {
            type,
            data: {
                labels,
                datasets: [
                    type === 'doughnut'
                        ? { data: values, backgroundColor: palette, borderWidth: 0, hoverOffset: 6 }
                        : {
                              data: values,
                              borderColor: dark ? gold : navy,
                              backgroundColor: dark ? 'rgba(251,211,0,.12)' : 'rgba(43,53,125,.08)',
                              fill: true,
                              tension: 0.38,
                              pointRadius: 0,
                              pointHoverRadius: 5,
                              borderWidth: 2.5,
                          },
                ],
            },
            options: {
                maintainAspectRatio: false,
                cutout: type === 'doughnut' ? '68%' : undefined,
                plugins: {
                    legend: { display: type === 'doughnut', position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, padding: 14 } },
                    tooltip: { backgroundColor: '#111033', padding: 10, cornerRadius: 10, rtl: document.dir === 'rtl' },
                },
                scales:
                    type === 'doughnut'
                        ? {}
                        : {
                              x: { reverse: document.dir === 'rtl', grid: { display: false }, border: { display: false } },
                              y: { position: document.dir === 'rtl' ? 'right' : 'left', beginAtZero: true, ticks: { precision: 0 }, grid: { color: grid }, border: { display: false } },
                          },
            },
        });
    },
}));

window.Alpine = Alpine;
window.http = http;
window.errorMessage = errorMessage;
window.uploadFile = uploadFile;

// Start after every module script (e.g. the page builder) has registered its components.
document.addEventListener('DOMContentLoaded', () => Alpine.start());
