import http, { errorMessage } from './http';

const meta = (name) => document.querySelector(`meta[name="${name}"]`)?.content;

/**
 * Upload one file to the media library in chunks, so large videos pass the server's
 * upload_max_filesize. Videos get a poster frame + dimensions captured in the browser.
 *
 * @returns {Promise<object>} the created media JSON
 */
export async function uploadFile(file, { folder = null, onProgress = () => {} } = {}) {
    const chunkSize = Number(meta('upload-chunk-size')) || 2 * 1024 * 1024;
    const total = Math.max(1, Math.ceil(file.size / chunkSize));
    const uploadId = `${Date.now().toString(36)}${Math.random().toString(36).slice(2, 10)}`;
    const isVideo = file.type.startsWith('video/');
    const videoInfo = isVideo ? await captureVideoInfo(file).catch(() => null) : null;

    let response;
    for (let index = 0; index < total; index++) {
        const form = new FormData();
        form.append('upload_id', uploadId);
        form.append('chunk_index', index);
        form.append('total_chunks', total);
        form.append('file_name', file.name);
        form.append('chunk', file.slice(index * chunkSize, (index + 1) * chunkSize), file.name);
        if (folder) form.append('folder', folder);

        if (index === total - 1 && videoInfo) {
            form.append('width', videoInfo.width);
            form.append('height', videoInfo.height);
            form.append('duration', videoInfo.duration);
            if (videoInfo.poster) form.append('poster', videoInfo.poster, 'poster.jpg');
        }

        response = await retry(() =>
            http.post(meta('media-upload-url'), form, {
                onUploadProgress: (e) => {
                    const done = index * chunkSize + (e.loaded || 0);
                    onProgress(Math.min(99, Math.round((done / file.size) * 100)));
                },
            }),
        );
    }

    onProgress(100);
    return response.data.media;
}

async function retry(fn, attempts = 3) {
    for (let i = 1; ; i++) {
        try {
            return await fn();
        } catch (error) {
            const status = error?.response?.status;
            if (i >= attempts || (status && status < 500 && status !== 408 && status !== 429)) throw error;
            await new Promise((r) => setTimeout(r, 800 * i));
        }
    }
}

/** Read width/height/duration and grab a JPEG frame (~1s in) as the poster. */
function captureVideoInfo(file) {
    return new Promise((resolve, reject) => {
        const url = URL.createObjectURL(file);
        const video = document.createElement('video');
        video.preload = 'metadata';
        video.muted = true;
        video.playsInline = true;
        video.src = url;

        const cleanup = () => URL.revokeObjectURL(url);
        const timer = setTimeout(() => {
            cleanup();
            reject(new Error('timeout'));
        }, 15000);

        video.addEventListener('error', () => {
            clearTimeout(timer);
            cleanup();
            reject(new Error('unreadable'));
        });

        video.addEventListener('loadedmetadata', () => {
            video.currentTime = Math.min(1, (video.duration || 2) / 3);
        });

        video.addEventListener('seeked', () => {
            const canvas = document.createElement('canvas');
            const scale = Math.min(1, 1920 / (video.videoWidth || 1920));
            canvas.width = Math.round((video.videoWidth || 1280) * scale);
            canvas.height = Math.round((video.videoHeight || 720) * scale);
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(
                (poster) => {
                    clearTimeout(timer);
                    cleanup();
                    resolve({
                        width: video.videoWidth,
                        height: video.videoHeight,
                        duration: Math.round(video.duration || 0),
                        poster,
                    });
                },
                'image/jpeg',
                0.86,
            );
        });
    });
}

/**
 * Alpine component: drag & drop / picker upload queue with per-file progress.
 * Dispatches `media-uploaded` with the media JSON for every finished file.
 */
export function uploadQueue({ folder = null, accept = 'image/*,video/*' } = {}) {
    return {
        queue: [],
        dragging: false,
        accept,
        running: false,

        get busy() {
            return this.queue.some((item) => item.status === 'uploading' || item.status === 'waiting');
        },

        pick() {
            this.$refs.fileInput.click();
        },

        onDrop(event) {
            this.dragging = false;
            this.add(event.dataTransfer.files);
        },

        add(fileList) {
            Array.from(fileList || []).forEach((file) =>
                this.queue.push({ id: Math.random(), file, name: file.name, progress: 0, status: 'waiting', error: null }),
            );
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
            this.run();
        },

        async run() {
            if (this.running) return;
            this.running = true;
            let item;
            while ((item = this.queue.find((q) => q.status === 'waiting'))) {
                item.status = 'uploading';
                try {
                    const media = await uploadFile(item.file, { folder, onProgress: (p) => (item.progress = p) });
                    item.status = 'done';
                    this.$dispatch('media-uploaded', media);
                } catch (error) {
                    item.status = 'error';
                    item.error = errorMessage(error);
                }
            }
            this.running = false;
            setTimeout(() => (this.queue = this.queue.filter((q) => q.status !== 'done')), 1500);
        },
    };
}
