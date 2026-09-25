/** Global toast notifications: Alpine.store('toast').push('Saved', 'success') or window.toast(...) */
export default function registerToast(Alpine) {
    Alpine.store('toast', {
        items: [],
        push(message, type = 'success', timeout = 3800) {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.dismiss(id), timeout);
        },
        dismiss(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    });

    window.toast = (message, type) => Alpine.store('toast').push(message, type);
}
