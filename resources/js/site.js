import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import PhotoSwipeLightbox from 'photoswipe/lightbox';

import themeToggle from './shared/theme';
import heroFx from './site/hero-effects';

const root = document.documentElement;
// The OS "reduce motion" preference (e.g. Windows "Animation effects" off) is honoured
// unless disabled in Settings → Appearance.
const osReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches && root.dataset.reducedMotion !== 'ignore';
const motionOk = !osReduced && root.dataset.animations !== 'off';
if (!motionOk) root.classList.add('no-motion');

Alpine.plugin([collapse, focus]);
Alpine.data('themeToggle', themeToggle);
Alpine.data('heroFx', heroFx);

/* ------------------------------------------------------------------ header */
Alpine.data('siteHeader', () => ({
    scrolled: false,
    hidden: false,
    menu: false,
    lastY: 0,
    init() {
        const onScroll = () => {
            const y = window.scrollY;
            this.scrolled = y > 24;
            this.hidden = !this.menu && y > 400 && y > this.lastY;
            this.lastY = y;
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        this.$watch('menu', (open) => document.body.classList.toggle('overflow-hidden', open));
    },
}));

/* ------------------------------------------------------------------ contact form */
Alpine.data('contactForm', (url) => ({
    busy: false,
    sent: false,
    errors: {},
    async submit(event) {
        this.busy = true;
        this.errors = {};
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(event.target),
            });
            const data = await response.json().catch(() => ({}));
            if (response.ok) {
                this.sent = true;
                event.target.reset();
            } else if (response.status === 422) {
                this.errors = Object.fromEntries(Object.entries(data.errors || {}).map(([k, v]) => [k, v[0]]));
            } else {
                this.errors = { form: data.message || 'Error' };
            }
        } catch (e) {
            this.errors = { form: e.message };
        } finally {
            this.busy = false;
        }
    },
}));

/* ------------------------------------------------------------------ before / after */
Alpine.data('compare', () => ({ pos: 50 }));

/* ------------------------------------------------------------------ video player with poster overlay */
Alpine.data('videoPlayer', (autoplay = false) => ({
    playing: autoplay,
    play() {
        const video = this.$refs.video;
        video.controls = true;
        video.play();
        this.playing = true;
    },
}));

/* ------------------------------------------------------------------ embed facade (YouTube / Vimeo loaded on click) */
Alpine.data('embedFacade', (src) => ({
    loaded: false,
    src,
    load() {
        this.loaded = true;
    },
}));

/* ------------------------------------------------------------------ load more (work index) */
Alpine.data('loadMore', (next) => ({
    next,
    busy: false,
    async more() {
        if (!this.next || this.busy) return;
        this.busy = true;
        const response = await fetch(this.next, { headers: { 'X-Fragment': '1' } });
        const html = await response.text();
        const tpl = document.createElement('template');
        tpl.innerHTML = html;
        const grid = tpl.content.querySelector('[data-grid]');
        const target = this.$refs.grid.querySelector('[data-grid]') || this.$refs.grid;
        [...(grid?.children || [])].forEach((node) => target.appendChild(node));
        this.next = tpl.content.querySelector('[data-next]')?.dataset.next || null;
        this.busy = false;
        observeReveals(this.$refs.grid);
        bindImages(this.$refs.grid);
    },
}));

window.Alpine = Alpine;
Alpine.start();

/* ------------------------------------------------------------------ reveal on scroll */
const revealObserver =
    motionOk && 'IntersectionObserver' in window
        ? new IntersectionObserver(
              (entries) =>
                  entries.forEach((entry) => {
                      if (entry.isIntersecting) {
                          show(entry.target);
                          revealObserver.unobserve(entry.target);
                      }
                  }),
              { rootMargin: '0px 0px -8% 0px', threshold: 0.05 },
          )
        : null;

function show(el) {
    el.classList.add('is-visible');
    el.dataset.shownAt = Date.now();
}

function observeReveals(scope = document) {
    const fold = window.innerHeight * 1.05;
    scope.querySelectorAll('[data-reveal]:not(.is-visible)').forEach((el) => {
        // Above the fold: animate in right away instead of waiting for the observer.
        if (!revealObserver || el.getBoundingClientRect().top < fold) {
            show(el);
        } else {
            revealObserver.observe(el);
        }
    });
}

/* ------------------------------------------------------------------ image fade-in */
function bindImages(scope = document) {
    scope.querySelectorAll('img.img-fade:not(.is-loaded)').forEach((img) => {
        if (img.complete && img.naturalWidth) img.classList.add('is-loaded');
        else img.addEventListener('load', () => img.classList.add('is-loaded'), { once: true });
        img.addEventListener('error', () => img.classList.add('is-loaded'), { once: true });
    });
}

observeReveals();
bindImages();

