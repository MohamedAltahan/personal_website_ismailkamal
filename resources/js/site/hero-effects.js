/**
 * Hero effects — Alpine component `heroFx` used by resources/views/blocks/hero.blade.php.
 *
 *   effect:      none | aurora | particles | spotlight | grid | waves | stars
 *   textEffect:  none | rise | scramble | typewriter | shimmer
 *   interactive: pointer parallax (--mx / --my) + magnetic button
 *
 * Canvas effects only animate while the hero is on screen and the tab is visible,
 * and render a single still frame when motion is reduced / disabled.
 */
// site.js sets .no-motion from the OS preference + Settings → Appearance.
const reducedMotion = () => document.documentElement.classList.contains('no-motion');

function parseColor(value, fallback = [255, 210, 0]) {
    const ctx = document.createElement('canvas').getContext('2d');
    ctx.fillStyle = '#000';
    ctx.fillStyle = (value || '').trim() || '#ffd200';
    const hex = ctx.fillStyle;
    if (hex.startsWith('#') && hex.length === 7) {
        return [parseInt(hex.slice(1, 3), 16), parseInt(hex.slice(3, 5), 16), parseInt(hex.slice(5, 7), 16)];
    }
    const m = hex.match(/\d+(\.\d+)?/g);
    return m ? m.slice(0, 3).map(Number) : fallback;
}

const rgba = ([r, g, b], a) => `rgba(${r},${g},${b},${a})`;

/* ------------------------------------------------------------------ canvas painters */
const painters = {
    particles(ctx, w, h, s) {
        if (!s.dots) {
            const count = Math.min(170, Math.round(((w * h) / 16000) * (0.4 + s.intensity)));
            s.dots = Array.from({ length: count }, () => ({
                x: Math.random() * w,
                y: Math.random() * h,
                vx: (Math.random() - 0.5) * 0.45,
                vy: (Math.random() - 0.5) * 0.45,
                r: Math.random() * 1.6 + 0.6,
            }));
        }
        const link = 110 + s.intensity * 60;
        ctx.clearRect(0, 0, w, h);
        for (const p of s.dots) {
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < 0 || p.x > w) p.vx *= -1;
            if (p.y < 0 || p.y > h) p.vy *= -1;
            if (s.pointer) {
                const dx = s.pointer.x - p.x;
                const dy = s.pointer.y - p.y;
                const d = Math.hypot(dx, dy);
                if (d < 200 && d > 1) {
                    p.x -= (dx / d) * 1.4;
                    p.y -= (dy / d) * 1.4;
                }
            }
        }
        for (let i = 0; i < s.dots.length; i++) {
            const a = s.dots[i];
            for (let j = i + 1; j < s.dots.length; j++) {
                const b = s.dots[j];
                const d = Math.hypot(a.x - b.x, a.y - b.y);
                if (d < link) {
                    ctx.strokeStyle = rgba(s.color, (1 - d / link) * 0.45);
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.stroke();
                }
            }
            ctx.fillStyle = rgba(s.color, 0.9);
            ctx.beginPath();
            ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2);
            ctx.fill();
        }
    },

    waves(ctx, w, h, s) {
        const lines = Math.round(8 + s.intensity * 22);
        const px = s.pointer ? s.pointer.x / w : 0.5;
        const py = s.pointer ? s.pointer.y / h : 0.5;
        ctx.clearRect(0, 0, w, h);
        ctx.lineWidth = 1.2;
        for (let i = 0; i < lines; i++) {
            const k = i / lines;
            ctx.strokeStyle = rgba(s.color, 0.08 + (1 - Math.abs(k - 0.5) * 2) * 0.45);
            ctx.beginPath();
            for (let x = 0; x <= w; x += 8) {
                const nx = x / w;
                const amp = h * (0.05 + 0.1 * s.intensity) * (1 + 0.8 * Math.exp(-Math.pow((nx - px) * 3, 2)) * (py - 0.2));
                const y =
                    h * (0.35 + k * 0.4) +
                    Math.sin(nx * 6 + s.t * 0.6 + k * 3) * amp * 0.6 +
                    Math.sin(nx * 13 - s.t * 0.9 + k * 7) * amp * 0.25;
                x === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
            }
            ctx.stroke();
        }
    },

    stars(ctx, w, h, s) {
        if (!s.stars) {
            const count = Math.round(200 + s.intensity * 500);
            s.stars = Array.from({ length: count }, () => ({ x: (Math.random() - 0.5) * w, y: (Math.random() - 0.5) * h, z: Math.random() * w }));
        }
        const cx = w / 2 + (s.pointer ? (s.pointer.x - w / 2) * 0.6 : 0);
        const cy = h / 2 + (s.pointer ? (s.pointer.y - h / 2) * 0.6 : 0);
        const speed = 1.5 + s.intensity * 7;
        ctx.fillStyle = rgba(s.bg, 0.35);
        ctx.fillRect(0, 0, w, h);
        for (const st of s.stars) {
            st.z -= speed;
            if (st.z <= 1) {
                st.x = (Math.random() - 0.5) * w;
                st.y = (Math.random() - 0.5) * h;
                st.z = w;
            }
            const k = 180 / st.z;
            const x = st.x * k + cx;
            const y = st.y * k + cy;
            if (x < 0 || x > w || y < 0 || y > h) continue;
            const size = Math.max(0.4, (1 - st.z / w) * 2.6);
            ctx.fillStyle = rgba(st.x > 0 === st.y > 0 ? s.color : s.ink, Math.min(1, 1.2 - st.z / w));
            ctx.fillRect(x, y, size, size);
        }
    },
};

