/* Top-bar notification bell: polls the JSON endpoints of NotificationController. */
import { http } from './http.js';
import { showToast } from './toast.js';

const base = document.querySelector('meta[name="notifications-url"]')?.content;
const $ = (id) => document.getElementById(id);

if (base) init();

function init() {
    const panel = $('notif-panel');

    $('notif-btn').addEventListener('click', () => {
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) refresh();
    });
    document.addEventListener('click', (e) => {
        if (!$('notif-wrap').contains(e.target)) panel.classList.add('hidden');
    });
    document.addEventListener('keydown', (e) => e.key === 'Escape' && panel.classList.add('hidden'));

    $('notif-mark-all').addEventListener('click', async () => {
        await http(`${base}/read-all`, { method: 'PATCH' });
        showToast('Notifications', 'All notifications marked as read.', 'success', 2500);
        refresh();
    });
    $('notif-clear-read').addEventListener('click', async () => {
        await http(`${base}/read`, { method: 'DELETE' });
        showToast('Cleared', 'Read notifications removed.', 'info', 2500);
        refresh();
    });
    $('notif-list').addEventListener('click', async (e) => {
        const item = e.target.closest('[data-id]');
        if (!item) return;

        if (e.target.closest('[data-dismiss]')) {
            item.classList.add('max-h-0', 'overflow-hidden', 'border-0', 'py-0', 'opacity-0');
            await http(`${base}/${item.dataset.id}`, { method: 'DELETE' });
        } else {
            await http(`${base}/${item.dataset.id}/read`, { method: 'PATCH' });
        }
        refresh();
    });

    refresh();
    setInterval(refresh, 30_000);
}

const TYPES = {
    create: { icon: '➕', text: 'text-present', soft: 'bg-present-soft', ring: 'border-present/20' },
    update: { icon: '✏️', text: 'text-primary', soft: 'bg-primary-soft', ring: 'border-primary/20' },
    delete: { icon: '🗑️', text: 'text-absent', soft: 'bg-absent-soft', ring: 'border-absent/20' },
};

const escapeHtml = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

function timeAgo(iso) {
    const seconds = Math.floor((Date.now() - new Date(iso)) / 1000);
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
    return `${Math.floor(seconds / 86400)}d ago`;
}

function setBadge(el, unread) {
    if (!el) return;
    el.textContent = unread > 99 ? '99+' : unread;
    el.classList.toggle('hidden', unread === 0);
    el.classList.toggle('inline-flex', unread > 0);
}

function render({ notifications, unread }) {
    setBadge($('notif-badge'), unread);
    setBadge($('sidebar-notif-badge'), unread);
    $('notif-dot').classList.toggle('hidden', unread === 0);

    $('notif-list').innerHTML = notifications.length
        ? notifications.map((n) => {
            const t = TYPES[n.type] ?? TYPES.create;
            return `
            <div data-id="${n.id}" class="flex cursor-pointer items-start gap-3 border-b border-line px-4 py-3 transition-all duration-200 ${n.is_read ? '' : t.soft}">
                <div class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-[10px] border text-base ${t.soft} ${t.ring}">${t.icon}</div>
                <div class="min-w-0 grow overflow-hidden">
                    <div class="mb-[3px] text-[.82rem] font-bold ${t.text}">${escapeHtml(n.title)}</div>
                    <div class="break-words text-[.78rem] leading-snug text-muted">${escapeHtml(n.message)}</div>
                    <div class="mt-[5px] text-[.7rem] text-faint">${timeAgo(n.created_at)}</div>
                </div>
                <button type="button" data-dismiss title="Dismiss" class="shrink-0 rounded px-[5px] py-0.5 text-[.85rem] leading-none text-faint transition-colors hover:text-absent">✕</button>
            </div>`;
        }).join('')
        : '<div class="p-10 text-center text-[.85rem] text-muted">🔕 No notifications yet</div>';
}

function refresh() {
    return http(base).then(render).catch(() => {});
}
