const BORDER = { success: 'border-l-present', error: 'border-l-absent', warning: 'border-l-late', info: 'border-l-primary' };
const ICON = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };

/** Small transient message in the bottom-right corner. */
export function showToast(title, message, type = 'success', duration = 3500) {
    const toast = document.createElement('div');
    toast.className = 'animate-slide-toast flex min-w-[280px] max-w-[360px] items-start gap-2.5 rounded-[10px] border border-l-[3px] border-line bg-surface px-4 py-3 shadow-lg transition-opacity duration-300 '
        + (BORDER[type] ?? BORDER.info);

    const body = document.createElement('div');
    body.innerHTML = '<div class="text-[.85rem] font-bold"></div><div class="text-[.8rem] text-muted"></div>';
    body.children[0].textContent = title;
    body.children[1].textContent = message;

    const icon = Object.assign(document.createElement('span'), { className: 'mt-px shrink-0 text-base', textContent: ICON[type] ?? '' });
    const close = Object.assign(document.createElement('button'), { type: 'button', className: 'ml-auto text-[.9rem] text-faint', textContent: '✕' });
    close.addEventListener('click', () => toast.remove());

    toast.append(icon, body, close);
    document.getElementById('toast-container').append(toast);

    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, duration);
}