/* ------------------------------------------------------------------ text effects */
function scramble(el, text, done) {
    const glyphs = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#%&*+=/<>';
    const total = 34;
    let frame = 0;
    const tick = () => {
        const progress = frame / total;
        el.textContent = [...text]
            .map((ch, i) => (ch === ' ' || i / text.length < progress ? ch : glyphs[(Math.random() * glyphs.length) | 0]))
            .join('');
        if (frame++ < total) requestAnimationFrame(() => setTimeout(tick, 28));
        else {
            el.textContent = text;
            done();
        }
    };
    tick();
}

function typewriter(el, text, done) {
    const caret = document.createElement('span');
    caret.className = 'fx-caret';
    const node = document.createTextNode('');
    el.textContent = '';
    el.append(node, caret);
    let i = 0;
    const step = () => {
        node.textContent = text.slice(0, ++i);
        if (i < text.length) setTimeout(step, 45 + Math.random() * 60);
        else done();
    };
    setTimeout(step, 250);
}

/* ------------------------------------------------------------------ component */
export default function heroFx({ effect = 'none', textEffect = 'none', intensity = 0.6, interactive = true } = {}) {
    let raf = null;
    let visible = true;
    let cleanup = [];

    return {
        init() {
            const root = this.$el;
            const still = reducedMotion();

            this.startText(still);

            if (interactive && !still && window.matchMedia('(pointer: fine)').matches) {
                const move = (e) => {
                    const r = root.getBoundingClientRect();
                    root.classList.add('fx-pointer');
                    root.style.setProperty('--mx', ((e.clientX - r.left) / r.width).toFixed(3));
                    root.style.setProperty('--my', ((e.clientY - r.top) / r.height).toFixed(3));
                    this.magnet(e, root);
                    if (this.state) this.state.pointer = { x: e.clientX - r.left, y: e.clientY - r.top };
                };
                const leave = () => {
                    root.classList.remove('fx-pointer');
                    if (this.state) this.state.pointer = null;
                    root.querySelectorAll('[data-magnetic]').forEach((b) => { b._pullX = b._pullY = 0; b.style.transform = ''; });
                };
                root.addEventListener('pointermove', move, { passive: true });
                root.addEventListener('pointerleave', leave);
                cleanup.push(() => root.removeEventListener('pointermove', move), () => root.removeEventListener('pointerleave', leave));
            } else if (effect === 'spotlight' && !still) {
                // Touch devices: let the spotlight drift on its own.
                this.drift(root);
            }

            const canvas = this.$refs.canvas;
            if (canvas && painters[effect]) this.startCanvas(canvas, still);
        },

        destroy() {
            cancelAnimationFrame(raf);
            cleanup.forEach((fn) => fn());
            cleanup = [];
        },

        startText(still) {
            const title = this.$refs.title;
            if (!title || !['scramble', 'typewriter'].includes(textEffect)) return;
            const text = title.dataset.text || title.textContent.trim();
            const ready = () => title.classList.add('fx-ready');

            if (still) {
                title.textContent = text;
                ready();
                return;
            }
            ready();
            // Arabic letters are joined, so scrambling individual glyphs breaks them: type instead.
            if (textEffect === 'scramble' && !/[؀-ۿ]/.test(text)) scramble(title, text, () => {});
            else typewriter(title, text, () => {});
        },

        magnet(e, root = this.$el) {
            root.querySelectorAll('[data-magnetic]').forEach((btn) => {
                // Measure from the resting position (minus the current pull) to avoid jitter.
                const r = btn.getBoundingClientRect();
                const cx = r.left + r.width / 2 - (btn._pullX || 0);
                const cy = r.top + r.height / 2 - (btn._pullY || 0);
                const dx = e.clientX - cx;
                const dy = e.clientY - cy;
                const near = Math.hypot(dx, dy) < Math.max(190, r.width);
                btn._pullX = near ? dx * 0.35 : 0;
                btn._pullY = near ? dy * 0.4 : 0;
                btn.style.transform = near ? `translate(${btn._pullX}px, ${btn._pullY}px)` : '';
            });
        },

        drift(root) {
            let t = 0;
            const loop = () => {
                t += 0.004;
                root.style.setProperty('--mx', (0.5 + Math.sin(t * 2) * 0.3).toFixed(3));
                root.style.setProperty('--my', (0.45 + Math.cos(t * 3) * 0.25).toFixed(3));
                if (visible) raf = requestAnimationFrame(loop);
            };
            this.watchVisibility(root, loop);
        },

        startCanvas(canvas, still) {
            const ctx = canvas.getContext('2d');
            const styles = getComputedStyle(this.$el);
            this.state = {
                t: 0,
                intensity,
                pointer: null,
                color: parseColor(styles.getPropertyValue('--fx')),
                ink: parseColor(styles.color, [240, 240, 240]),
                bg: parseColor(getComputedStyle(document.body).backgroundColor, [11, 11, 12]),
            };
            let w = 0;
            let h = 0;

            const resize = () => {
                const dpr = Math.min(2, window.devicePixelRatio || 1);
                w = canvas.clientWidth;
                h = canvas.clientHeight;
                canvas.width = w * dpr;
                canvas.height = h * dpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                this.state.dots = null;
                this.state.stars = null;
                if (still) painters[effect](ctx, w, h, this.state);
            };
            resize();
            const ro = new ResizeObserver(resize);
            ro.observe(canvas);
            cleanup.push(() => ro.disconnect());

            // Theme switches change the colours the canvas painted with.
            const mo = new MutationObserver(() => {
                const cs = getComputedStyle(this.$el);
                this.state.ink = parseColor(cs.color, this.state.ink);
                this.state.bg = parseColor(getComputedStyle(document.body).backgroundColor, this.state.bg);
                this.state.color = parseColor(cs.getPropertyValue('--fx'), this.state.color);
            });
            mo.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
            cleanup.push(() => mo.disconnect());

            if (still) return;

            const loop = () => {
                this.state.t += 0.016;
                painters[effect](ctx, w, h, this.state);
                if (visible) raf = requestAnimationFrame(loop);
            };
            this.watchVisibility(this.$el, loop);
        },

        watchVisibility(el, loop) {
            const update = (onScreen) => {
                const next = onScreen && document.visibilityState === 'visible';
                if (next && !visible) {
                    visible = true;
                    raf = requestAnimationFrame(loop);
                } else if (!next) {
                    visible = false;
                    cancelAnimationFrame(raf);
                }
            };
            let onScreen = true;
            const io = new IntersectionObserver(([entry]) => update((onScreen = entry.isIntersecting)));
            io.observe(el);
            const vis = () => update(onScreen);
            document.addEventListener('visibilitychange', vis);
            cleanup.push(() => io.disconnect(), () => document.removeEventListener('visibilitychange', vis));
            raf = requestAnimationFrame(loop);
        },
    };
}
