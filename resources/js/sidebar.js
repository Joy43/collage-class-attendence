/* Off-canvas sidebar on small screens. */
const sidebar = document.getElementById('sidebar');
const hide = () => sidebar?.classList.add('-translate-x-full');

document.getElementById('menu-btn')?.addEventListener('click', () => sidebar.classList.toggle('-translate-x-full'));
document.addEventListener('keydown', (e) => e.key === 'Escape' && hide());
