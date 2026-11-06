/* Notifications page: mark unread items as read after they've been on screen for 5 seconds. */
const base = document.querySelector('meta[name="notifications-url"]').content;
const token = document.querySelector('meta[name="csrf-token"]').content;

document.querySelectorAll('[data-unread-id]').forEach((row) => {
    setTimeout(() => {
        fetch(`${base}/${row.dataset.unreadId}/read`, {
            method: 'PATCH',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token },
        });
        row.classList.remove(...row.dataset.unreadBg.split(' '));
        row.querySelector('[data-unread-dot]')?.remove();
    }, 5000);
});
