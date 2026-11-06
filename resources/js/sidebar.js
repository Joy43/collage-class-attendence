/* Off-canvas sidebar on small screens with backdrop overlay. */
const sidebar = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebar-backdrop');
const menuBtn = document.getElementById('menu-btn');
const closeBtn = document.getElementById('sidebar-close-btn');

function showSidebar() {
    sidebar?.classList.remove('-translate-x-full');
    backdrop?.classList.remove('hidden');
}

function hideSidebar() {
    sidebar?.classList.add('-translate-x-full');
    backdrop?.classList.add('hidden');
}

menuBtn?.addEventListener('click', () => {
    if (sidebar?.classList.contains('-translate-x-full')) {
        showSidebar();
    } else {
        hideSidebar();
    }
});

closeBtn?.addEventListener('click', hideSidebar);
backdrop?.addEventListener('click', hideSidebar);
document.addEventListener('keydown', (e) => e.key === 'Escape' && hideSidebar());
