/**
 * Page builder (projects & pages).
 *
 * The document is { ...meta, blocks: [{ id, type, data, style }] }. Every change is
 * pushed to an undo history, saved as a local draft and re-rendered in the preview
 * iframe using the real public templates (POST admin/builder/preview).
 */
const clone = (value) => JSON.parse(JSON.stringify(value));
const uid = () => 'b_' + Math.random().toString(36).slice(2, 12);
const debounce = (fn, ms) => {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), ms);
    };
};
const isTyping = (el) => el && (el.isContentEditable || ['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName));

function builder(cfg) {
    const draftKey = `builder-draft:${cfg.mode}:${cfg.id || 'new'}`;

    return {
        cfg,
        doc: clone(cfg.data),
        media: cfg.media || {},
        types: cfg.types,
        lang: document.documentElement.lang === 'en' ? 'en' : 'ar',
        device: 'desktop',
        selectedId: null,
        leftTab: 'blocks',
        rightTab: 'content',
        inspectorOpen: false,
        palette: { open: false, index: null, q: '' },
        saving: false,
        dirty: false,
        previewLoading: false,
        previewY: 0,
        history: [],
        future: [],
        draft: null,
        liveUrl: cfg.liveUrl,
        tagInput: '',
        lastSnapshot: null,

        init() {
            // Blocks saved before a setting existed get its default value.
            this.doc.blocks.forEach((block) => {
                const blank = this.type(block.type).blank;
                if (!blank) return;
                block.data = { ...clone(blank.data), ...block.data };
                block.style = { ...clone(blank.style), ...block.style };
            });

            this.lastSnapshot = JSON.stringify(this.doc);

            try {
                const saved = JSON.parse(localStorage.getItem(draftKey) || 'null');
                if (saved && JSON.stringify(saved.doc) !== this.lastSnapshot) this.draft = saved;
            } catch (e) {
                // Ignore unreadable drafts.
            }

            this.schedulePreview = debounce(() => this.refreshPreview(), 450);
            this.recordChange = debounce(() => this.commit(), 350);

            this.$watch('doc', () => {
                this.dirty = JSON.stringify(this.doc) !== this.cleanSnapshot;
                this.recordChange();
                this.schedulePreview();
            });
            this.cleanSnapshot = this.lastSnapshot;

            this.$watch('lang', () => this.refreshPreview());
            this.$watch('selectedId', (id) => this.postToPreview({ type: 'builder:highlight', id, scroll: true }));

            window.addEventListener('message', (event) => this.onPreviewMessage(event));
            window.addEventListener('beforeunload', (e) => {
                if (this.dirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
            window.addEventListener('keydown', (e) => this.onKey(e));

            if (this.doc.blocks.length) this.selectedId = null;
            this.$nextTick(() => this.refreshPreview());
        },

        /* ------------------------------------------------------------ helpers */
        get sel() {
            return this.doc.blocks.find((b) => b.id === this.selectedId) || null;
        },

        get selIndex() {
            return this.doc.blocks.findIndex((b) => b.id === this.selectedId);
        },

        get other() {
            return this.lang === 'ar' ? 'en' : 'ar';
        },

        type(key) {
            return this.types.find((t) => t.type === key) || { label: { ar: key, en: key }, icon: 'grid' };
        },

        label(key) {
            return this.type(key).label[document.documentElement.lang] || key;
        },

        m(id) {
            return id ? this.media[id] || null : null;
        },

        t(value) {
            if (!value || typeof value !== 'object') return value || '';
            return value[this.lang] || value[this.other] || '';
        },

        strip(html) {
            const div = document.createElement('div');
            div.innerHTML = html || '';
            return div.textContent.trim();
        },

        summary(block) {
            const d = block.data;
            const text = this.t(d.title) || this.t(d.text) || this.t(d.heading) || this.strip(this.t(d.html)) || this.t(d.caption);
            if (text) return text.slice(0, 60);
            if (d.items?.length) return d.items.length + ' ×';
            if (d.media && this.m(d.media)) return this.m(d.media).name;
            return '';
        },

        thumb(block) {
            const d = block.data;
            const id = d.media || d.items?.[0]?.media || d.before || d.cols?.find((c) => c.media)?.media;
            return this.m(id)?.thumb || null;
        },

        missingLang(block) {
            const check = (v) => v && typeof v === 'object' && 'ar' in v && 'en' in v && !!(v.ar || '').trim() !== !!(v.en || '').trim();
            return Object.values(block.data).some((v) => check(v) || (Array.isArray(v) && v.some((row) => Object.values(row || {}).some(check))));
        },

        /* ------------------------------------------------------------ history */
        commit() {
            const snapshot = JSON.stringify(this.doc);
            if (snapshot === this.lastSnapshot) return;
            this.history.push(this.lastSnapshot);
            if (this.history.length > 100) this.history.shift();
            this.future = [];
            this.lastSnapshot = snapshot;
            try {
                localStorage.setItem(draftKey, JSON.stringify({ doc: this.doc, at: Date.now() }));
            } catch (e) {
                // Storage full / private mode: drafts are a convenience only.
            }
        },

        undo() {
            this.commit();
            if (!this.history.length) return;
            this.future.push(this.lastSnapshot);
            this.lastSnapshot = this.history.pop();
            this.doc = JSON.parse(this.lastSnapshot);
        },

        redo() {
            if (!this.future.length) return;
            this.history.push(this.lastSnapshot);
            this.lastSnapshot = this.future.pop();
            this.doc = JSON.parse(this.lastSnapshot);
        },

        restoreDraft() {
            this.doc = this.draft.doc;
            this.draft = null;
        },

        discardDraft() {
            localStorage.removeItem(draftKey);
            this.draft = null;
        },

        /* ------------------------------------------------------------ blocks */
        openPalette(index = null) {
            this.palette = { open: true, index: index ?? this.doc.blocks.length, q: '' };
        },

        paletteGroups() {
            const q = this.palette.q.trim().toLowerCase();
            const groups = {};
            this.types
                .filter((t) => !q || Object.values(t.label).some((l) => l.toLowerCase().includes(q)) || t.type.includes(q))
                .forEach((t) => (groups[t.group] ||= []).push(t));
            return groups;
        },

        async add(typeKey) {
            const def = this.type(typeKey);
            let block = clone(def.blank);
            block.id = uid();
            const index = Math.min(this.palette.index ?? this.doc.blocks.length, this.doc.blocks.length);
            this.doc.blocks.splice(index, 0, block);
            this.palette.open = false;
            this.select(block.id);

            // Work on the reactive copy from now on — mutating `block` itself would
            // change the data without Alpine noticing (no re-render, no preview refresh).
            block = this.doc.blocks[index];

            // Media blocks: open the picker straight away.
            if (['image', 'video', 'embed'].includes(typeKey)) {
                await this.pickMedia(block.data, 'media', typeKey === 'image' ? ['image'] : typeKey === 'embed' ? ['embed'] : ['video', 'embed']);
            } else if (typeKey === 'gallery') {
                await this.addItems(block.data);
            } else if (typeKey === 'before_after') {
                await this.pickMedia(block.data, 'before', ['image']);
                if (block.data.before) await this.pickMedia(block.data, 'after', ['image']);
            }
        },

        select(id) {
            this.selectedId = id;
            this.rightTab = 'content';
            this.inspectorOpen = true;
            this.$nextTick(() => document.getElementById('row-' + id)?.scrollIntoView({ block: 'nearest', behavior: 'smooth' }));
        },

        duplicate(index) {
            const copy = clone(this.doc.blocks[index]);
            copy.id = uid();
            this.doc.blocks.splice(index + 1, 0, copy);
            this.select(copy.id);
        },

        async remove(index) {
            const ok = await window.confirmAction({ title: this.cfg.i18n.deleteBlock, message: this.cfg.i18n.deleteBlockHint });
            if (!ok) return;
            const [removed] = this.doc.blocks.splice(index, 1);
            if (removed.id === this.selectedId) this.selectedId = null;
        },

        move(index, dir) {
            const to = index + dir;
            if (to < 0 || to >= this.doc.blocks.length) return;
            const [block] = this.doc.blocks.splice(index, 1);
            this.doc.blocks.splice(to, 0, block);
        },

        reorder(id, position) {
            const from = this.doc.blocks.findIndex((b) => b.id === id);
            if (from < 0 || from === position) return;
            const [block] = this.doc.blocks.splice(from, 1);
            this.doc.blocks.splice(position, 0, block);
        },

        reorderItems(list, from, to) {
            const [item] = list.splice(from, 1);
            list.splice(to, 0, item);
        },

        copyFromOther(obj, key) {
            if (obj[key] && typeof obj[key] === 'object') obj[key][this.lang] = obj[key][this.other];
        },

        /* ------------------------------------------------------------ media */
        remember(list) {
            list.forEach((item) => (this.media[item.id] = item));
        },

        async pickMedia(target, key, accept = ['image']) {
            const [picked] = await Alpine.store('picker').open({ accept, multiple: false });
            if (!picked) return;
            this.remember([picked]);
            target[key] = picked.id;
        },

        async addItems(data) {
            const picked = await Alpine.store('picker').open({ accept: ['image'], multiple: true });
            if (!picked.length) return;
            this.remember(picked);
            picked.forEach((p) => data.items.push({ media: p.id, caption: { ar: '', en: '' }, span: '1' }));
        },

        /* ------------------------------------------------------------ meta helpers */
        addTag() {
            const tag = this.tagInput.trim().replace(/,$/, '');
            if (tag && !this.doc.tags.includes(tag)) this.doc.tags.push(tag);
            this.tagInput = '';
        },

        get subCategories() {
            return this.cfg.categories?.find((c) => c.id == this.doc.category_id)?.subs || [];
        },

        slugify() {
            const source = this.doc.title.en || this.doc.title.ar || '';
            this.doc.slug = source
                .toLowerCase()
                .normalize('NFKD')
                .replace(/[̀-ͯ]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .slice(0, 80);
        },

        /* ------------------------------------------------------------ preview */
        async refreshPreview() {
            const frame = this.$refs.preview;
            if (!frame) return;
            this.previewLoading = true;
            try {
                const { data } = await window.http.post(this.cfg.previewUrl, { mode: this.cfg.mode, locale: this.lang, data: this.doc }, { responseType: 'text' });
                frame.srcdoc = data;
            } catch (error) {
                window.toast(window.errorMessage(error), 'error');
            } finally {
                this.previewLoading = false;
            }
        },

        postToPreview(message) {
            this.$refs.preview?.contentWindow?.postMessage(message, '*');
        },

        onPreviewMessage({ data, source }) {
            if (source !== this.$refs.preview?.contentWindow) return;
            if (data?.type === 'builder:select') this.select(data.id);
            if (data?.type === 'builder:scrolled') this.previewY = data.y;
            if (data?.type === 'builder:ready') {
                this.postToPreview({ type: 'builder:scroll', y: this.previewY });
                this.postToPreview({ type: 'builder:highlight', id: this.selectedId, scroll: false });
            }
        },

        get frameWidth() {
            return { desktop: '100%', tablet: '820px', mobile: '390px' }[this.device];
        },

        /* ------------------------------------------------------------ save */
        async save() {
            if (this.saving) return;
            this.commit();
            this.saving = true;
            try {
                const method = this.cfg.method === 'PUT' ? 'put' : 'post';
                const { data } = await window.http[method](this.cfg.saveUrl, this.doc);
                this.cleanSnapshot = JSON.stringify(this.doc);
                this.dirty = false;
                localStorage.removeItem(draftKey);
                if (data.slug) this.doc.slug = data.slug;
                if (data.liveUrl) this.liveUrl = data.liveUrl;
                window.toast(data.message);
                if (data.redirect) {
                    window.location = data.redirect;
                }
            } catch (error) {
                window.toast(window.errorMessage(error), 'error');
            } finally {
                this.saving = false;
            }
        },

        onKey(e) {
            const mod = e.ctrlKey || e.metaKey;
            if (mod && e.key.toLowerCase() === 's') {
                e.preventDefault();
                this.save();
                return;
            }
            if (isTyping(document.activeElement)) return;
            if (mod && e.key.toLowerCase() === 'z') {
                e.preventDefault();
                e.shiftKey ? this.redo() : this.undo();
            } else if (mod && e.key.toLowerCase() === 'y') {
                e.preventDefault();
                this.redo();
            } else if (e.key === 'Escape') {
                this.selectedId = null;
            } else if ((e.key === 'Delete' || e.key === 'Backspace') && this.sel) {
                e.preventDefault();
                this.remove(this.selIndex);
            }
        },
    };
}

document.addEventListener('alpine:init', () => window.Alpine.data('builder', builder));
