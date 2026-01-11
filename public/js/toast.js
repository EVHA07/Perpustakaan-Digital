// Toast Notification System
class Toast {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        if (!document.getElementById('toast-container')) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-50 space-y-2';
            document.body.appendChild(this.container);
        }
    }

    show(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success'
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

        toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-all duration-300 -translate-y-full opacity-0`;
        toast.innerHTML = `
            ${icon}
            <span class="font-medium">${message}</span>
        `;

        this.container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('-translate-y-full', 'opacity-0');
        }, 10);

        // Auto dismiss
        setTimeout(() => {
            this.dismiss(toast);
        }, 3000);

        return toast;
    }

    dismiss(toast) {
        toast.classList.add('-translate-y-full', 'opacity-0');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 300);
    }

    success(message) {
        return this.show(message, 'success');
    }

    error(message) {
        return this.show(message, 'error');
    }
}

// Global instance
window.toast = new Toast();

// Auto-show flash messages
document.addEventListener('DOMContentLoaded', () => {
    const successMessages = document.querySelectorAll('[data-flash-success]');
    successMessages.forEach(el => {
        const message = el.dataset.flashSuccess;
        window.toast.success(message);
        el.remove();
    });
});
