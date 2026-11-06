/* AttApp — shared front-end behaviour (theme, toasts, modals, notifications). */
(function () {
  const $ = (id) => document.getElementById(id);
  const APP = window.APP || {};

  /* ─── Theme ─── */
  function syncThemeIcon() {
    const el = $('theme-icon');
    if (!el || !APP.icons) return;
    el.innerHTML = document.documentElement.classList.contains('dark') ? APP.icons.sun : APP.icons.moon;
  }
  window.toggleTheme = function () {
    const dark = document.documentElement.classList.toggle('dark');
    try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (e) {}
    syncThemeIcon();
  };
  syncThemeIcon();

  /* ─── Sidebar (mobile) ─── */
  window.toggleSidebar = function () { $('sidebar').classList.toggle('-translate-x-full'); };
  function closeSidebar() { const s = $('sidebar'); if (s) s.classList.add('-translate-x-full'); }

  /* ─── Toasts ─── */
  const TOAST_BORDER = { success: 'border-l-present', error: 'border-l-absent', warning: 'border-l-late', info: 'border-l-primary' };
  window.showToast = function (title, msg, type = 'success', duration = 3500) {
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const t = document.createElement('div');
    t.className = 'flex min-w-[280px] max-w-[360px] animate-slide-toast items-start gap-2.5 rounded-[10px] border border-l-[3px] border-line bg-surface px-4 py-3 shadow-lg transition-opacity duration-300 '
      + (TOAST_BORDER[type] || TOAST_BORDER.info);
    t.innerHTML = `
      <span class="mt-px shrink-0 text-base">${icons[type] || ''}</span>
      <div><div class="text-[.85rem] font-bold">${title}</div><div class="text-[.8rem] text-muted">${msg}</div></div>
      <button type="button" class="ml-auto cursor-pointer border-0 bg-transparent text-[.9rem] text-faint" onclick="this.closest('div.flex').remove()">✕</button>`;
    $('toast-container').appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, duration);
  };

  /* ─── Modals: any element with [data-modal] ─── */
  window.openModal  = (id) => { const m = $(id); m.classList.remove('hidden'); m.classList.add('flex'); };
  window.closeModal = (id) => { const m = $(id); m.classList.add('hidden');   m.classList.remove('flex'); };
  const closeAllModals = () => document.querySelectorAll('[data-modal]').forEach((m) => closeModal(m.id));
  document.querySelectorAll('[data-modal]').forEach((m) =>
    m.addEventListener('click', (e) => { if (e.target === m) closeModal(m.id); }));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') { closeAllModals(); closeNotifPanel(); closeSidebar(); }
  });

  /* ═══════════ Notifications dropdown ═══════════ */
  const NOTIF_API = APP.notifApi || '/api/notifications.php';
  let notifOpen = false;

  const TYPE = {
    create: { icon: '➕', text: 'text-present', soft: 'bg-present-soft', ring: 'border-present/20' },
    update: { icon: '✏️', text: 'text-primary', soft: 'bg-primary-soft', ring: 'border-primary/20' },
    delete: { icon: '🗑️', text: 'text-absent',  soft: 'bg-absent-soft',  ring: 'border-absent/20' },
  };

  window.toggleNotifPanel = function () {
    notifOpen = !notifOpen;
    $('notif-panel').classList.toggle('hidden', !notifOpen);
    if (notifOpen) fetchNotifications();
  };
  function closeNotifPanel() {
    notifOpen = false;
    const p = $('notif-panel'); if (p) p.classList.add('hidden');
  }
  document.addEventListener('click', (e) => {
    const wrap = $('notif-wrap');
    if (wrap && !wrap.contains(e.target)) closeNotifPanel();
  });

  function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
  }

  function setBadge(el, unread, shownClass) {
    if (!el) return;
    if (unread > 0) {
      el.textContent = unread > 99 ? '99+' : unread;
      el.classList.remove('hidden'); el.classList.add(shownClass);
    } else {
      el.classList.add('hidden'); el.classList.remove(shownClass);
    }
  }

  function renderNotifications(data) {
    const list = $('notif-list');
    const notifs = data.notifications || [];
    const unread = data.unread || 0;

    setBadge($('notif-badge'), unread, 'inline-flex');
    setBadge($('sidebar-notif-badge'), unread, 'inline-flex');
    const dot = $('notif-dot'); if (dot) dot.classList.toggle('hidden', unread === 0);

    if (!notifs.length) {
      list.innerHTML = '<div class="p-10 text-center text-[.85rem] text-muted">🔕 No notifications yet</div>';
      return;
    }

    list.innerHTML = notifs.map((n) => {
      const c = TYPE[n.type] || TYPE.create;
      const isUnread = n.is_read == 0;
      return `<div id="notif-item-${n.id}" data-unread="${isUnread ? 1 : 0}" onclick="markRead(${n.id})"
          class="flex cursor-pointer items-start gap-3 border-b border-line px-4 py-3 transition-all duration-200 ${isUnread ? c.soft : ''}">
        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] border text-base ${c.soft} ${c.ring}">${c.icon}</div>
        <div class="min-w-0 flex-grow overflow-hidden">
          <div class="mb-[3px] text-[.82rem] font-bold ${c.text}">${n.title}</div>
          <div class="break-words text-[.78rem] leading-snug text-muted">${n.message}</div>
          <div class="mt-[5px] text-[.7rem] text-faint">${timeAgo(n.created_at)}</div>
        </div>
        <button type="button" title="Dismiss" onclick="event.stopPropagation();dismissNotif(${n.id})"
          class="shrink-0 cursor-pointer rounded border-0 bg-transparent px-[5px] py-0.5 text-[.85rem] leading-none text-faint transition-colors hover:text-absent">✕</button>
      </div>`;
    }).join('');
  }

  function fetchNotifications() {
    fetch(NOTIF_API + '?action=fetch').then((r) => r.json()).then(renderNotifications).catch(() => {});
  }
  window.markRead = (id) => fetch(NOTIF_API + '?action=mark_read&id=' + id).then(fetchNotifications);
  window.markAllRead = () => fetch(NOTIF_API + '?action=mark_all_read', { method: 'POST' }).then(() => {
    fetchNotifications();
    showToast('Notifications', 'All notifications marked as read.', 'success', 2500);
  });
  window.dismissNotif = function (id) {
    const item = $('notif-item-' + id);
    if (item) item.classList.add('max-h-0', 'overflow-hidden', 'border-0', 'py-0', 'opacity-0');
    fetch(NOTIF_API + '?action=delete&id=' + id).then(() => setTimeout(fetchNotifications, 300));
  };
  window.clearAllRead = function () {
    const reqs = [];
    document.querySelectorAll('#notif-list [data-unread="0"]').forEach((item) => {
      reqs.push(fetch(NOTIF_API + '?action=delete&id=' + item.id.replace('notif-item-', '')));
      item.remove();
    });
    Promise.all(reqs).then(() => {
      fetchNotifications();
      showToast('Cleared', 'Read notifications removed.', 'info', 2500);
    });
  };

  fetchNotifications();
  setInterval(fetchNotifications, 30000);
})();