// Failsafe: if the browser paused transitions (background tab, power saving…), never leave
// revealed content stuck invisible.
setInterval(() => {
    document.querySelectorAll('[data-reveal].is-visible:not([data-settled])').forEach((el) => {
        if (Date.now() - el.dataset.shownAt < 1500) return;
        if (parseFloat(getComputedStyle(el).opacity) < 0.99) {
            el.style.transition = 'none';
        }
        el.dataset.settled = '1';
    });
}, 2500);

/* ------------------------------------------------------------------ hover video previews on project cards */
document.addEventListener(
    'mouseover',
    (e) => {
        const card = e.target.closest('[data-hover-video]');
        if (!card || card.dataset.playing) return;
        const video = card.querySelector('video');
        if (!video) return;
        card.dataset.playing = '1';
        video.play().catch(() => {});
        card.addEventListener(
            'mouseleave',
            () => {
                video.pause();
                delete card.dataset.playing;
            },
            { once: true },
        );
    },
    { passive: true },
);

/* ------------------------------------------------------------------ autoplay videos only while visible */
if ('IntersectionObserver' in window) {
    const videoObserver = new IntersectionObserver((entries) =>
        entries.forEach(({ target, isIntersecting }) => (isIntersecting ? target.play().catch(() => {}) : target.pause())),
    );
    document.querySelectorAll('video[data-autoplay]').forEach((v) => videoObserver.observe(v));
}

/* ------------------------------------------------------------------ lightbox */
if (document.querySelector('[data-lightbox]')) {
    const lightbox = new PhotoSwipeLightbox({
        gallery: 'main',
        children: 'a[data-lightbox]',
        pswpModule: () => import('photoswipe'),
        bgOpacity: 0.96,
        showHideAnimationType: 'zoom',
        wheelToZoom: true,
    });
    lightbox.init();
}

/* ------------------------------------------------------------------ media protection */
if (root.dataset.protect === 'on') {
    document.addEventListener('contextmenu', (e) => {
        if (e.target.closest('img, video, picture, [data-protect]')) e.preventDefault();
    });
    document.addEventListener('dragstart', (e) => {
        if (e.target.closest('img, video')) e.preventDefault();
    });
}

/* ------------------------------------------------------------------ custom cursor */
// <html data-custom-cursor="hero|site|off">: "hero" shows the cursor only over hero sections.
const cursorScope = root.dataset.customCursor;
if (['hero', 'site'].includes(cursorScope) && motionOk && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    const dot = document.createElement('div');
    dot.className = 'cursor-dot';
    dot.style.opacity = '0';
    document.body.appendChild(dot);
    let x = -100, y = -100, cx = x, cy = y, running = false;

    // Animate only while the dot is catching up — no idle requestAnimationFrame loop.
    const loop = () => {
        cx += (x - cx) * 0.2;
        cy += (y - cy) * 0.2;
        dot.style.transform = `translate3d(${cx}px, ${cy}px, 0)`;
        if (Math.abs(x - cx) + Math.abs(y - cy) > 0.3) requestAnimationFrame(loop);
        else running = false;
    };

    window.addEventListener('mousemove', (e) => {
        x = e.clientX;
        y = e.clientY;
        if (!running) {
            running = true;
            requestAnimationFrame(loop);
        }
    }, { passive: true });

    let inZone = false;
    document.addEventListener('mouseover', (e) => {
        inZone = cursorScope === 'site' || !!e.target.closest('.fx-hero');
        dot.style.opacity = inZone ? '1' : '0';
        const view = inZone ? e.target.closest('[data-cursor]') : null;
        const link = inZone && e.target.closest('a, button, [role="button"], input, textarea, select, label');
        dot.classList.toggle('is-view', !!view);
        dot.classList.toggle('is-link', !view && !!link);
        dot.textContent = view ? view.dataset.cursor : '';
    });

    document.addEventListener('mouseleave', () => (dot.style.opacity = '0'));
    document.addEventListener('mouseenter', () => (dot.style.opacity = inZone ? '1' : '0'));
}

/* ------------------------------------------------------------------ builder preview bridge */
if (window.parent !== window && root.dataset.preview === 'on') {
    document.addEventListener('click', (e) => {
        const block = e.target.closest('[data-block-id]');
        if (e.target.closest('a')) e.preventDefault();
        if (block) window.parent.postMessage({ type: 'builder:select', id: block.dataset.blockId }, '*');
    }, true);

    window.addEventListener('message', ({ data }) => {
        if (data?.type === 'builder:highlight') {
            document.querySelectorAll('[data-block-id]').forEach((el) => el.classList.toggle('builder-selected', el.dataset.blockId === data.id));
            if (data.scroll) document.querySelector(`[data-block-id="${data.id}"]`)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (data?.type === 'builder:scroll') window.scrollTo(0, data.y || 0);
    });

    window.addEventListener('scroll', () => window.parent.postMessage({ type: 'builder:scrolled', y: window.scrollY }, '*'), { passive: true });
    window.parent.postMessage({ type: 'builder:ready' }, '*');
}
