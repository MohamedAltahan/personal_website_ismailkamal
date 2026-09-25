import http, { errorMessage } from './http';

const meta = (name) => document.querySelector(`meta[name="${name}"]`)?.content;

/**
 * Global media picker.
 *   const [media] = await Alpine.store('picker').open({ accept: ['image'], multiple: false })
 * Resolves with an array of media JSON objects (empty array when cancelled).
 */
export default function registerMediaPicker(Alpine) {
    Alpine.store('picker', {
        isOpen: false,
        options: {},
        resolver: null,

        open(options = {}) {
            this.options = { accept: ['image', 'video', 'embed'], multiple: false, title: null, ...options };
            this.isOpen = true;
            window.dispatchEvent(new CustomEvent('picker-opened'));
            return new Promise((resolve) => (this.resolver = resolve));
        },

        close(result = []) {
            this.isOpen = false;
            this.resolver?.(result);
            this.resolver = null;
        },
    });

    Alpine.data('mediaPicker', () => ({
        items: [],
        selected: [],
        page: 1,
        hasMore: false,
        loading: false,
        q: '',
        type: '',
        tab: 'library',
        embedUrl: '',
        embedLoading: false,

        get store() {
            return Alpine.store('picker');
        },

        get accept() {
            return this.store.options.accept || [];
        },

        init() {
            window.addEventListener('picker-opened', () => {
                this.selected = [];
                this.tab = 'library';
                this.type = this.accept.length === 1 ? this.accept[0] : '';
                this.load(true);
            });
        },

        async load(reset = false) {
            if (reset) {
                this.page = 1;
                this.items = [];
            }
            this.loading = true;
            try {
                const { data } = await http.get(meta('media-list-url'), {
                    params: { page: this.page, q: this.q || undefined, type: this.type || undefined, accept: this.accept.join(',') },
                });
                this.items = reset ? data.data : this.items.concat(data.data);
                this.hasMore = !!data.next_page;
            } finally {
                this.loading = false;
            }
        },

        more() {
            if (this.hasMore && !this.loading) {
                this.page++;
                this.load();
            }
        },

        isSelected(item) {
            return this.selected.some((s) => s.id === item.id);
        },

        toggle(item) {
            if (!this.store.options.multiple) {
                this.selected = [item];
                return;
            }
            this.selected = this.isSelected(item) ? this.selected.filter((s) => s.id !== item.id) : [...this.selected, item];
        },

        order(item) {
            return this.selected.findIndex((s) => s.id === item.id) + 1;
        },

        uploaded(media) {
            if (!this.accept.includes(media.type)) return;
            this.items.unshift(media);
            this.tab = 'library';
            if (this.store.options.multiple) {
                this.selected.push(media);
            } else {
                this.selected = [media];
            }
        },

        async addEmbed() {
            if (!this.embedUrl) return;
            this.embedLoading = true;
            try {
                const { data } = await http.post(meta('media-embed-url'), { url: this.embedUrl });
                this.embedUrl = '';
                this.uploaded(data.media);
            } catch (error) {
                window.toast(errorMessage(error), 'error');
            } finally {
                this.embedLoading = false;
            }
        },

        confirm() {
            this.store.close(this.selected);
        },

        cancel() {
            this.store.close([]);
        },
    }));
}
